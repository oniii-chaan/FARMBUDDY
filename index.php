<?php
session_start();
require_once "Scripts/db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: Sign-in/login.php");
    exit();
}

// Fetch user details
try {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = :id");
    $stmt->bindParam(":id", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FarmBuddy - Home</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($user["name"]); ?>!</h2>
    <p><a href="Scripts/logout.php">Logout</a></p>
</body>
</html>
