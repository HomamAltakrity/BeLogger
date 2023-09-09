<?php
    require_once('config.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    $_loggedUser = $_SESSION['user_id'];
    $sqlQuery = 'SELECT id, name, isAuthor, image FROM user WHERE id = :userId';
    $stmt = $db->prepare($sqlQuery);
    $stmt->bindParam(':userId', $_loggedUser, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        $userId = $userData['id'];
        $userName = $userData['name'];
        $userImage = $userData['image'];
        $isAuthor = $userData['isAuthor'];
    }
?>