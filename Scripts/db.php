<?php
$host = "localhost";
$port = "5432"; // Default PostgreSQL port
$dbname = "farmbuddy_db"; // Your database name
$user = "postgres"; // Your PostgreSQL username
$password = "admin"; // Your PostgreSQL password

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
