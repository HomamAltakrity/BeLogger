<?php
require_once('config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* This code block is responsible for handling the form submission when the user clicks the "Post"
button. */
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $authorID = $_SESSION['user_id'];
    $currentDate = date("Y-m-d H:i:s");
    $isEdited = 0;

   /* This code block is retrieving the maximum value of the `id` column from the `posts` table in the
   database. It then assigns the maximum value to the variable ``. The next post ID is
   calculated by adding 1 to the maximum post ID. This is done to ensure that each post has a unique
   ID when it is inserted into the database. */
    $sqlMaxPostID = "SELECT MAX(id) FROM posts";
    $stmtMaxPostID = $db->prepare($sqlMaxPostID);
    $stmtMaxPostID->execute();
    $maxPostID = $stmtMaxPostID->fetchColumn();
    $nextPostID = $maxPostID + 1;

    $imageFilename = $nextPostID . '_post_' . $_SESSION['user_name'] . '.jpg';
    $folder = "../../images/posts/" . $imageFilename;

    if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $folder)) {
        $sqlPost = "INSERT INTO posts (`title`, `content`, `image`, `publish_date`, `is_edited`, `author_id`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sqlPost);
        $result = $stmt->execute([$title, $content, $imageFilename, $currentDate, $isEdited, $authorID]);

        if ($result) {
            echo '<script>alert("Post Added Successfully");</script>';
            header("Location: ../../index.php");
        } else {
            echo '<script>alert("Post Not Added Successfully");</script>';
        }
    } else {
        echo '<script>alert("Failed to upload image!");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/formStyle.css" />
</head>

<body>
    <form action="add_post.php" method="post" enctype="multipart/form-data">
        <div class="container">
            <div class="row">
                <hr class="mb-3">
                <div class="input-group">

                    <label class="fieldLabel" for="title">Title:</label>
                    <input type="text" name="title" id="title" placeholder="Enter Your Post Title" required />

                    <label class="fieldLabel" for="content">Content:</label>
                    <textarea class="textarea" name="content" id="content" placeholder="Enter Your Post Content"
                        required></textarea>

                    <label class="fieldLabel" for="uploadfile">Upload File:</label>
                    <input type="file" name="uploadfile" value="" accept=".jpg, .jpeg" />

                    <br />
                    <hr class="mb-3">
                    <div class="customBtn">
                        <input class="red" type="submit" name="submit" value="Post" />
                    </div>

                </div>

            </div>
        </div>
    </form>
</body>

</html>