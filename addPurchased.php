<?php
session_start();
require_once('classes/database.php');

$con = new Database();
$htmlAvailableProduct = '';
$htmlSelectedProduct = '';

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    // If not, redirect to the login page or handle the error accordingly
    header('Location: login.php');
    exit;
}

// Assuming user ID is stored in session
// Replace this with actual user ID from session

// Initialize selected products array
// Fetch available products
$availableProducts = $con->fetchAvailableProduct();

if (count($availableProducts) > 0) {
    $htmlAvailableProduct = '<tbody id="availableProducts">';
    foreach ($availableProducts as $product) {
        $htmlAvailableProduct .= '<tr>';
        $htmlAvailableProduct .= '<td>' . $product['product_id'] . '</td>';
        $htmlAvailableProduct .= '<td>' . $product['name'] . '</td>';
        $htmlAvailableProduct .= '<td>' . $product['stock'] . '</td>';
        $htmlAvailableProduct .= '<td>' . $product['price'] . '</td>';
        $htmlAvailableProduct .= '<td>';
        $htmlAvailableProduct .= '<button class="btn btn-success" onclick="addProduct(' . $product['product_id'] . ')">Add</button>';
        $htmlAvailableProduct .= '</td>';
        $htmlAvailableProduct .= '</tr>';
    }
    $htmlAvailableProduct .= '</tbody>';
} else {
    $htmlAvailableProduct = '<tbody id="availableProducts"><tr><td colspan="5">No available products</td></tr></tbody>';
}

// Fetch selected products from session
if (!isset($_SESSION['selected_products'])) {
  $_SESSION['selected_products'] = [];
}

if (count($_SESSION['selected_products']) > 0) {
  $selectedProductIds = $_SESSION['selected_products'];
  $selectedProducts = $con->fetchSelectedProducts($selectedProductIds);
  $htmlSelectedProduct = '<tbody id="selectedProducts">';
  foreach ($selectedProducts as $product) {
      $htmlSelectedProduct.= '<tr>';
      $htmlSelectedProduct.= '<td>'. $product['product_id']. '</td>';
      $htmlSelectedProduct.= '<td>'. $product['name']. '</td>';
      $htmlSelectedProduct.= '<td>'. $product['stock']. '</td>';
      $htmlSelectedProduct.= '<td>'. $product['price']. '</td>';
      $htmlSelectedProduct.= '<td><button class="btn btn-danger" onclick="removeProduct('. $product['product_id']. ')">Remove</button></td>';
      $htmlSelectedProduct.= '</tr>';
  }
} else {
  $htmlSelectedProduct = '<tbody id="selectedProducts"><tr><td colspan="5">No selected products</td></tr></tbody>';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bumili</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container">
  <h2 class="text-center my-4">Purchased Table</h2>
  
  <!-- Available Product Table -->
  <div class="card mb-4">
    <div class="card-header">
      <h4>Available Product</h4>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered w-100">
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Available Stock</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="availableProducts"> 
        <?php echo $htmlAvailableProduct; ?>
      </table>
    </div>
  </div>
  
  <!-- Selected Product Table -->
  <div class="card mb-4">
    <div class="card-header">
        <h4>Selected Product</h4>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="selectedProducts">
                <?php echo $htmlSelectedProduct; ?>
            </tbody>
        </table>
    </div>
</div>
  
  <!-- Save Transaction Button -->
  <div class="text-center mb-4">
    <button class="btn btn-primary" onclick="saveTransaction()">Save Transaction</button>
  </div>
</div>

<script>
function addProduct(productId) {
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'add', product_id: productId },
        success: function(response) {
            location.reload();
        }
    });
    var_dump($_SESSION['product_id']); 
}
 
function removeProduct(productId) {
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'remove', product_id: productId }, // Change 'product' to 'product_id'
        success: function(response) {
            location.reload();
        }
    });
}
 
function saveTransaction() {
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'save' },
        success: function(response) {
            alert('Transaction saved successfully!');
            location.reload();
        }
    });
}
   
</script>
</body>
</html>