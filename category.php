<?php
require_once('classes/database.php');
$con = new database();
 
// session_start();


// if (empty($_SESSION['admin_id'])){
//   header('location:product.php');                                                                                                                                                 
//  }

if(isset($_POST['category'])){
    $category_id = $_POST['id'];
    if($con->deleteCat($category_id)){
        header('location:category.php');
} else {
    echo 'Something went wrong';
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
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- For Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="./css/category.css">
</head>

<body>
<?php include('user_navbar.php');?>
<?php include('sidebar.php');?>
<div class="container">
        <h1>Inventory Management</h1>
<div class="container user-info rounded shadow p-3 my-2">
    <h2 class="text-center mb-2">Category Table</h2>
    <div class="table-responsive text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Type</th>
                    <th>Actions</th> <!-- Actions column header -->
                </tr>
            </thead>
            <tbody>
            <?php
                $counter = 1;
                $data = $con->getCategoryData();
                foreach($data as $row) {
                ?>
                <tr>
                    <td><?php echo $counter++;?></td>
                    <td><?php echo $row['Type'];?></td>
                  </td>
                    <td>
                       
                        <!-- Delete button -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['category_id']; ?>">
                            <input type="submit"  name="del" value="Delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">
                        </form>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Button for adding product -->
    <div class="text-center mt-3">
        <a href="addcategory.php" class="btn btn-primary">Add Category Product</a>
    </div>
            </div>
</div>
 
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<!-- Bootsrap JS na nagpapagana ng danger alert natin -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html>