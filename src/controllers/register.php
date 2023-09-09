<?php
require_once('config.php');
?>
<!DOCTYPE html >
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/formStyle.css" />
</head>
<div>
<?php
$alertMessage = null;
/* This code block is handling the form submission when the user clicks the "Register" button. It
checks if the form has been submitted by checking if the 'submit' parameter is set in the 
array. */
if (isset($_POST['submit'])) {
    $name = strtolower($_POST['name']);
    $email = strtolower($_POST['email']);
    $Fpassword = $_POST['Fpassword'];
    $Spassword = $_POST['Spassword'];
    $birthdate = $_POST['date'];
    $gender = $_POST['gender'];
    $isAuthor = $_POST['author'];
    $showAlert = false; 

    if ($Fpassword !== $Spassword || strlen($Fpassword) < 4) {
        $showAlert = true;
        $alertMessage = "Invalid password. Passwords must match and have a minimum length of 4 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $showAlert = true;
        $alertMessage = "Invalid email address. Please enter a valid email.";
    } elseif (!ctype_alpha(str_replace(' ', '', $name))) {
        $showAlert = true;
        $alertMessage = "Invalid name. Name should contain only letters.";
    } else {
        $hash = password_hash($Fpassword, PASSWORD_DEFAULT);
        $sqlUser = "INSERT INTO user (`name`, `email`, `gender`, `birthdate`, `isAuthor`, `Image`) VALUES (?,?,?,?,?,?)";
        $startUserInsert = $db->prepare($sqlUser);
        $resultUser = $startUserInsert->execute([$name, $email, $gender, $birthdate, $isAuthor, "empty.jpg"]);

        if ($resultUser) {
            $user_id = $db->lastInsertId();
            $sqlPass = "INSERT INTO credentials (`user_id`, `Hash`) VALUES (?, ?)";
            $startPassInsert = $db->prepare($sqlPass);
            $resultPass = $startPassInsert->execute([$user_id, $hash]);
            if ($resultPass) {
                header('Location: login.php');
                $alertMessage = null;
            } else {
                $alertMessage = "Error inserting user.";
            }
        } else {
            $alertMessage = "Error inserting user.";
        }
    }
}
?>

</div>

<body>
    <div class="form-container">
        <form action="register.php" method="post" enctype="multipart/form-data">  
            <?php if (isset($showAlert)) { ?>
                <p><?php echo $alertMessage; ?></p>
            <?php } ?>
            <h1>Register</h1>
            <hr class="mb-3">
            <div class="input-group"> 

                    <label class="fieldLabel text-left" for="name">Name:</label>
                    <input type="text" name="name" id="name" placeholder="Enter Your Name" required value="<?php if (isset($_POST['name'])) echo htmlspecialchars($_POST['name']); ?>"/>

                    <label class="fieldLabel text-left" for="email">E-mail:</label>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email" required value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>"/>
                    
                    <label class="fieldLabel text-left" for="Fpassword">Password:</label>
                    <input type="password" name="Fpassword" id="Fpassword" placeholder="Enter Your Password" required/>

                    <label class="fieldLabel text-left" for="Spassword">Password again:</label>
                    <input type="password" name="Spassword" id="Spassword" placeholder="Enter Your Password again" required/>

                    <label class="fieldLabel text-left" for="date">Birthdate:</label>
                    <input type="date" name="date" id="date" placeholder="Enter Your Birthdate" required value="<?php if (isset($_POST['date'])) echo htmlspecialchars($_POST['date']); ?>"/>

                    <div class="fieldGroup">
                        <label class="fieldLabel text-left" for="gender">Gender:</label>
                        <input type="radio" id="gender-male" name="gender" value="male" required/>Male
                        <input type="radio" id="gender-female" name="gender" value="female" required/>Female
                    </div>
                    <br />
                    <div class="fieldGroup">
                        <label class="fieldLabel text-left" for="author">Author:</label>
                        <input type="radio" id="author-yes" name="author" value="super" required/>Super
                        <input type="radio" id="author-no" name="author" value="normal" required />Normal
                    </div>  
                    

                    <br />
                    <hr class="mb-3">
                    <a class="mb-2" href="login.php">Already Have an account ?</a>
                    <input class="red" type="submit" name="submit" value="Register" />
            </div>
        </form>
    </div>
</body>
</html>