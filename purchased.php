<?php
require_once('classes/database.php');
$con = new database();
 
    // Proceed with your database operations
    if(isset($_POST['product'])) {
        $product_id = $_POST['id'];
        if($con->deletePro($product_id)) {
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
                        <th>Date and Time</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $counter = 1;
                    $data = $con->getPurchasedData();
                    foreach($data as $row) {
                ?>
                    <tr>
                        <td><?php echo $counter++;?></td>
                        <td><?php echo $row['product_name'];?></td>
                        <td><?php echo $row['product_quantity'];?></td>
                        <td><?php echo $row['date_purchased'];?></td>
                        <td><?php echo $row['payment_totalamount'];?></td>
                        <td>
                        <form action="updateproduct.php" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['purchased_id']; ?>">
                            <button type="submit" name="edit" class="btn btn-primary btn-sm">Edit</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['purchased_id']; ?>">
                            <input type="submit" name="product" value="Delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">
                        </form>
                        </td>
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