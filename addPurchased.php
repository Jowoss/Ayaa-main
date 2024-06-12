<?php
session_start();
require_once('classes/database.php');

$con = new Database();
$htmlAvailableProduct = '';
$htmlSelectedProduct = '';
$totalPrice = 0;
$dateTime = date('Y-m-d H:i:s'); // Get the current date and time

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

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
        $htmlAvailableProduct .= '<button class="btn btn-success" onclick="addProduct(' . $product['product_id'] . ', ' . $product['price'] . ')">Add</button>';
        $htmlAvailableProduct .= '</td>';
        $htmlAvailableProduct .= '</tr>';
    }
    $htmlAvailableProduct .= '</tbody>';
} else {
    $htmlAvailableProduct = '<tbody id="availableProducts"><tr><td colspan="5">No available products</td></tr></tbody>';
}

if (!isset($_SESSION['selected_products'])) {
    $_SESSION['selected_products'] = [];
}

if (count($_SESSION['selected_products']) > 0) {
    $selectedProductIds = array_keys($_SESSION['selected_products']);
    $selectedProducts = $con->fetchSelectedProducts($selectedProductIds);
    $htmlSelectedProduct = '<tbody id="selectedProducts">';
    foreach ($selectedProducts as $product) {
        $quantity = $_SESSION['selected_products'][$product['product_id']];
        $totalPrice += $product['price'] * $quantity;
        $htmlSelectedProduct .= '<tr>';
        $htmlSelectedProduct .= '<td>' . $product['product_id'] . '</td>';
        $htmlSelectedProduct .= '<td>' . $product['name'] . '</td>';
        $htmlSelectedProduct .= '<td>';
        $htmlSelectedProduct .= '<input type="number" class="quantity" data-product-id="' . $product['product_id'] . '" value="' . $quantity . '" min="1" max="' . $product['stock'] . '">';
        $htmlSelectedProduct .= '</td>';
        $htmlSelectedProduct .= '<td>' . $product['price'] . '</td>';
        $htmlSelectedProduct .= '<td>' . $dateTime . '</td>';
        $htmlSelectedProduct .= '<td>';
        $htmlSelectedProduct .= '<button class="btn btn-danger" onclick="removeProduct(' . $product['product_id'] . ', ' . $product['price'] . ')">Remove</button>';
        $htmlSelectedProduct .= '</td>';
        $htmlSelectedProduct .= '</tr>';
    }
} else {
    $htmlSelectedProduct = '<tbody id="selectedProducts"><tr><td colspan="6">No selected products</td></tr></tbody>';
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
                    <th>Date & Time</th> <!-- Add Date and Time column -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="selectedProducts">
                <?php echo $htmlSelectedProduct; ?>
            </tbody>
        </table>
    </div>
</div>
  
<div class="text-center mb-4">
    <span id="totalAmount">Total Amount: ₱<?php echo number_format($totalPrice, 2); ?></span>
</div>

  <!-- Save Transaction Button -->
  <div class="text-center mb-4">
  <button class="btn btn-primary" onclick="saveTransaction()">Save Transaction</button>
</div>

<script>
var totalAmount = <?php echo $totalPrice; ?>;

function addProduct(productId, price) {
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'add', product_id: productId },
        success: function(response) {
            location.reload();
            // Update total amount
            totalAmount += price;
            $('#totalAmount').text('Total Amount: ₱' + totalAmount.toFixed(2));
        }
    });
}

function removeProduct(productId, price) {
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'remove', product_id: productId },
        success: function(response) {
            location.reload();
            // Update total amount
            totalAmount -= price;
            $('#totalAmount').text('Total Amount: ₱' + totalAmount.toFixed(2));
        }
    });
}

function updateTotal() {
    totalAmount = 0;
    $('#selectedProducts tr').each(function() {
        var price = parseFloat($(this).find('td:nth-child(4)').text());
        var quantity = parseInt($(this).find('input.quantity').val());
        if (!isNaN(price) && !isNaN(quantity)) {
            totalAmount += price * quantity;
        }
    });
    $('#totalAmount').text('Total Amount: ₱' + totalAmount.toFixed(2));
}

$(document).on('change', '.quantity', function() {
    var productId = $(this).data('product-id');
    var newQuantity = $(this).val();
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'updateQuantity', product_id: productId, quantity: newQuantity },
        success: function(response) {
            updateTotal();
        }
    });
});

function saveTransaction() {
    let selectedProducts = {};
    let isValid = true;

    $('#selectedProducts tr').each(function() {
        let productId = $(this).find('input[type="number"]').data('product-id');
        let quantity = $(this).find('input.quantity').val();
        let price = parseFloat($(this).find('td:nth-child(4)').text());
        quantity = parseInt(quantity, 10);

        if (productId && !isNaN(quantity) && quantity > 0) {
            selectedProducts[productId] = quantity;
        } else {
            isValid = false;
            alert('Invalid product ID or quantity for product: ' + (productId || 'Unknown'));
            return false;
        }
    });

    if (isValid) {
        $.ajax({
            url: 'process_Purchased.php',
            method: 'POST',
            data: {
                action: 'save',
                selected_products: selectedProducts
            },
            success: function(response) {
                let res = JSON.parse(response);
                if (res.status === 'success') {
                    alert('Transaction saved successfully!');
                    window.location.href = 'purchased.php';
                } else {
                    alert('Failed to save transaction: ' + res.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving transaction:', error);
                alert('Error saving transaction: ' + error);
            }
        });
    }
}

// Initialize total amount display on page load
$(document).ready(function() {
    updateTotal();
});


</script>
</body>
</html>
