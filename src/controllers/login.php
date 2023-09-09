<?php
require_once('config.php');
$alertMessage = null;
$showAlert = false; 
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if ($email == 'admin@gmail.com' && $password == '123456') {
        header('Location: ../../admin.php');
    } else {
    $sqlGetUserId = "SELECT id, name, Image FROM user WHERE email = :email"; 
    $stmt = $db->prepare($sqlGetUserId);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR); 
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $id = $user['id'];
        $sqlGetUserHash = "SELECT Hash FROM credentials WHERE user_id = :user_id";
        $stmtGetUserHash = $db->prepare($sqlGetUserHash); 
        $stmtGetUserHash->bindParam(':user_id', $id, PDO::PARAM_INT); 
        $stmtGetUserHash->execute();
        $hash = $stmtGetUserHash->fetch(PDO::FETCH_ASSOC);
        if ($hash) {
            $UserHash = $hash['Hash'];
            $status = password_verify($password, $UserHash);
            if ($status) {
                session_start();
                $_SESSION['user_id'] = $id; 
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_image'] = $user['Image'];
                $alertMessage = null;
                header('Location: ../../index.php'); 
                exit(); 
            } else {
                $showAlert = true;
                $alertMessage = "Invalid email or password.";
            }
        }
    } else {
        $showAlert = true;
        $alertMessage = "Invalid email or password.";
    }
    }
}

?>
<!DOCTYPE html >
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/formStyle.css" />
</head>
<body>
        <div class="form-container">
            <form action="login.php" method="post">
                <?php if (isset($showAlert)) { ?>
                    <p><?php echo $alertMessage; ?></p>
                <?php } ?>
                <h1>Login</h1>
                <hr class="mb-3">
                <div class="input-group">                        
                    <label class="fieldLabel text-left" for="email">E-mail:</label>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email" required />

                    <label class="fieldLabel text-left" for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter Your Password" required />
                    <a href="register.php">Don't Have an Account?</a>
                    <br />
                    <hr class="mb-3">
                
                    <input class="red" type="submit" name="submit" value="Sign In" />
                </div>
            </form>
        </div>
</body>
</html>
