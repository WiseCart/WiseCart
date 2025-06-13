<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// הפעלת הצגת שגיאות
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB connection
$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db = "isorye_product_prices";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection error', 'details' => $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['userId', 'items', 'totalPrice', 'storeName'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

$userId = (int)$input['userId'];
$items = $input['items'];
$totalPrice = (float)$input['totalPrice'];
$storeId = isset($input['storeId']) ? (int)$input['storeId'] : null;
$storeName = $input['storeName'];
$storeAddress = $input['storeAddress'] ?? '';

// Validate user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

try {
    $conn->begin_transaction();
    
    // יצירת רשימה חדשה בטבלת user_shopping_lists
    $listName = "רשימת קניות בוט - " . date('d/m/Y H:i');
    
    $stmt = $conn->prepare("
        INSERT INTO user_shopping_lists 
        (user_id, list_name, total_price, recommended_store_id, recommended_store_name, recommended_store_address, completed_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param('isdsss', $userId, $listName, $totalPrice, $storeId, $storeName, $storeAddress);
    $stmt->execute();
    
    $listId = $conn->insert_id;
    
    // הוספת פריטים לטבלת shopping_list_items
    $stmt = $conn->prepare("
        INSERT INTO shopping_list_items 
        (list_id, Item_Code, Item_Name, Manufacture_Name, quantity, unit_price, total_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($items as $item) {
        $itemCode = $item['itemCode'] ?? null;
        $itemName = $item['name']; // Item_Name
        $manufactureName = $item['brand'] ?? ''; // Manufacture_Name
        $quantity = (int)$item['quantity'];
        $unitPrice = (float)$item['price'];
        $totalPrice = $unitPrice * $quantity; // הוספת total_price
        
        $stmt->bind_param('isssids', 
            $listId, $itemCode, $itemName, $manufactureName, $quantity, $unitPrice, $totalPrice
        );
        $stmt->execute();
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'listId' => $listId,
        'message' => 'הרשימה נשמרה בהצלחה'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to save shopping list',
        'details' => $e->getMessage()
    ]);
}

$conn->close();
?>