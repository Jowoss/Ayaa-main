<?php
require_once('classes/database.php');
$con = new database();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['product'])) {
    $product_id = $_POST['id'];
    if ($con->deletePro($product_id)) {
        header('location:product.php');
    } else {
        echo 'Error: Unable to delete product.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="./css/product.css">
</head>

<body>

<?php include('user_navbar.php');?>
<?php include('sidebar.php');?>
<div class="container">
    <h1>Inventory Management</h1>
    <div class="container user-info rounded shadow p-3 my-2">
        <h2 class="text-center mb-2">Purchased Table</h2>
        <div class="table-responsive text-center">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Product Quantity</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $data = $con->getPurchasedData();
                    $currentPaymentId = null; // Track current payment_id
                    $counter = 1; // Initialize counter
                    foreach ($data as $row) {
                        // Check if new payment_id group is starting
                        if ($row['payment_id'] !== $currentPaymentId) {
                            // Display a row for the new payment group
                            echo "<tr>";
                            echo "<td colspan='4' class='text-left'><strong>Payment ID: {$row['payment_id']}</strong></td>";
                            echo "<td colspan='4' class='text-left-right'><strong>  ₱ {$row['payment_totalamount']}</strong></td>"; // Display total amount in the same row
                            echo "</tr>";
                            $currentPaymentId = $row['payment_id'];
                            $counter = 1; // Reset the counter for the new group
                        }
                        // Display each purchased item within the current payment group
                        ?>
                        <tr>
                            <td><?php echo $counter++;?></td>
                            <td><?php echo htmlspecialchars($row['product_name']);?></td>
                            <td><?php echo htmlspecialchars($row['product_quantity']);?></td>
                            <td><?php echo htmlspecialchars($row['date_purchase']??'');?></td>
                            <td></td> <!-- Add any additional columns here if needed -->
                        </tr>
                <?php
                    }
                ?>
                </tbody>
            </table>
        </div>
   
        <div class="text-center mt-3">
            <a href="addPurchased.php" class="btn btn-primary">Add Order</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

