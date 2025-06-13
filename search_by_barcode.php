<?php
// search_by_barcode.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once('includes/config.php');

// פונקציה לרישום לוג
function logDebug($message) {
    error_log("[BARCODE_SEARCH] " . $message);
}

// בדיקה שנשלח פרמטר הברקוד
if (!isset($_GET['code']) || empty(trim($_GET['code']))) {
    logDebug("לא נשלח קוד ברקוד");
    echo json_encode([
        'success' => false,
        'message' => 'לא נשלח קוד ברקוד'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$barcode = trim($_GET['code']);
logDebug("מחפש ברקוד: " . $barcode);

try {
    // בדיקה של חיבור למסד הנתונים
    if (!isset($pdo)) {
        // אם אין חיבור PDO, ננסה ליצור אותו או להשתמש במשתנה אחר
        if (isset($connection)) {
            // אם יש חיבור MySQLi
            $stmt = $connection->prepare("
                SELECT 
                    Item_Code,
                    Item_Name,
                    category_id,
                    is_on_sale
                FROM products 
                WHERE Item_Code = ? 
                LIMIT 1
            ");
            
            $stmt->bind_param("s", $barcode);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
        } else {
            throw new Exception("אין חיבור למסד הנתונים");
        }
    } else {
        // חיבור PDO
        $stmt = $pdo->prepare("
            SELECT 
                Item_Code,
                Item_Name,
                category_id,
                is_on_sale
            FROM products 
            WHERE Item_Code = ? 
            LIMIT 1
        ");
        
        $stmt->execute([$barcode]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    logDebug("תוצאת חיפוש: " . ($product ? "נמצא - " . $product['Item_Name'] : "לא נמצא"));
    
    if ($product) {
        // בדיקה אם קיימת תמונה למוצר
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/WISECART/images/" . $product['Item_Code'] . ".jpg";
        $hasImage = file_exists($imagePath);
        
        logDebug("מוצר נמצא: " . $product['Item_Name'] . ", תמונה: " . ($hasImage ? "כן" : "לא"));
        
        echo json_encode([
            'success' => true,
            'code' => $product['Item_Code'],
            'name' => $product['Item_Name'],
            'category_id' => $product['category_id'],
            'is_on_sale' => $product['is_on_sale'],
            'has_image' => $hasImage,
            'message' => 'המוצר נמצא בהצלחה'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        logDebug("מוצר לא נמצא עבור קוד: " . $barcode);
        echo json_encode([
            'success' => false,
            'message' => 'המוצר לא נמצא במערכת',
            'searched_code' => $barcode
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    logDebug("שגיאה PDO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'שגיאה בחיפוש במסד הנתונים',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    logDebug("שגיאה כללית: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'שגיאה כללית בחיפוש: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>