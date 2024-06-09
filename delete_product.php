<?php
// Check if the ID parameter is set and not empty
if(isset($_POST['id']) && !empty($_POST['id'])) {
    // Include the database class file
    require_once('classes/database.php');

    // Create a new instance of the database class
    $con = new database();

    // Get the product ID from the POST data
    $product_id = $_POST['id'];

    // Attempt to delete the product from the database
    if($con->deletePro($product_id)) {
        // Product successfully deleted
        echo json_encode(['success' => 'Product deleted successfully.']);
    } else {
        // Error occurred while deleting the product
        echo json_encode(['error' => 'Error deleting product.']);
    }
} else {
    // ID parameter not set or empty
    echo json_encode(['error' => 'Invalid request.']);
}
?>