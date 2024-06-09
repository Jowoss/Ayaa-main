<?php
require_once('classes/database.php');
$con = new database();

session_start();

if(isset($_POST['product'])){
    $name = $_POST['name'];
    // Check if the product already exists
    $existingProduct = $con->getProductByName($name);
    if ($existingProduct) {
        // Product already exists, set error message
        $error = "Product '$name' already exists.";
    } else {
        // Proceed with adding the product to the database
        $type = $_POST['type'];
        $stock = $_POST['stock'];
        $price = $_POST['price'];
        $expiration = $_POST['expiration'];
        // Handle file upload
        if(isset($_FILES['productPicture'])) {
            $target_dir = "products/";
            $original_file_name = basename($_FILES["productPicture"]["name"]);
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
            $check = getimagesize($_FILES["productPicture"]["tmp_name"]);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["productPicture"]["size"] > 500000) {
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
                if (move_uploaded_file($_FILES["productPicture"]["tmp_name"], $target_file)) {
                    echo "The file " . htmlspecialchars($new_file_name) . " has been uploaded.";

                    // Save the user data and the path to the profile picture in the database
                    $profile_picture_path = 'products/'.$new_file_name;
                    $productID = $con->addProduct($name,$type, $stock, $price, $expiration, $profile_picture_path);

                    if ($productID) {
                        // Signup successful, redirect to login page
                        header('location: product.php');
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
            echo "Product picture not found.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/product.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Add Product</h2>
        <!-- Display error message if it exists -->
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="name" required>
                
            </div>


            <div class="mb-3">
    <label for="categoryName" class="form-label">Category</label>
    <select class="form-control" id="categoryName" name="type" required>
        <?php
        // Fetch categories from the database
        $categories = $con->getCategoryData();
        // Check if categories are fetched
        if ($categories) {
            // Loop through each category and populate the dropdown
            foreach ($categories as $category) {
                echo "<option value='" . $category['Type'] . "'>" . $category['Type'] . "</option>";
            }
        } else {
            echo "<option value=''>No categories available</option>";
        }
        ?>
    </select>
</div>

            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="expiration" class="form-label">Expiration Date</label>
                <input type="date" class="form-control" id="expiration" name="expiration" required>
            </div>
            <div class="mb-3">
                <label for="productPicture" class="form-label">Product Picture</label>
                <input type="file" class="form-control" id="productPicture" name="productPicture" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary" name="product">Add Product</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="./bootstrap-5.3.3-dist
