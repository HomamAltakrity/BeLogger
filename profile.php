<?php
require_once('src/controllers/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: src/controllers/login.php');
    exit();
}

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$userImage = $_SESSION['user_image'];

/* This code block is responsible for updating the user's profile information and image. */
if (isset($_POST['update_profile'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];

    $sqlUpdate = "UPDATE user SET name = ?, email = ?, gender = ?, birthdate = ? WHERE id = ?";
    $stmt = $db->prepare($sqlUpdate);
    $stmt->execute([$name, $email, $gender, $birthdate, $userID]);
    $_SESSION['user_name'] = $name;
    if ($_FILES['profile_image']['size'] > 0) {
        $targetDirectory = 'images/profiles/';
        $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $profileImageName = $userID . '_Profile_' . $name . '.' . $fileExtension;
        $targetFile = $targetDirectory . $profileImageName;
        $_SESSION['user_image'] = $profileImageName;
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }


        $check = getimagesize($_FILES['profile_image']['tmp_name']);

        if ($check !== false) {
            $allowedFormats = ['jpg', 'jpeg'];

            if (in_array($fileExtension, $allowedFormats)) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                    $sqlUpdateImage = "UPDATE user SET Image = ? WHERE id = ?";
                    $stmtUpdateImage = $db->prepare($sqlUpdateImage);
                    $stmtUpdateImage->execute([$profileImageName, $userID]);
                } else {
                    echo '<script>alert("Error uploading image.");</script>';
                }
            } else {
                echo '<script>alert("Invalid image format. Allowed formats: jpg, jpeg");</script>';
            }
        } else {
            echo '<script>alert("File is not an image.");</script>';
        }
    }


    header('Location: profile.php');
    exit();
}
/* This code block is selecting the user's profile information from the database based on their user
ID. It prepares and executes a SQL statement to fetch the user data from the "user" table. The
fetched data is then stored in the `` variable as an associative array. */
$sqlSelectUser = "SELECT * FROM user WHERE id = ?";
$stmtSelectUser = $db->prepare($sqlSelectUser);
$stmtSelectUser->execute([$userID]);
$userData = $stmtSelectUser->fetch(PDO::FETCH_ASSOC);

$name = $userData['name'];
$email = $userData['email'];
$gender = $userData['gender'];
$birthdate = $userData['birthdate'];
$profileImage = $userData['Image'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="src/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="src/css/formStyle.css">
    <style>
        .backIcon {
            text-decoration: none;
            color: inherit;
        }

        .backIcon:hover {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h1><a class="backIcon" href="index.php"><span>
                            <&nbsp;</span></a>Edit Profile</h1>
                <hr class="mb-3">
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="male" <?php if ($gender === 'male')
                                echo 'selected'; ?>>Male</option>
                            <option value="female" <?php if ($gender === 'female')
                                echo 'selected'; ?>>Female</option>
                            <option value="other" <?php if ($gender === 'other')
                                echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Birthdate:</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate"
                            value="<?php echo $birthdate; ?>" required>
                    </div>

                    <div class="text-center">
                        <img src="images/profiles/<?php echo $userImage; ?>" alt="User Image"
                            class="rounded-circle user-image" width="100px" height="100px">
                    </div>
                    <div class="form-group">
                        <label for="profile_image">Profile Image:</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="profile_image" name="profile_image"
                                    onchange="updateLabel()">
                                <label class="custom-file-label" for="profile_image">
                                    <?php echo basename($profileImage); ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <script>
                        function updateLabel() {
                            const input = document.getElementById('profile_image');
                            const label = input.nextElementSibling;

                            if (input.files.length > 0) {
                                label.innerHTML = input.files[0].name;
                            } else {
                                label.innerHTML = <?php echo json_encode(basename($profileImage)); ?>;
                            }
                        }
                    </script>


            </div>
        </div>
        <div class="text-center">
            <a href="user_posts.php">View My Posts</a>
        </div>
        <div class="form-group text-center mt-3">

            <button type="submit" name="update_profile">Update Profile</button>
        </div>
        </form>
    </div>
    </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>