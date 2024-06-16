<?php
require_once('classes/database.php');
$con = new database();

$product_id = $_POST['id'];
// Check if the form is submitted
if (isset($_POST['update'])) {
    // Retrieve form data
    $product_id = $_POST['id'];
    $stock = $_POST['stock'];
    $price= $_POST['price'];
    // Update the product in the database
    $con->updateProduct($product_id, $stock, $price);
    // Redirect to the product page or any other page you want
    header('Location: product.php');
    exit;
} 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .custom-container {
            width: 800px;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<body>

    <div class="container custom-container rounded-3 shadow my-5 p-3 px-5">
        <h3 class="text-center mt-4">Update Product</h3>
        <form method="post">

            <!-- Product Information -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">Product Information</div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="stock">Stock:</label>
                            <input type="number" class="form-control" name="stock" value="<?php echo $stock['stock']; ?>" placeholder="Enter stock">
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="price">Price:</label>
                            <input type="number" class="form-control" name="price" value="<?php echo $price['price']; ?>" placeholder="Enter price">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="container">
                <div class="row justify-content-center gx-0">
                    <div class="col-lg-3 col-md-4">
                        <input type="hidden" name="id" value="<?php echo $product_id; ?>">
                        <input type="submit" name="update" class="btn btn-outline-primary btn-block mt-4" value="Update">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <a class="btn btn-outline-danger btn-block mt-4" href="product.php">Go Back</a>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
