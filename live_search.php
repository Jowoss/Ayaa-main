<?php
require_once('classes/database.php');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if 'search' parameter is set in POST data
    if (isset($_POST['search'])) {
        // Sanitize the search term to prevent SQL injection
        $searchterm = htmlspecialchars($_POST['search']);
        
        // Create a new instance of database connection
        $con = new database();

        try {
            // Open database connection
            $connection = $con->opencon();

            // Check if the connection is successful
            if ($connection) {
                // Prepare SQL query using prepared statement to avoid SQL injection
                $query = $connection->prepare("SELECT product.product_id, product.name, product.stock, product.price
                                             FROM product
                                             WHERE product.name LIKE :searchterm OR product.product_id LIKE :searchterm");
                
                // Bind the parameter
                $query->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
                
                // Execute the query
                $query->execute();
                
                // Fetch all matching products
                $products = $query->fetchAll(PDO::FETCH_ASSOC);

                // Output the results as JSON (or you can format it differently)
                header('Content-Type: application/json');
                echo json_encode($products);
            } else {
                // Handle connection error if connection fails
                echo "Failed to connect to the database.";
            }
        } catch (PDOException $e) {
            // Handle PDO exceptions
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the database connection
            $con->closecon();
        }
    } else {
        // Handle case where 'search' parameter is not set
        echo "No search term provided.";
    }
} else {
    // Handle case where request method is not POST
    echo "Invalid request method.";
}
?>
