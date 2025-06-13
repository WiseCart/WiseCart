<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db   = "isorye_product_prices";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ חיבור נכשל: " . $conn->connect_error);
}

echo "✅ התחברת בהצלחה למסד הנתונים";
