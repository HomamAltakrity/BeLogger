<?php
require_once('config.php');
session_start();

/* This code is checking if the `delete_comment` parameter is set in the `` array and if the
`user_id` is set in the `` array. If both conditions are true, it proceeds to delete a
comment from the database. */
if (isset($_POST['delete_comment']) && isset($_SESSION['user_id'])) {
    $commentId = $_POST['comment_id'];
    $userId = $_SESSION['user_id'];

    // Check if the user is the owner of the comment or the owner of the post
    $sqlCheckOwnership = "SELECT c.user_id AS comment_owner, p.author_id AS post_owner
                          FROM comments c
                          INNER JOIN posts p ON c.post_id = p.id
                          WHERE c.id = ?";
    $stmtCheckOwnership = $db->prepare($sqlCheckOwnership);
    $stmtCheckOwnership->execute([$commentId]);
    $ownershipData = $stmtCheckOwnership->fetch(PDO::FETCH_ASSOC);

    if ($ownershipData['comment_owner'] == $userId || $ownershipData['post_owner'] == $userId) {
        $sqlDeleteComment = "DELETE FROM comments WHERE id = ?";
        $stmtDeleteComment = $db->prepare($sqlDeleteComment);
        $stmtDeleteComment->execute([$commentId]);
        header('Location: ../../index.php'); 
        exit();
    } else {
        header('Location: ../../index.php');
    }
} else {
    echo 'Invalid request';
}
?>
