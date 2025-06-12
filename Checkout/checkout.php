<?php
session_start();
require_once "../Scripts/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../Sign-in/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

try {
    // Fetch cart items
    $stmt = $conn->prepare("
        SELECT c.id, p.name, p.price, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :user_id
    ");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        echo "<p>Your cart is empty.</p>";
        echo '<a href="../index.php">Back to Home</a>';
        exit();
    }

    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item["price"] * $item["quantity"];
    }

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (:user_id, :total)");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":total", $total);
    $stmt->execute();
    $order_id = $conn->lastInsertId();

    // Insert order items
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
        $stmt->bindParam(":order_id", $order_id);
        $stmt->bindParam(":product_id", $item["id"]);
        $stmt->bindParam(":quantity", $item["quantity"]);
        $stmt->bindParam(":price", $item["price"]);
        $stmt->execute();
    }

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    echo "<p>Order placed successfully! Your total is $" . number_format($total, 2) . ".</p>";
    echo '<a href="../index.php">Back to Home</a>';
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
