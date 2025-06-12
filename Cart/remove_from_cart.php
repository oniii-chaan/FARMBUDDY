<?php
session_start();
require_once "../Scripts/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../Sign-in/login.php");
    exit();
}

if (isset($_GET["id"])) {
    $cart_id = $_GET["id"];
    $user_id = $_SESSION["user_id"];

    try {
        // Remove the item from the cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id");
        $stmt->bindParam(":cart_id", $cart_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

header("Location: cart.php");
exit();
?>