<?php
require_once('classes/database.php');
$con = new database();
 ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- For Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="./css/dashboard.css">
  
    
</head>
<body>
<?php include('user_navbar.php');?>
<?php include('sidebar.php');?>
    
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="summary">
            <div class="summary-item">
                <h2>Total Sales</h2>
                <p>$ <span id="total-sales"><?php echo getTotalSales(); ?></span></p>
            </div>
            <div class="summary-item">
                <h2>Total Income</h2>
                <p>$ <span id="total-income"><?php echo getTotalIncome(); ?></span></p>
            </div>
            <div class="summary-item">
                <h2>Total Customers</h2>
                <p><span id="total-customers"><?php echo getTotalCustomers(); ?></span></p>
            </div>
        </div>
        <div class="table-container">
            <h2>Sales Performance Table</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sales</th>
                    </tr>
                </thead>
                <tbody id="sales-performance">
                    <?php populateSalesPerformanceTable(); ?>
                </tbody>
            </table>
        </div>
        <div class="table-container">
            <h2>Popular Categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Number of Sales</th>
                    </tr>
                </thead>
                <tbody id="popular-categories">
                    <!-- Popular categories data will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
        <div class="table-container">
            <h2>Recent Customers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody id="recent-customers">
                    <!-- Recent customers data will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
    </div>
    

    <?php
    // Function to simulate fetching total sales from database
    function getTotalSales() {
        // Simulated data
        return number_format(5000, 2); // Replace with actual database query
    }

    // Function to simulate fetching total income from database
    function getTotalIncome() {
        // Simulated data
        return number_format(3000, 2); // Replace with actual database query
    }

    // Function to simulate fetching total customers from database
    function getTotalCustomers() {
        // Simulated data
        return 100; // Replace with actual database query
    }

    // Function to populate the sales performance table
    function populateSalesPerformanceTable() {
        // Simulated data
        $salesData = array(
            array("date" => "2024-05-01", "sales" => 1200),
            array("date" => "2024-05-02", "sales" => 1500),
            array("date" => "2024-05-03", "sales" => 1800),
            array("date" => "2024-05-04", "sales" => 2000),
            array("date" => "2024-05-05", "sales" => 2200)
        );

        foreach ($salesData as $item) {
            echo "<tr>";
            echo "<td>" . $item['date'] . "</td>";
            echo "<td>$" . number_format($item['sales'], 2) . "</td>";
            echo "</tr>";
        }
    }
    ?>
    <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<!-- Bootsrap JS na nagpapagana ng danger alert natin -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
