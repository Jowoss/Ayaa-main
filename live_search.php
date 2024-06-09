<?php
require_once('classes/database.php');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['search'])) {
        $searchterm = $_POST['search']; 
        $con = new database();

        try {
            $connection = $con->opencon();
            
            // Check if the connection is successful
            if ($connection) {
                // SQL query with JOIN
                $query = $connection->prepare("SELECT product.product_id, product.name, product.stock, product.price, product.expiration_date, category.type,product.picture
                FROM product
                INNER JOIN category ON product.category_id = category.category_id WHERE product.name LIKE ? OR product.product_id LIKE ? ");
                $query->execute(["%$searchterm%","%$searchterm%"]);
                $product = $query->fetchAll(PDO::FETCH_ASSOC);

                // Generate HTML for table rows
                $html = '';
                foreach ($product as $products) {
                    $html .= '<tr>';
                    $html .= '<td>' . $products['product_id'] . '</td>';
                    $html .= '<td><img src="' . htmlspecialchars($products['picture']) . '" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;"></td>';
                    $html .= '<td>' . $products['name'] . '</td>';
                    $html .= '<td>' . $products['type'] . '</td>';
                    $html .= '<td>' . $products['stock'] . '</td>';
                    $html .= '<td>' . $products['price'] . '</td>';
                    $html .= '<td>' . $products['expiration_date'] . '</td>';
                    $html .= '<td>'; // Action column
                    $html .= '<form action="update.php" method="post" style="display: inline;">';
                    $html .= '<input type="hidden" name="id" value="' . $products['product_id'] . '">';
                    $html .= '<button type="submit" class="btn btn-primary btn-sm">Edit</button>';
                    $html .= '</form>';
                    $html .= '<form method="POST" style="display: inline;">';
                    $html .= '<input type="hidden" name="id" value="' . $products['product_id'] . '">';
                    $html .= '<input type="submit" name="delete" class="btn btn-danger btn-sm" value="Delete" onclick="return confirm(\'Are you sure you want to delete this product?\')">';
                    $html .= '</form>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            
                echo $html;
            } else {
                echo json_encode(['error' => 'Database connection failed.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'No search query provided.']);
    }
} 