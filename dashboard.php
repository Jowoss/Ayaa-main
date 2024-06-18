<?php
require_once('classes/database.php');
$con = new database();


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include('user_navbar.php'); ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
<?php
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number from query string
$recordsPerPage = 5; // Number of records to display per page

$startFrom = ($page - 1) * $recordsPerPage; // Calculate the starting point for fetching records

$salesData = $con->getSalesPerformanceDataPaginated($startFrom, $recordsPerPage); // Modify this method in your database class

?>
    

<?php include('sidebar.php'); ?>
    
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="summary">
            <div class="summary-item">
                <h2>Total Sales</h2>
                <p>₱ <span id="total-sales"><?php echo $con->getTotalSales(); ?></span></p>
            </div>
            <div class="summary-item">
                <h2>Total Customers</h2>
                <p><span id="total-customers"><?php echo $con->getTotalCustomers(); ?></span></p>
            </div>
        </div>

        <div class="table-container">
    <h2>Sales Performance Table</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Sales</th>
            </tr>
        </thead>
        <tbody id="sales-performance">
            <?php
            // PHP code for populating sales performance table dynamically with pagination
            $salesData = $con->getSalesPerformanceDataPaginated($startFrom, $recordsPerPage); // Fetch data based on current page
            foreach ($salesData as $row) {
                echo '<tr>';
                echo '<td>' . $row['date_purchase'] . '</td>';
                echo '<td>₱ ' . number_format($row['payment_totalamount'], 2) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <!-- Pagination links -->
    <ul class="pagination">
        <?php
        // Generate pagination links dynamically
        $totalPages = ceil($con->countTotalSales() / $recordsPerPage); // Assuming a method to count total records
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }
        ?>
    </ul>
</div>
        <div class="table-container">
            <h2>Popular Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Most Bought Products</th>
                    </tr>
                </thead>
                <tbody id="popular-product">
                    <!-- Popular categories data will be inserted here dynamically -->
                    <?php
                    // PHP code for populating sales performance table dynamically
                    $productData = $con->getmostboughtproduct(); // Assuming a method to fetch sales performance data from the database
                    foreach ($productData as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td> ' . $row['MostBoughtProduct'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
    <!-- Bootstrap JS for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>