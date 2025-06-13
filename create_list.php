<?php
session_start();

// בדיקה שהמשתמש מחובר
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'משתמש לא מחובר']);
    exit;
}

// חיבור למסד הנתונים
$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db = "isorye_product_prices";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'שגיאת חיבור למסד הנתונים']);
    exit;
}
$conn->set_charset('utf8mb4');

// בדיקה שהבקשה היא POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'שיטת בקשה לא נתמכת']);
    exit;
}

// קבלת הנתונים מהבקשה
$input = json_decode(file_get_contents('php://input'), true);
$list_name = trim($input['list_name'] ?? '');

// ולידציה
if (empty($list_name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'שם הרשימה חובה']);
    exit;
}

if (strlen($list_name) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'שם הרשימה ארוך מדי (מקסימום 100 תווים)']);
    exit;
}

$id = $_SESSION['id'];

try {
    // הוספת הרשימה החדשה למסד הנתונים
    $stmt = $conn->prepare("INSERT INTO shopping_lists (list_name,id, list_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("si", $list_name, $id);
    
    if ($stmt->execute()) {
        $new_list_id = $conn->insert_id;
        
        // החזרת תגובה מוצלחת
        echo json_encode([
            'success' => true, 
            'message' => 'הרשימה נוצרה בהצלחה',
            'list_id' => $new_list_id,
            'list_name' => $list_name,
            'list_date' => date('d/m/Y')
        ]);
    } else {
        throw new Exception('שגיאה ביצירת הרשימה');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'שגיאה ביצירת הרשימה: ' . $e->getMessage()]);
}

$conn->close();
?>