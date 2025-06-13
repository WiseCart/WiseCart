<?php
/**
 * מעבד טופס צור קשר - WiseCart
 * גרסה פשוטה ללא שדות חסרים
 */

// הצגת שגיאות לבדיקה
error_reporting(E_ALL);
ini_set('display_errors', 1);

// הגדרת headers
header('Content-Type: application/json; charset=utf-8');

// בדיקת method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'error' => 'Method not allowed'
    ]);
    exit;
}

// הגדרות מסד נתונים - עדכן את הפרטים!
$DB_HOST = 'localhost';
$DB_NAME = 'isorye_product_prices';
$DB_USER = 'isorye_WISECART';  // שנה לשם המשתמש הנכון שלך
$DB_PASS = 'NOMbys230799!';

try {
    // חיבור למסד נתונים
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", 
        $DB_USER, 
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    // קבלת נתונים
    $input = json_decode(file_get_contents('php://input'), true);
    
    // אם JSON לא עבד, נסה $_POST
    if (!$input) {
        $input = $_POST;
    }
    
    // בדיקה שהנתונים התקבלו
    if (!$input) {
        throw new Exception('לא התקבלו נתונים');
    }
    
    // פונקציות עזר
    function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    function validatePhone($phone) {
        return preg_match('/^05\d([- ]?)(\d{7})$/', $phone);
    }
    
    // איסוף ועיבוד נתונים
    $fullName = sanitizeInput($input['name'] ?? '');
    $email = sanitizeInput($input['email'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');
    $subject = sanitizeInput($input['subject'] ?? '');
    $message = sanitizeInput($input['message'] ?? '');
    
    // מערך לשגיאות
    $errors = [];
    
    // וולידציה
    if (empty($fullName)) {
        $errors[] = 'נא להזין שם מלא';
    } elseif (strlen($fullName) < 2) {
        $errors[] = 'השם קצר מדי';
    }
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'נא להזין כתובת דוא"ל תקינה';
    }
    
    if (!empty($phone) && !validatePhone($phone)) {
        $errors[] = 'נא להזין מספר טלפון תקין';
    }
    
    if (empty($subject)) {
        $errors[] = 'נא לבחור נושא';
    }
    
    if (empty($message)) {
        $errors[] = 'נא להזין תוכן הודעה';
    } elseif (strlen(trim($message)) < 10) {
        $errors[] = 'ההודעה קצרה מדי (מינימום 10 תווים)';
    } elseif (strlen(trim($message)) > 1000) {
        $errors[] = 'ההודעה ארוכה מדי (מקסימום 1000 תווים)';
    }
    
    // אם יש שגיאות
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
    
    // שאילתה פשוטה - רק עם השדות שקיימים
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages 
        (full_name, email, phone, subject, message, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    // הרצת השאילתה - רק עם הנתונים הבסיסיים
    $result = $stmt->execute([
        $fullName,
        $email,
        $phone ?: null,
        $subject,
        $message
    ]);
    
    if ($result) {
        $messageId = $pdo->lastInsertId();
        
        // תגובה מוצלחת
        echo json_encode([
            'success' => true,
            'message' => 'ההודעה נשלחה בהצלחה!',
            'messageId' => $messageId
        ]);
        
    } else {
        throw new Exception('שגיאה בשמירת ההודעה');
    }
    
} catch (PDOException $e) {
    // שגיאת מסד נתונים
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'שגיאת מסד נתונים: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    // שגיאה כללית
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'שגיאה: ' . $e->getMessage()
    ]);
}
?>