<?php
session_start();
require_once('classes/database.php');

$con = new Database();

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if (!isset($_SESSION['selected_products'])) {
        $_SESSION['selected_products'] = [];
    }

    try {
        switch ($action) {
            case 'add':
                if ($productId > 0 && !array_key_exists($productId, $_SESSION['selected_products'])) {
                    $_SESSION['selected_products'][$productId] = 1; // Default quantity to 1
                }
                break;

            case 'remove':
                if (isset($_SESSION['selected_products'][$productId])) {
                    unset($_SESSION['selected_products'][$productId]);
                }
                break;

            case 'save':
                $selectedProducts = $_POST['selected_products'];
                if (!empty($selectedProducts)) {
                    $con->beginTransaction();
                    foreach ($selectedProducts as $productId => $quantity) {
                        $quantity = (int)$quantity;
                        if ($quantity > 0) {
                            // Insert purchased product
                            $query = $con->prepare("INSERT INTO purchased (product_id, product_quantity) VALUES (:product_id, :product_quantity)");
                            $query->bindParam(':product_id', $productId, PDO::PARAM_INT);
                            $query->bindParam(':product_quantity', $quantity, PDO::PARAM_INT);
                            $query->execute();

                            // Update product stock
                            $updateQuery = $con->prepare("UPDATE product SET stock = stock - :product_quantity WHERE product_id = :product_id");
                            $updateQuery->bindParam(':product_quantity', $quantity, PDO::PARAM_INT);
                            $updateQuery->bindParam(':product_id', $productId, PDO::PARAM_INT);
                            $updateQuery->execute();
                        }
                    }
                    $con->commit();
                    $_SESSION['selected_products'] = [];
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No selected products']);
                }
                break;
        }
    } catch (PDOException $e) {
        $con->rollBack();
        error_log('PDOException: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    exit;
}
?>
 