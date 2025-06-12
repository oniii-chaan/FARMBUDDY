<?php
session_start();
require_once "../Scripts/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../Sign-in/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $product_id = $_POST["product_id"];
    $quantity = $_POST["quantity"];

    try {
        // Check if product is already in the cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->execute();
        $existing_cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_cart_item) {
            // Update quantity
            $new_quantity = $existing_cart_item["quantity"] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE id = :cart_id");
            $stmt->bindParam(":quantity", $new_quantity);
            $stmt->bindParam(":cart_id", $existing_cart_item["id"]);
            $stmt->execute();
        } else {
            // Insert new cart item
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->execute();
        }

        header("Location: cart.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
