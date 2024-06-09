<?php
require_once('classes/database.php');
$con = new database();

 session_start();

if(isset($_POST['signup'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['user'];
    $password = $_POST['pass'];
    $confirm = $_POST['confirm'];

    // Handle file upload
    if(isset($_FILES['profile_picture'])) {
        $target_dir = "uploads/";
        $original_file_name = basename($_FILES["profile_picture"]["name"]);
        $new_file_name = $original_file_name;
        $target_file = $target_dir . $original_file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Check if file already exists and rename if necessary
        if (file_exists($target_file)) {
            // Generate a unique file name by appending a timestamp
            $new_file_name = pathinfo($original_file_name, PATHINFO_FILENAME) . '_' . time() . '.' . $imageFileType;
            $target_file = $target_dir . $new_file_name;
        }

        // Check if file is an actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["profile_picture"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars($new_file_name) . " has been uploaded.";

                // Save the user data and the path to the profile picture in the database
                $profile_picture_path = 'uploads/'.$new_file_name;
                $userID = $con->signupUser($firstname, $lastname, $username, $password, $profile_picture_path);

                if ($userID) {
                    // Signup successful, redirect to login page
                    header('location: login.php');
                    exit; // Stop further execution
                } else {
                    // Signup failed, display error message
                    echo "Sorry, there was an error signing up.";
                }
            } else {
                // File upload failed, display error message
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "Profile picture not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/main.css">
</head>
<body>


<div class="container-fluid rounded shadow login-container">
    <h2 class="text-center mb-4">Sign-Up</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" class="form-control" name="firstname" placeholder="Enter your first name" >
            </div>
            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" class="form-control" name="lastname" placeholder="Enter you last name">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="user" placeholder="Enter your name" >
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="pass" placeholder="Enter password">
            </div>
            <div class="form-group">
                <label for="password">Confirm Password:</label>
                <input type="password" class="form-control" placeholder="Confirm your password" name="confirm">
            </div>
            <div class="form-group">
                <label for="profilePicture">Profile Picture:</label>
                <input type="file" class="form-control" name="profile_picture" accept="image/*" required>
                <div class="invalid-feedback">Please upload a profile picture.</div>
            </div>
            <input type="submit" value="Sign Up" class="btn btn-danger btn-block" name="signup">
        </div>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>