<?php
session_start();
require_once('classes/database.php');

$con = new Database();

// Fetch selected products from session
$selectedProducts = $_SESSION['selected_products'];

$htmlSelectedProduct = '';

if (count($selectedProducts) > 0) {
    $htmlSelectedProduct = '<tbody id="selectedProductsTable">';
    foreach ($selectedProducts as $product) {
        $productDetails = $con->fetchProduct($product['product_id']);
        if ($productDetails) {
            $htmlSelectedProduct .= '<tr>';
            $htmlSelectedProduct .= '<td>' . $product['product_id'] . '</td>';
            $htmlSelectedProduct .= '<td>' . $product['name'] . '</td>';
            $htmlSelectedProduct .= '<td>';
            $htmlSelectedProduct .= '<input type="number" id="quantity_' . $product['product_id'] . '" value="' . $product['quantity'] . '" min="1" max="' . $product['stock'] . '">';
            $htmlSelectedProduct .= '</td>';
            $htmlSelectedProduct .= '<td>' . $product['price'] . '</td>';
            $htmlSelectedProduct .= '<td>';
            $htmlSelectedProduct .= '<button class="btn btn-danger" onclick="removeProduct(' . $product['product_id'] . ')">Remove</button>';
            $htmlSelectedProduct .= '</td>';
            $htmlSelectedProduct .= '</tr>';
        }
    }
    $htmlSelectedProduct .= '</tbody>';
} else {
    $htmlSelectedProduct = '<tbody id="selectedProductsTable"><tr><td colspan="5">No selected products</td></tr></tbody>';
}

echo $htmlSelectedProduct;
?>