<?php
session_start();
require_once('classes/database.php');

$con = new Database();

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    // Ensure 'selected_products' session variable is initialized as an array
    if (!isset($_SESSION['selected_products'])) {
        $_SESSION['selected_products'] = [];
    }

    // Debugging: Log session state before action
    error_log("Before action: " . print_r($_SESSION['selected_products'], true));

    switch ($action) {
        case 'add':
            if ($productId > 0 && !in_array($productId, $_SESSION['selected_products'])) {
                $_SESSION['selected_products'][] = $productId;
            }
            break;

        case 'remove':
            if (($key = array_search($productId, $_SESSION['selected_products'])) !== false) {
                unset($_SESSION['selected_products'][$key]);
                // Reindex the array to avoid issues with array handling
                $_SESSION['selected_products'] = array_values($_SESSION['selected_products']);
            }
            break;

        case 'save':
            //$userId = $_SESSION['user_id']; // Replace with actual user ID from session.

            //foreach ($_SESSION['selected_products'] as $productId) {
            //    $query = $con->prepare("INSERT INTO selected_products (user_id, product_id) VALUES (:user_id, :product_id)");
            //    $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
            //    $query->bindParam(':product_id', $productId, PDO::PARAM_INT);
            //    $query->execute();
            //}

            // Clear selected products after saving
            //$_SESSION['selected_products'] = [];
            break;
    }

    // Debugging: Log session state after action
    error_log("After action: " . print_r($_SESSION['selected_products'], true));

    echo json_encode(['status' => 'success']);
    exit;
}
?>