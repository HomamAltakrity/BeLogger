<?php
    require_once('src/controllers/config.php');
    require_once('src/controllers/userData.php');
   /* This code is checking if a session is already active. If the session is not active, it starts a
   new session using `session_start()`. Then, it checks if the `user_id` session variable is set. If
   it is not set, it redirects the user to the login page (`src/controllers/login.php`) and exits
   the script. This code is used to ensure that only logged-in users can access the rest of the code
   in the file. */
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: src/controllers/login.php");
        exit();
    }
    
   /* This code block is checking if the form has been submitted by checking if the 'submit' button has
   been clicked. If the form has been submitted, it retrieves the values entered in the form fields
   (title and content) using the  superglobal. It also retrieves the user ID from the session
   variable. */
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $authorID = $_SESSION['user_id'];
        $currentDate = date("Y-m-d H:i:s"); 
        $isEdited = 0;
        $imageFilename = $_SESSION['user_id'] .  '_post_'  .  $_SESSION['user_name'] . '.jpg';
        $folder = "./images/posts/" . $imageFilename;
    
        if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $folder)) {
            $sqlPost = "INSERT INTO posts (`title`, `content`, `image`, `publish_date`, `is_edited`, `author_id`) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sqlPost);
    
            // Execute the query
            $result = $stmt->execute([$title, $content, $imageFilename, $currentDate, $isEdited, $authorID]);
    
            if ($result) {
                echo "Post added successfully!";
            } else {
                echo "Error inserting post into the database.";
            }
        } else {
            echo "Failed to upload image!";
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
    <?php include('src/controllers/header.php'); ?>
    <div class="layoutFixH"></div>
    <div class="content">
        
        <div class="filter">
            <form method="get" action="index.php">
            
                <div class="text-center">
                <select name="sort_by" id="sort_by">
                    <option value="date_desc">Date (Newest First)</option>
                    <option value="date_asc">Date (Oldest First)</option>
                </select>
                </div>
                <div class="text-center">
                    <button type="submit" style="color:black;">Filter</button>
                    <button type="button" style="color:black;" data-toggle="modal" data-target="#addPostModal">Add Post</button>
                </div>
            </form>  
        </div>
        <?php
            
            $sortOption = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_desc';
            include('src/controllers/posts.php');
            
        ?>
        <div class="layoutFixH"></div>
    </div>
   

    <div class="modal fade" id="addPostModal" tabindex="-1" role="dialog" aria-labelledby="addPostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="addPostModalLabel"><b>Add New Post</b></span>
                <button class="modal-close-button " type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php include('src/controllers/add_post.php'); ?>
            </div>
        </div>
    </div>
    </div>
    <div style="position: fixed; bottom: 0; left: 0; right: 0; margin-top: 10px;">
        <?php include('src/controllers/footer.php'); ?>
    </div>

   

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
