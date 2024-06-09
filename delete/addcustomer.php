<!-- <?php
require_once('classes/database.php');
$con = new database();

//  session_start();

if(isset($_POST['customer'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $contact = $_POST['contact'];

    // Handle file upload
    if(isset($_FILES['profilepic'])) {
        $target_dir = "customer/";
        $original_file_name = basename($_FILES["profilepic"]["name"]);
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
        $check = getimagesize($_FILES["profilepic"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["profilepic"]["size"] > 500000) {
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
            if (move_uploaded_file($_FILES["profilepic"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars($new_file_name) . " has been uploaded.";

                // Save the user data and the path to the profile picture in the database
                $profile_picture_path = 'customer/'.$new_file_name;
                $userID = $con->signupCustomer($firstname, $lastname, $contact,$profile_picture_path);

                if ($userID) {
                    // Signup successful, redirect to login page
                    header('location: customer.php');
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
    <title>Customer Information Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/customer.css">
   
</head>
<body>
  <div class="container">
    <h2>Customer Information</h2>
    <form action="#" method="post" enctype="multipart/form-data">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="contact">Contact Number:</label>
        <input type="number" id="contact" name="contact" required>

        <label for="profilepic">Profile Picture:</label>
        <input type="file" id="profilepic" name="profilepic" accept="image/*" required>

        <input type="submit" value="Add Customer" class="btn btn-danger btn-block" name="customer">
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html> -->