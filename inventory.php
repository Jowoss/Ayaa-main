<?php
require_once('classes/database.php');
$con = new database();

 session_start();

 if(isset($_POST['category'])){
    $type = $_POST['type'];

    
                $category_id = $con->addCategory($type);

                if ($category_id) {
                    // Signup successful, redirect to login page
                    header('location: category.php');
                    exit; // Stop further execution
                } else {
                    // Signup failed, display error message
                    echo "Sorry, there was an error signing up.";
                
    }
}
if(isset($_POST['product'])){
    $name = $_POST['name'];
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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="./css/inventory.css">
</head>
<body>
    <div class="container">
        <h1>Inventory Management</h1>

         <!-- Add Category Card -->
        <div class="card">
            <div class="card-header">
                <h2>Add Category</h2>
            </div>
            <div class="card-body">
                <form method="post" >
                    <div class="form-group">
                        <label for="categoryName">Category Name:</label>
                        <input type="text" id="categoryName" name="type">
                    </div>
                    <button type="submit" class="btn" name="category">Add Category</button>
                </form>
            </div>

        <!-- Add Product Card -->
        <div class="card">
            <div class="card-header">
                <h2>Add Product</h2>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="productName">Product Name:</label>
                        <input type="text" id="productName" name="name"required>
                    </div>
                    <div class="form-group">
                        <label for="categoryName">Category:</label>
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
                    <div class="form-group">
                        <label for="stock">Stock:</label>
                        <input type="number" id="stock" name="stock" min="0"required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"required>
                    </div>
                    <div class="form-group">
                        <label for="expiration">Expiration Date:</label>
                        <input type="date" id="expiration" name="expiration">
                    </div>
                    <div class="form-group">
                        <label for="productPicture">Product Picture:</label>
                        <input type="file" id="productPicture" name="productPicture"accept="image/*" required>
                    </div>
                    <button type="submit" class="btn" name="product">Add Product</button>
                </form>
            </div>
        </div>
       
    </div>
</body>
</html>
