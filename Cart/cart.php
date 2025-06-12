<?php
session_start();
require_once "../Scripts/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../Sign-in/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch cart items for the logged-in user
try {
    $stmt = $conn->prepare("
        SELECT c.id, p.name, p.price, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :user_id
    ");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Cart - FarmBuddy</title>
</head>
<body>
    <h2>Your Shopping Cart</h2>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item["name"]); ?></td>
                    <td><?php echo number_format($item["price"], 2); ?></td>
                    <td><?php echo $item["quantity"]; ?></td>
                    <td>
                        <a href="remove_from_cart.php?id=<?php echo $item['id']; ?>">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <a href="../Checkout/checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
    <br><br>
    <a href="../index.php">Back to Home</a>
</body>
</html>
