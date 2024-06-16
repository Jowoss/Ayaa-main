<?php
session_start();
require_once('classes/database.php');

$database = new Database();
$con = $database->opencon();

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
                echo json_encode(['status' => 'success']); // Respond with success
                break;

            case 'remove':
                if (isset($_SESSION['selected_products'][$productId])) {
                    unset($_SESSION['selected_products'][$productId]);
                }
                echo json_encode(['status' => 'success']); // Respond with success
                break;

            case 'save':
                $selectedProducts = isset($_POST['selected_products']) ? $_POST['selected_products'] : [];
                $totalPrice = isset($_POST['total_price']) ? $_POST['total_price'] : 0;
                $purchaseDate = isset($_POST['purchase_date']) ? $_POST['purchase_date'] : date('Y-m-d');

                if (!empty($selectedProducts) && is_array($selectedProducts)) {
                    try {
                        // Start transaction
                        $con->beginTransaction();

                        // Insert payment record (only once per transaction)
                        $paymentQuery = $con->prepare("INSERT INTO payment (date_purchase, payment_totalamount) VALUES (:date_purchase, :payment_totalamount)");
                        $paymentQuery->bindParam(':date_purchase', $purchaseDate, PDO::PARAM_STR);
                        $paymentQuery->bindParam(':payment_totalamount', $totalPrice, PDO::PARAM_STR);
                        $paymentQuery->execute();
                        $paymentId = $con->lastInsertId(); // Get the last inserted payment_id

                        // Insert purchased products
                        foreach ($selectedProducts as $productId => $productData) {
                            $quantity = $productData['quantity'];

                            // Insert purchased product
                            $query = $con->prepare("INSERT INTO purchased (product_id, product_quantity, payment_id) VALUES (:product_id, :product_quantity, :payment_id)");
                            $query->bindParam(':product_id', $productId, PDO::PARAM_INT);
                            $query->bindParam(':product_quantity', $quantity, PDO::PARAM_INT);
                            $query->bindParam(':payment_id', $paymentId, PDO::PARAM_INT);
                            $query->execute();

                            // Update product stock
                            $updateQuery = $con->prepare("UPDATE product SET stock = stock - :product_quantity WHERE product_id = :product_id");
                            $updateQuery->bindParam(':product_quantity', $quantity, PDO::PARAM_INT);
                            $updateQuery->bindParam(':product_id', $productId, PDO::PARAM_INT);
                            $updateQuery->execute();
                        }

                        // Commit transaction
                        $con->commit();

                        // Clear the selected products from the session
                        $_SESSION['selected_products'] = [];

                        // Fetch and return purchased data after transaction is saved
                        $sql = "SELECT p.purchased_id, p.product_id, p.product_quantity, py.date_purchase, py.payment_totalamount
                                FROM purchased p
                                LEFT JOIN payment py ON p.payment_id = py.payment_id";
                        $stmt = $con->query($sql);
                        $purchasedData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        echo json_encode(['status' => 'success', 'purchased_data' => $purchasedData]);

                    } catch (PDOException $e) {
                        // Rollback transaction on error
                        $con->rollBack();

                        // Log the error message
                        error_log('PDOException: ' . $e->getMessage());

                        // Return error response as JSON
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                    }
                } else {
                    // Return error response if no selected products
                    echo json_encode(['status' => 'error', 'message' => 'No selected products']);
                }
                break;

            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                break;
        }
    } catch (PDOException $e) {
        error_log('PDOException: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); // Return error response as JSON
    }

    exit;
}
?>
