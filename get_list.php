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

$id = $_SESSION['id'];

// זיהוי סוג הפעולה
$action = $_GET['action'] ?? 'get_lists';
$input = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json; charset=utf-8');

try {
    switch ($action) {
        case 'get_lists':
            // הקוד המקורי שלך - שליפת כל הרשימות
            $stmt = $conn->prepare("
                SELECT 
                    list_id, 
                    list_name, 
                    created_at,
                    (SELECT COUNT(*) FROM shopping_list_items sli WHERE sli.list_id = usl.list_id) as item_count
                FROM user_shopping_lists usl 
                WHERE id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $lists = [];
            while ($row = $result->fetch_assoc()) {
                // קבלת כל המוצרים ברשימה
                $itemsSql = "SELECT Item_Code, quantity FROM shopping_list_items WHERE list_id = ?";
                $itemsStmt = $conn->prepare($itemsSql);
                $itemsStmt->bind_param("i", $row['list_id']);
                $itemsStmt->execute();
                $itemsResult = $itemsStmt->get_result();
                
                $items = [];
                while ($itemRow = $itemsResult->fetch_assoc()) {
                    $items[] = $itemRow;
                }
                
                // חישוב מחירים לפי חנויות
                $storePrices = [];
                if (!empty($items)) {
                    // קבלת כל החנויות
                    $storesSql = "SELECT DISTINCT store_ID FROM pricing";
                    $storesResult = $conn->query($storesSql);
                    
                    while ($storeRow = $storesResult->fetch_assoc()) {
                        $storeId = $storeRow['store_ID'];
                        $totalPrice = 0;
                        $availableItems = 0;
                        
                        foreach ($items as $item) {
                            $priceSql = "SELECT item_price FROM pricing WHERE Item_Code = ? AND store_ID = ?";
                            $priceStmt = $conn->prepare($priceSql);
                            $priceStmt->bind_param("si", $item['Item_Code'], $storeId);
                            $priceStmt->execute();
                            $priceResult = $priceStmt->get_result();
                            
                            if ($priceResult->num_rows > 0) {
                                $priceRow = $priceResult->fetch_assoc();
                                $totalPrice += $priceRow['item_price'] * $item['quantity'];
                                $availableItems++;
                            }
                        }
                        
                        if ($availableItems > 0) {
                            $storePrices[] = $totalPrice;
                        }
                    }
                }
                
                // חישוב מחיר זול ביותר וחיסכון
                $cheapestPrice = !empty($storePrices) ? min($storePrices) : 0;
                $expensivePrice = !empty($storePrices) ? max($storePrices) : 0;
                $savings = $expensivePrice - $cheapestPrice;
                
                $lists[] = [
                    'id' => $row['list_id'],
                    'name' => $row['list_name'],
                    'date' => date('d/m/Y', strtotime($row['created_at'])),
                    'item_count' => (int)$row['item_count'],
                    'total_price' => round($cheapestPrice, 2),
                    'savings' => round($savings, 2)
                ];
            }
            
            echo json_encode(['success' => true, 'lists' => $lists]);
            break;

        case 'get_single_list':
            // קבלת רשימה ספציפית
            if (!isset($input['listId'])) {
                http_response_code(400);
                echo json_encode(['error' => 'List ID is required']);
                exit;
            }
            
            $listId = (int)$input['listId'];
            
            // קבלת פרטי הרשימה
            $listSql = "SELECT * FROM user_shopping_lists WHERE list_id = ? AND id = ?";
            $stmt = $conn->prepare($listSql);
            $stmt->bind_param("ii", $listId, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $list = $result->fetch_assoc();
            
            if (!$list) {
                echo json_encode(['error' => 'List not found']);
                exit;
            }
            
            // קבלת המוצרים ברשימה
            $itemsSql = "SELECT sli.*, p.Item_Name, p.Manufacture_Name 
                         FROM shopping_list_items sli
                         JOIN products p ON sli.Item_Code = p.Item_Code
                         WHERE sli.list_id = ?";
            $stmt = $conn->prepare($itemsSql);
            $stmt->bind_param("i", $listId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            
            $list['items'] = $items;
            echo json_encode(['success' => true, 'list' => $list]);
            break;

        case 'update_list_name':
            // עדכון שם רשימה
            if (!isset($input['listId']) || !isset($input['newName'])) {
                http_response_code(400);
                echo json_encode(['error' => 'List ID and new name are required']);
                exit;
            }
            
            $listId = (int)$input['listId'];
            $newName = trim($input['newName']);
            
            if (empty($newName)) {
                echo json_encode(['error' => 'Name cannot be empty']);
                exit;
            }
            
            $sql = "UPDATE user_shopping_lists SET list_name = ? WHERE list_id = ? AND id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $newName, $listId, $id);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Failed to update list name']);
            }
            break;

        case 'update_item_quantity':
            // עדכון כמות מוצר
            if (!isset($input['listId']) || !isset($input['itemCode']) || !isset($input['quantity'])) {
                http_response_code(400);
                echo json_encode(['error' => 'List ID, item code and quantity are required']);
                exit;
            }
            
            $listId = (int)$input['listId'];
            $itemCode = $input['itemCode'];
            $quantity = (int)$input['quantity'];
            
            if ($quantity <= 0) {
                echo json_encode(['error' => 'Quantity must be positive']);
                exit;
            }
            
            // בדיקה שהרשימה שייכת למשתמש
            $checkSql = "SELECT list_id FROM user_shopping_lists WHERE list_id = ? AND id = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("ii", $listId, $id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows === 0) {
                echo json_encode(['error' => 'List not found or access denied']);
                exit;
            }
            
            $sql = "UPDATE shopping_list_items SET quantity = ? WHERE list_id = ? AND Item_Code = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $quantity, $listId, $itemCode);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Failed to update quantity']);
            }
            break;

        case 'remove_item':
            // הסרת מוצר מרשימה
            if (!isset($input['listId']) || !isset($input['itemCode'])) {
                http_response_code(400);
                echo json_encode(['error' => 'List ID and item code are required']);
                exit;
            }
            
            $listId = (int)$input['listId'];
            $itemCode = $input['itemCode'];
            
            // בדיקה שהרשימה שייכת למשתמש
            $checkSql = "SELECT list_id FROM user_shopping_lists WHERE list_id = ? AND id = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("ii", $listId, $id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows === 0) {
                echo json_encode(['error' => 'List not found or access denied']);
                exit;
            }
            
            $sql = "DELETE FROM shopping_list_items WHERE list_id = ? AND Item_Code = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $listId, $itemCode);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Failed to remove item']);
            }
            break;

        case 'save_new_list':
            // שמירת רשימה חדשה
            if (!isset($input['listName']) || !isset($input['items'])) {
                http_response_code(400);
                echo json_encode(['error' => 'List name and items are required']);
                exit;
            }
            
            $listName = trim($input['listName']);
            $items = $input['items'];
            
            if (empty($listName) || !is_array($items) || empty($items)) {
                echo json_encode(['error' => 'Invalid input data']);
                exit;
            }
            
            // התחלת טרנזקציה
            $conn->autocommit(false);
            
            try {
                // יצירת הרשימה
                $listSql = "INSERT INTO user_shopping_lists (id, list_name, created_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($listSql);
                $stmt->bind_param("is", $id, $listName);
                $stmt->execute();
                $listId = $conn->insert_id;
                
                // הוספת המוצרים לרשימה
                $itemSql = "INSERT INTO shopping_list_items (list_id, Item_Code, quantity, created_at) VALUES (?, ?, ?, NOW())";
                $itemStmt = $conn->prepare($itemSql);
                
                foreach ($items as $item) {
                    if (isset($item['Item_Code']) && isset($item['quantity']) && $item['quantity'] > 0) {
                        $quantity = (int)$item['quantity'];
                        $itemStmt->bind_param("isi", $listId, $item['Item_Code'], $quantity);
                        $itemStmt->execute();
                    }
                }
                
                $conn->commit();
                echo json_encode(['success' => true, 'listId' => $listId]);
                
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['error' => 'Failed to save list: ' . $e->getMessage()]);
            }
            
            $conn->autocommit(true);
            break;

        case 'delete_list':
            // מחיקת רשימה שמורה
            if (!isset($input['listId'])) {
                http_response_code(400);
                echo json_encode(['error' => 'List ID is required']);
                exit;
            }
            
            $listId = (int)$input['listId'];
            
            // התחלת טרנזקציה
            $conn->autocommit(false);
            
            try {
                // בדיקה שהרשימה שייכת למשתמש
                $checkSql = "SELECT list_id FROM user_shopping_lists WHERE list_id = ? AND id = ?";
                $stmt = $conn->prepare($checkSql);
                $stmt->bind_param("ii", $listId, $id);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows === 0) {
                    echo json_encode(['error' => 'List not found or access denied']);
                    exit;
                }
                
                // מחיקת הפריטים מהרשימה
                $deleteItemsSql = "DELETE FROM shopping_list_items WHERE list_id = ?";
                $stmt = $conn->prepare($deleteItemsSql);
                $stmt->bind_param("i", $listId);
                $stmt->execute();
                
                // מחיקת הרשימה עצמה
                $deleteListSql = "DELETE FROM user_shopping_lists WHERE list_id = ? AND id = ?";
                $stmt = $conn->prepare($deleteListSql);
                $stmt->bind_param("ii", $listId, $id);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    $conn->commit();
                    echo json_encode(['success' => true]);
                } else {
                    $conn->rollback();
                    echo json_encode(['error' => 'Failed to delete list']);
                }
                
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            
            $conn->autocommit(true);
            break;

        case 'compare_prices':
            // השוואת מחירים
            if (!isset($input['items']) || !is_array($input['items'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Items array is required']);
                exit;
            }
            
            $items = $input['items'];
            
            // קבלת כל החנויות
            $storesSql = "SELECT DISTINCT store_ID, chain, adress FROM stores";
            $result = $conn->query($storesSql);
            
            $stores = [];
            while ($store = $result->fetch_assoc()) {
                $stores[] = [
                    'storeId' => $store['store_ID'],
                    'storeName' => $store['chain'],
                    'address' => $store['adress'],
                    'totalPrice' => 0,
                    'availableItems' => 0,
                    'totalItems' => count($items),
                    'weightedScore' => 0
                ];
            }
            
            // חישוב מחיר משוקלל לכל חנות
            foreach ($stores as &$store) {
                $totalPrice = 0;
                $availableItems = 0;
                
                foreach ($items as $item) {
                    $itemCode = $item['Item_Code'];
                    $quantity = $item['quantity'];
                    
                    // חיפוש מחיר המוצר בחנות הספציפית
                    $priceSql = "SELECT item_price FROM pricing WHERE Item_Code = ? AND store_ID = ?";
                    $stmt = $conn->prepare($priceSql);
                    $stmt->bind_param("si", $itemCode, $store['storeId']);
                    $stmt->execute();
                    $priceResult = $stmt->get_result();
                    
                    if ($priceResult->num_rows > 0) {
                        $priceRow = $priceResult->fetch_assoc();
                        $itemPrice = $priceRow['item_price'] * $quantity;
                        $totalPrice += $itemPrice;
                        $availableItems++;
                    }
                }
                
                $store['totalPrice'] = round($totalPrice, 2);
                $store['availableItems'] = $availableItems;
                
                // חישוב ציון משוקלל (לוקח בחשבון זמינות מוצרים)
                if ($availableItems > 0) {
                    $availabilityRatio = $availableItems / count($items);
                    // פנליה על חוסר זמינות מוצרים
                    $store['weightedScore'] = $totalPrice * (1 + (1 - $availabilityRatio) * 0.5);
                } else {
                    $store['weightedScore'] = PHP_FLOAT_MAX;
                }
            }
            
            // סינון חנויות שיש להן לפחות מוצר אחד
            $validStores = array_filter($stores, function($store) {
                return $store['availableItems'] > 0;
            });
            
            // מיון לפי הציון המשוקלל (מחיר + זמינות)
            usort($validStores, function($a, $b) {
                return $a['weightedScore'] <=> $b['weightedScore'];
            });
            
            echo json_encode([
                'success' => true,
                'stores' => array_values($validStores)
            ]);
            break;

case 'add_to_list':
            // הוספת מוצר לרשימה קיימת
            if (!isset($input['list_id']) || !isset($input['item_code'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'נתונים חסרים']);
                exit;
            }
            
            $list_id = (int)$input['list_id'];
            $item_code = trim($input['item_code']);
            $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
            
            if ($quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'כמות חייבת להיות חיובית']);
                exit;
            }
            
            try {
                // בדיקה שהרשימה שייכת למשתמש
                $stmt = $conn->prepare("SELECT list_id FROM user_shopping_lists WHERE list_id = ? AND id = ?");
                $stmt->bind_param("ii", $list_id, $id);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows === 0) {
                    echo json_encode(['success' => false, 'message' => 'רשימה לא נמצאה או אין גישה']);
                    exit;
                }
                
                // בדיקה אם המוצר כבר קיים ברשימה
                $stmt = $conn->prepare("SELECT Item_Code FROM shopping_list_items WHERE list_id = ? AND Item_Code = ?");
                $stmt->bind_param("is", $list_id, $item_code);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    // עדכון הכמות אם המוצר כבר קיים
                    $stmt = $conn->prepare("UPDATE shopping_list_items SET quantity = quantity + ? WHERE list_id = ? AND Item_Code = ?");
                    $stmt->bind_param("iis", $quantity, $list_id, $item_code);
                } else {
                    // הוספת מוצר חדש
                    $stmt = $conn->prepare("INSERT INTO shopping_list_items (list_id, Item_Code, quantity, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->bind_param("isi", $list_id, $item_code, $quantity);
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'המוצר נוסף בהצלחה']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'שגיאה בהוספת המוצר']);
                }
                
            } catch (Exception $e) {
                error_log("Error adding product to list: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'שגיאה במסד הנתונים']);
            }
            break;

        case 'create_list':
            // יצירת רשימה חדשה
            if (!isset($input['list_name']) || empty(trim($input['list_name']))) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'שם הרשימה לא יכול להיות ריק']);
                exit;
            }
            
            $list_name = trim($input['list_name']);
            
            // בדיקה שאורך השם תקין
            if (strlen($list_name) > 100) {
                echo json_encode(['success' => false, 'message' => 'שם הרשימה ארוך מדי']);
                exit;
            }
            
            try {
                // בדיקה שלא קיימת רשימה עם אותו שם למשתמש
                $stmt = $conn->prepare("SELECT list_id FROM user_shopping_lists WHERE id = ? AND list_name = ?");
                $stmt->bind_param("is", $id, $list_name);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    echo json_encode(['success' => false, 'message' => 'כבר קיימת רשימה עם שם זה']);
                    exit;
                }
                
                // יצירת הרשימה החדשה
                $stmt = $conn->prepare("INSERT INTO user_shopping_lists (id, list_name, created_at) VALUES (?, ?, NOW())");
                $stmt->bind_param("is", $id, $list_name);
                
                if ($stmt->execute()) {
                    $new_list_id = $conn->insert_id;
                    echo json_encode([
                        'success' => true, 
                        'message' => 'הרשימה נוצרה בהצלחה',
                        'list_id' => $new_list_id,
                        'list_name' => $list_name
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'שגיאה ביצירת הרשימה']);
                }
                
            } catch (Exception $e) {
                error_log("Error creating shopping list: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'שגיאה במסד הנתונים']);
            }
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'שגיאה: ' . $e->getMessage()]);
}

$conn->close();
?>