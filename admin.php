<?php
require_once('src/controllers/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: src/controllers/login.php');
    exit();
}

$sql = "SELECT * FROM user";
$stmt = $db->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* This code block is responsible for updating and deleting user records in the database based on the
form submissions. */
if (isset($_POST['submit'])) {
    $userId = $_POST['user_id'];
    $isAuthor = $_POST['is_author'];
    $sql = "UPDATE user SET isAuthor = :isAuthor WHERE id = :userId";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':isAuthor', $isAuthor);
    $stmt->bindParam(':userId', $userId);
    $result = $stmt->execute();
    if ($result) {
        header('Location: admin.php');
    } else {
        $errorInfo = $stmt->errorInfo();
        echo 'Error updating user: ' . $errorInfo[2];
    }
}

if (isset($_POST['delete_user'])) {
    $userIdToDelete = $_POST['user_id'];
    try {
        $sqlDeleteUser = "DELETE FROM user WHERE id = ?";
        $stmtDeleteUser = $db->prepare($sqlDeleteUser);
        $stmtDeleteUser->execute([$userIdToDelete]);
        if ($stmtDeleteUser->rowCount() > 0) {
            header('Location: admin.php');
        } else {
            echo "User with ID $userIdToDelete was not found.";
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>G&S</title>
    <link rel="stylesheet" type="text/css" href="src/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="src/css/style.css">
</head>

<body style="display:block">

    <div class="content">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Is Author</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <?php echo $user['id']; ?>
                        </td>
                        <td>
                            <?php echo $user['name']; ?>
                        </td>
                        <td>
                            <?php echo $user['email']; ?>
                        </td>
                        <td>
                            <?php echo $user['isAuthor']; ?>
                        </td>
                        <td>
                            <form method="post" action="admin.php" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="is_author" class="form-control">
                                    <option value="normal" <?php echo ($user['isAuthor'] === 'normal') ? 'selected' : ''; ?>>
                                        Normal</option>
                                    <option value="super" <?php echo ($user['isAuthor'] === 'super') ? 'selected' : ''; ?>>
                                        Super</option>
                                </select>
                                <button type="submit" name="submit" class="btn btn-primary">Update</button>
                            </form>

                            <form method="post" action="admin.php" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this user?')">Delete
                                    User</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include('src/controllers/footer.php'); ?>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>