<?php
require_once('config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
/* This code block is retrieving the logged-in user's ID and name from the session. It then prepares
and executes a SQL query to fetch all the posts from the database, along with the author's name and
image. The fetched posts are stored in the `` variable as an associative array. */

$loggedInUserID = $_SESSION['user_id'];
$loggedInUserName = $_SESSION['user_name'];
$sqlQuery = 'SELECT p.id, p.title, p.content, p.image, p.publish_date, p.is_edited, p.author_id, 
            u.name AS author_name,
            u.image AS author_image 
            FROM posts p JOIN user u ON p.author_id = u.id';
$stmt = $db->prepare($sqlQuery);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* This code block is responsible for deleting a post and fetching the posts from the database. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $postIdToDelete = $_POST['delete_post'];
    $sqlDeleteQuery = 'DELETE FROM posts WHERE id = :post_id AND author_id = :user_id';
    $deleteStmt = $db->prepare($sqlDeleteQuery);
    $deleteStmt->bindParam(':post_id', $postIdToDelete);
    $deleteStmt->bindParam(':user_id', $loggedInUserID);

    if ($deleteStmt->execute()) {
        header("Location: ../../index.php");
    } else {
        echo '<script>alert("Failed to delete the post.");</script>';
    }
}

$sortOption = isset($sortOption) ? $sortOption : 'date_desc';
if ($sortOption === 'date_asc') {
    $sqlOrderBy = 'ORDER BY p.publish_date ASC';
} else {
    $sqlOrderBy = 'ORDER BY p.publish_date DESC';
}
$sqlQuery = 'SELECT p.id, p.title, p.content, p.image, p.publish_date, p.is_edited, p.author_id, 
                u.name AS author_name,
                u.image AS author_image 
                FROM posts p JOIN user u ON p.author_id = u.id
                ' . $sqlOrderBy;

$stmt = $db->prepare($sqlQuery);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($posts as $post): ?>
    <div class="post">
        <div class="post-header">
            <div class="user-info">
                <img src="images/profiles/<?php echo $post['author_image']; ?>" alt="Profile Image" class="profile-image">
                <div class="user-details">
                    <span class="user-name">
                        <?php echo $post['author_name']; ?>
                    </span>
                    <span class="publish-date">
                        <?php echo $post['publish_date']; ?>
                    </span>
                </div>
            </div>


            <?php if ($post['author_id'] == $loggedInUserID): ?>
                <div class="post-author">
                    <form class="text-right" method="post" action="src/controllers/posts.php">
                        <input type="hidden" name="delete_post" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="delete-post-button"
                            onclick="return confirm('Are you sure you want to delete this post?');">X</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <div class="post-content">
            <p>
                <?php echo $post['content']; ?>
            </p>
        </div>
        <div class="post-image">
            <img src="images/posts/<?php echo $post['image']; ?>" alt="Post Image">
        </div>
        <div class="post-comments">

            <?php
            $sqlComments = "SELECT c.*, u.name AS user_name, u.Image AS user_image, p.author_id
                            FROM comments c
                            JOIN user u ON c.user_id = u.id
                            JOIN posts p ON c.post_id = p.id
                            WHERE c.post_id = :post_id";
            $stmtComments = $db->prepare($sqlComments);
            $stmtComments->bindParam(':post_id', $post['id']);
            $stmtComments->execute();
            $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

            foreach ($comments as $comment) {
                echo '<div class="comment mb-4 d-flex align-items-center">';
                echo '<img src="images/profiles/' . $comment['user_image'] . '" alt="User Image" class="profile-image" width="5px" height="5px">';
                echo '<span class="user-name" style="font-size:15px;">' . $comment['user_name'] . ': &nbsp;</span>';
                echo '<span class="comment-content">' . $comment['content'] . '</span>';

                if ($comment['user_id'] == $_SESSION['user_id'] || $comment['author_id'] == $_SESSION['user_id']) {
                    echo '<form class="text-right" action="src/controllers/delete_comment.php" method="post" >';
                    echo '<input type="hidden" name="comment_id" value="' . $comment['id'] . '">';
                    echo '<button type="submit" id="delete_comment" name="delete_comment" class="delete-comment-button ml-2">X</button>';
                    echo '</form>';
                }
                echo '</div>';
            }
            ?>



        </div>
        <div class="comment-form">
            <form class="commentForm" action="src/controllers/add_comment.php" method="post">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <div class="comment-input-container">
                    <input type="text" name="content" placeholder="Add a comment" class="comment-input">
                    <input type="submit" name="submit" value="Comment" class="comment-button">
                </div>
            </form>
        </div>


    </div>
    </div>
<?php endforeach; ?>