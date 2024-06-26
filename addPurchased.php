<?php
session_start();
require_once('classes/database.php');

$con = new Database();
$htmlAvailableProduct = '';
$htmlSelectedProduct = '';
$totalPrice = 0;
$dateTime = date('Y-m-d'); // Default to current date

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $connection = $con->opencon();

    // Check for connection error
    if (!$connection) {
        echo json_encode(['error' => 'Database connection failed.']);
        exit;
    }

    // Define the number of records per page
    $recordsPerPage = 5;

    // Get the current page number from the request, default to 1 if not set
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Get the total number of records
    $totalQuery = $connection->prepare("SELECT COUNT(*) AS total FROM product");
    $totalQuery->execute();
    $totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Fetch products for the current page
    $query = $connection->prepare("SELECT product_id, name, stock, price FROM product LIMIT :offset, :recordsPerPage");
    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
    $query->bindParam(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);
    $query->execute();
    $availableProducts = $query->fetchAll(PDO::FETCH_ASSOC);

    // Build HTML for available products table
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

    // Output the pagination HTML
    $paginationHtml = '';
    if ($totalPages > 1) {
        $paginationHtml .= '<nav><ul class="pagination justify-content-center">';
        if ($currentPage > 1) {
            $paginationHtml .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage - 1) . '">Previous</a></li>';
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = $i == $currentPage ? ' active' : '';
            $paginationHtml .= '<li class="page-item' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }
        if ($currentPage < $totalPages) {
            $paginationHtml .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage + 1) . '">Next</a></li>';
        }
        $paginationHtml .= '</ul></nav>';
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
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
        $htmlSelectedProduct .= '<td>';
        $htmlSelectedProduct .= '<button class="btn btn-danger" onclick="removeProduct(' . $product['product_id'] . ', ' . $product['price'] . ')">Remove</button>';
        $htmlSelectedProduct .= '</td>';
        $htmlSelectedProduct .= '</tr>';
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
  <h2 class="text-center my-4">Order Table</h2>
  
  <!-- Available Product Table -->
  <div class="card mb-4">
    <div class="card-header">
      <h4>Available Product</h4>
    </div>
    <div class="card-body table-responsive">
        <!-- Search input -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Search products...">
    </div>
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
    <?php echo $paginationHtml; ?>
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

<!-- Date Input -->
<div class="text-center mb-4">
    <label for="purchaseDate">Select Purchase Date: </label>
    <input type="date" id="purchaseDate" value="<?php echo $dateTime; ?>" class="form-control w-25 mx-auto">
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

$(document).ready(function() {
    $('#search').on('keyup', function() {
        var search = $(this).val();
        $.ajax({
            url: 'live_search.php',
            method: 'POST',
            data: {search: search},
            success: function(response) {
                $('#availableProducts').html(response);
            }
        });
    });
});

function addProduct(productId, price) {
    $.ajax({
        url: 'process_Purchased.php',
        method: 'POST',
        data: { action: 'add', product_id: productId },
        success: function(response) {
            location.reload();
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
    let purchaseDate = $('#purchaseDate').val(); // Get the selected purchase date

    $('#selectedProducts tr').each(function() {
        let productId = $(this).find('input.quantity').data('product-id');
        let quantity = $(this).find('input.quantity').val();
        let price = parseFloat($(this).find('td:nth-child(4)').text());
        quantity = parseInt(quantity, 10);

        if (productId && !isNaN(quantity) && quantity > 0 && purchaseDate) {
            selectedProducts[productId] = { quantity: quantity };
        } else {
            isValid = false;
            alert('Invalid product ID, quantity, or date for product: ' + (productId || 'Unknown'));
            return false;
        }
    });

    if (isValid) {
        console.log("Selected Products:", selectedProducts);

        $.ajax({
            url: 'process_Purchased.php',
            method: 'POST',
            data: {
                action: 'save',
                selected_products: selectedProducts,
                total_price: totalAmount,
                purchase_date: purchaseDate // Include the selected purchase date
            },
            success: function(response) {
                console.log("Server Response:", response);
                try {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert('Transaction saved successfully!');
                        // Example: Redirect to another page
                        window.location.href = 'purchased.php';
                        
                        // Example: Update UI with fetched data
                        // handleFetchedData(res.purchased_data);
                    } else {
                        alert('Failed to save transaction: ' + res.message);
                    }
                } catch (e) {
                    console.log('Response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }
}
</script>
</body>
</html>
