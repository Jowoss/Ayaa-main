<?php
require_once('classes/database.php');
$con = new database();
$html = ''; // Initialize empty variable for product table content

// Handle product deletion
if(isset($_POST['product'])){
    $product_id = $_POST['id'];
    if($con->deletePro($product_id)){
        header('location:product.php');
        exit;
    } else {
        echo 'Something went wrong';
    }
}

try {
    $connection = $con->opencon();

    // Check for connection error
    if (!$connection) {
        echo json_encode(['error' => 'Database connection failed.']);
        exit;
    }

    // Define the number of records per page
    $recordsPerPage = 6;

    // Get the current page number from the request, default to 1 if not set
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Get the total number of records
    $totalQuery = $connection->prepare("SELECT COUNT(*) AS total FROM product");
    $totalQuery->execute();
    $totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Fetch products for the current page
    $query = $connection->prepare("SELECT product.product_id, product.name, product.stock, product.price, product.expiration_date, category.type, product.picture
                                   FROM product
                                   INNER JOIN category ON product.category_id = category.category_id
                                   LIMIT :offset, :recordsPerPage");
    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
    $query->bindParam(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $html .= '<tr>';
        $html .= '<td>' . $product['product_id'] . '</td>';
        $html .= '<td><img src="' . htmlspecialchars($product['picture']) . '" alt="Product Picture" style="width: 50px; height: 50px; border-radius: 50%;"></td>';
        $html .= '<td>' . $product['name'] . '</td>';
        $html .= '<td>' . $product['type'] . '</td>';
        $html .= '<td>' . $product['stock'] . '</td>';
        $html .= '<td>' . $product['price'] . '</td>';
        $html .= '<td>' . $product['expiration_date'] . '</td>';
        $html .= '<td>'; // Action column
        $html .= '<form action="updateproduct.php" method="post" style="display: inline;">';
        $html .= '<input type="hidden" name="id" value="' . $product['product_id'] . '">';
        
        $html .= '<button type="submit" class="btn btn-primary btn-sm">Edit</button>';
        $html .= '</form>';
        $html .= '<form method="POST" style="display: inline;">';
        $html .= '<input type="hidden" name="id" value="' . $product['product_id'] . '">';
        $html .= '<input type="submit" name="product" class="btn btn-danger btn-sm" value="Delete" onclick="return confirm(\'Are you sure you want to delete this product?\')">';
        $html .= '</form>';
        $html .= '</td>';
        $html .= '</tr>';
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
  <link rel="stylesheet" href="./css/product.css">
</head>

<body>

<?php include('user_navbar.php');?>
<?php include('sidebar.php');?>
<div class="container">
        <h1>Inventory Management</h1>
<div class="container user-info rounded shadow p-3 my-2">
    <h2 class="text-center mb-2">Product Table</h2>
    <div class="table-responsive text-center">
        <table class="table table-bordered">
        <i class='bx bx-edit'></i>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Expiration Date</th>
                    <th>Actions</th> <!-- Actions column header -->
                </tr>
            </thead>
            <tbody>
                <?php echo $html; ?>
            </tbody>
        </table>
    </div>
    <?php echo $paginationHtml; ?>
    
    <!-- Button for adding product -->
    <div class="text-center mt-3">
        <a href="addproduct.php" class="btn btn-primary">Add Product</a>
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
