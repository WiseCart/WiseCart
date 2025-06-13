<?php
// הפעלת הצגת שגיאות
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB connection
$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db = "isorye_product_prices";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "<div style='background:#fdd; color:#900; padding:15px; font-family:sans-serif; border:1px solid #900; margin:20px;'>
            <strong>Database connection error:</strong> " . htmlspecialchars($conn->connect_error) . "
          </div>";
    exit;
}