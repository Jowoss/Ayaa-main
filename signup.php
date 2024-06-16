<?php
session_start();
require_once('classes/database.php');
$con = new database();
$error = "";

if (isset($_POST['signup'])) {
    // Getting the account information
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Getting the personal information
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    // Handle file upload
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
    } else {
        // Update $target_file with the original file name
        $target_file = $target_dir . $original_file_name;
    }

    // Check if file is an actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_formats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_formats)) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Save the user data and the path to the profile picture in the database
            $profile_picture_path = 'uploads/' . $new_file_name; // Save the new file name (with directory)

            // Call the signupUser method from your database class
            $userID = $con->signupUser($firstname, $lastname, $username, $password, $profile_picture_path);
            if ($userID) {
                // Redirect to login.php after registration
                header('Location: login.php');
                exit; // Ensure no further code execution after redirection
            } else {
                $error = "Error registering user.";
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .form-step {
            display: none;
        }

        .form-step-active {
            display: block;
        }
    </style>
</head>
<body>
<div class="container custom-container rounded-3 shadow my-5 p-3 px-5">
    <h3 class="text-center mt-4">Registration Form</h3>
    <form id="registration-form" method="post" action="" enctype="multipart/form-data" novalidate>
        <!-- Step 1 -->
        <div class="form-step form-step-active" id="step-1">
            <div class="card mt-4">
                <div class="card-header bg-info text-white">Account Information</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please enter a valid username.</div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character.</div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" class="form-control" name="password_confirm" id="confirmPassword" placeholder="Re-enter your password" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" onclick="nextStep()">Next</button>
                </div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="form-step" id="step-2">
            <div class="card mt-4">
                <div class="card-header bg-info text-white">Personal Information</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Enter first name" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please enter a valid first name.</div>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Enter last name" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please enter a valid last name.</div>
                    </div>
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture:</label>
                        <input type="file" class="form-control" name="profile_picture" id="profile_picture" accept="image/*" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please upload a profile picture.</div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-3" onclick="prevStep()">Previous</button>
                    <button type="submit" class="btn btn-primary mt-3" name="signup">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        // Initialize current step variable and form element
        let currentStep = 0;
        const form = document.getElementById('registration-form');
        const steps = document.querySelectorAll('.form-step');

        // Function to move to the next step
        window.nextStep = () => {
            if (validateStep(currentStep)) {
                steps[currentStep].classList.remove('form-step-active');
                currentStep++;
                steps[currentStep].classList.add('form-step-active');
            }
        };

        // Function to move to the previous step
        window.prevStep = () => {
            steps[currentStep].classList.remove('form-step-active');
            currentStep--;
            steps[currentStep].classList.add('form-step-active');
        };

        // Validate all inputs in the current step
        function validateStep(step) {
            let valid = true;
            const stepInputs = steps[step].querySelectorAll('input, select');
            stepInputs.forEach(input => {
                if (!validateInput(input)) {
                    valid = false;
                }
            });
            return valid;
        }

        // Validate an individual input
        function validateInput(input) {
            switch (input.name) {
                case 'password':
                    return validatePassword(input);
                case 'password_confirm':
                    return validateConfirmPassword(input);
                default:
                    if (input.checkValidity()) {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                        return true;
                    } else {
                        input.classList.remove('is-valid');
                        input.classList.add('is-invalid');
                        return false;
                    }
            }
        }

        // Validate password format
        function validatePassword(passwordInput) {
            const password = passwordInput.value;
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (regex.test(password)) {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
                return true;
            } else {
                passwordInput.classList.remove('is-valid');
                passwordInput.classList.add('is-invalid');
                return false;
            }
        }

        // Validate password confirmation
        function validateConfirmPassword(confirmPasswordInput) {
            const passwordInput = form.querySelector("input[name='password']");
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword !== '') {
                if (password === confirmPassword) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    return true;
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                    return false;
                }
            } else {
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.remove('is-invalid');
                return false;
            }
        }

        // Event listener to prevent form submission on Enter key press
        document.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        // Event listener for form submission
        form.addEventListener('submit', event => {
            if (!validateStep(currentStep)) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
</script>

</body>
</html>