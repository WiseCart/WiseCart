<?php
header('Content-Type: application/json');

// חיבור למסד הנתונים
$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db = "isorye_product_prices";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'שגיאה בחיבור למסד הנתונים']);
    exit;
}

// קבלת מילת החיפוש
$search = $_GET['q'] ?? '';
if (empty($search) || strlen($search) < 2) {
    echo json_encode([]);
    exit;
}

// חיפוש מוצרים - רק לפי שם המוצר
$search_param = '%' . $search . '%';
$sql = "SELECT Item_Code, Item_Name 
        FROM products 
        WHERE Item_Name LIKE ? 
        ORDER BY Item_Name ASC 
        LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // בדיקת קיום תמונה
    $image_path = $_SERVER['DOCUMENT_ROOT'] . '/WISECART/images/' . $row['Item_Code'] . '.jpg';
    $has_image = file_exists($image_path);
    
    $products[] = [
        'code' => $row['Item_Code'],
        'name' => $row['Item_Name'],
        'has_image' => $has_image
    ];
}

echo json_encode($products);
$conn->close();
?>