<?php
require_once('config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* This code block is checking if the form has been submitted. If the form has been submitted, it
retrieves the values from the form fields (`['content']`, `['user_id']`,
`['post_id']`) and assigns them to variables (``, ``, ``). */
if (isset($_POST['submit'])) {
    $content = $_POST['content'];
    $authorID = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    $currentDate = date("Y-m-d H:i:s");
    $isEdited = 0;

    $sqlComment = "INSERT INTO comments (`user_id`, `post_id`, `content`, `publish_date`, `is_edited`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sqlComment);
    $result = $stmt->execute([$authorID, $post_id, $content, $currentDate, $isEdited]);

    if ($result) {
        header("Location: ../../index.php");
        exit();
    } else {
        echo "Error adding new comment.";
    }
}
?>
