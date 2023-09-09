<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $_loggedUserId = $_SESSION['user_id'];
    $_loggedUserName = $_SESSION['user_name'];
    $_loggedUserImage = $_SESSION['user_image'];
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="position:fixed;width:100%">
        <a class="navbar-brand" href="#">G&S</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <?php if ($isAuthor === 'super'): ?>
                <li class="nav-item ">
                    <a href="src/controllers/add_post_page.php" class="btn px-0" style="color:white;">Add Post&nbsp;</a>
                </li>
                <?php endif; ?>
                <li class="nav-item ">
                    <a href="src/controllers/logout.php" class="btn px-0" style="color:white;">Logout</a>
                </li>
            </ul>
        </div>
        <div class="d-flex ml-auto align-items-center">
            <span class="mr-3" style="color:white"><?php echo $_loggedUserName; ?></span>
            <a href="profile.php"><img src="images/profiles/<?php echo $_loggedUserImage; ?>" alt="User Image" class="rounded-circle user-image" width="50px" height="50px"></a>
        </div>
    </nav>
</body>
</html>
