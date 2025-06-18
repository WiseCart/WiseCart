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

// Gemini API Configuration
$GEMINI_API_KEY = "****"; //מוסתר מטעמי אבטחה
$GEMINI_API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

$userMessage = trim($input['message']);
$userId = isset($input['userId']) ? (int)$input['userId'] : null;

// ========== כל הפונקציות בסדר הנכון ==========

/**
 * יצירת הprompt עבור Gemini
 */
function createGeminiPrompt($userMessage) {
    return "
אתה בוט חכם לניתוח רשימות קניות בעברית. המשתמש שלח לך רשימת קניות, ואתה צריך לנתח אותה ולהחזיר JSON מובנה.

חוקים חשובים:
1. זהה כל מוצר כמוצר נפרד - אפילו אם רשום בשורה אחת
2. מוצרים נפרדים יכולים להיות מופרדים ברווחים, פסיקים, או בשורות נפרדות
3. זהה שגיאות כתיב ונרמל אותן
4. אם לא מצוין מותג ספציפי, השאר את השדה brand ריק
5. כל מוצר צריך להיות פריט נפרד ברשימה

דוגמאות:
'חלב פופקורן' = שני מוצרים: חלב + פופקורן
'חלב, לחם, ביצים' = שלושה מוצרים נפרדים
'2 חלב ו-3 לחם' = שני מוצרים עם כמויות

רשימת הקניות של המשתמש:
\"$userMessage\"

החזר JSON בפורמט הבא בלבד (ללא טקסט נוסף):
{
  \"items\": [
    {
      \"originalText\": \"הטקסט המקורי של המוצר\",
      \"normalizedName\": \"שם מוצר מנורמל\",
      \"brand\": \"\",
      \"size\": \"גודל/כמות (אם צוין)\",
      \"quantity\": 1,
      \"searchTerms\": [\"מילות חיפוש רלוונטיות\"]
    }
  ],
  \"city\": \"עיר שזוהתה או null\",
  \"notes\": \"הערות נוספות מהמשתמש\"
}
";
}

/**
 * ניתוח רשימת קניות באמצעות Gemini AI
 */
function analyzeShoppingListWithGemini($userMessage) {
    global $GEMINI_API_KEY, $GEMINI_API_URL;
    
    $prompt = createGeminiPrompt($userMessage);
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048
        ]
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $GEMINI_API_KEY
            ],
            'content' => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($GEMINI_API_URL, false, $context);
    
    if ($response === false) {
        throw new Exception('Failed to connect to Gemini API');
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('Invalid response from Gemini API');
    }
    
    $geminiText = $result['candidates'][0]['content']['parts'][0]['text'];
    
    // ניתוח תגובת Gemini וחילוץ JSON
    $jsonStart = strpos($geminiText, '{');
    $jsonEnd = strrpos($geminiText, '}') + 1;
    
    if ($jsonStart === false || $jsonEnd === false) {
        throw new Exception('Invalid JSON response from Gemini');
    }
    
    $jsonString = substr($geminiText, $jsonStart, $jsonEnd - $jsonStart);
    $parsedResult = json_decode($jsonString, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to parse Gemini response: ' . json_last_error_msg());
    }
    
    return $parsedResult;
}

/**
 * בניית שאילתת חיפוש מוצר
 */
function buildProductQuery($params) {
    $sql = "SELECT 
                p.Item_Code,
                p.Item_Name,
                p.Manufacture_Name,
                MIN(pr.item_price) as min_price,
                pr.store_ID,
                s.chain as store_name,
                s.address as store_address
            FROM products p
            JOIN pricing pr ON p.Item_Code = pr.Item_Code
            JOIN stores s ON pr.store_ID = s.store_ID
            WHERE 1=1";
    
    $whereConditions = [];
    $bindParams = [];
    $bindTypes = '';
    
    // חיפוש לפי מילות חיפוש
    if (isset($params['terms']) && is_array($params['terms']) && !empty($params['terms'])) {
        $termConditions = [];
        foreach ($params['terms'] as $term) {
            $term = trim($term);
            if (!empty($term)) {
                $termConditions[] = "(p.Item_Name LIKE ? OR p.Manufacture_Name LIKE ?)";
                $bindParams[] = "%$term%";
                $bindParams[] = "%$term%";
                $bindTypes .= 'ss';
            }
        }
        if (!empty($termConditions)) {
            $whereConditions[] = "(" . implode(" OR ", $termConditions) . ")";
        }
    }
    
    // חיפוש לפי מותג
    if (isset($params['brand']) && !empty(trim($params['brand']))) {
        $whereConditions[] = "p.Manufacture_Name LIKE ?";
        $bindParams[] = "%{$params['brand']}%";
        $bindTypes .= 's';
    }
    
    // חיפוש לפי גודל
    if (isset($params['size']) && !empty(trim($params['size']))) {
        $whereConditions[] = "p.Item_Name LIKE ?";
        $bindParams[] = "%{$params['size']}%";
        $bindTypes .= 's';
    }
    
    // אם אין תנאי חיפוש כלל - תחזיר שאילתה בסיסית
    if (empty($whereConditions)) {
        $whereConditions[] = "p.Item_Name IS NOT NULL";
    }
    
    if (!empty($whereConditions)) {
        $sql .= " AND " . implode(" AND ", $whereConditions);
    }
    
    $sql .= " GROUP BY p.Item_Code, pr.store_ID
              ORDER BY min_price ASC
              LIMIT 5";
    
    return [
        'sql' => $sql,
        'types' => $bindTypes,
        'params' => $bindParams
    ];
}

/**
 * חיפוש מוצר בודד במאגר - פשוט ויעיל
 */
function searchSingleProduct($item, $conn) {
    $searchTerms = isset($item['searchTerms']) ? $item['searchTerms'] : [];
    
    foreach ($searchTerms as $term) {
        $term = trim($term);
        if (empty($term)) continue;
        
        // SQL מתוקן עם JOIN נכון
        $sql = "SELECT p.Item_Code, p.Item_Name, p.Manufacture_Name, 
                       pr.item_price, pr.store_ID, s.chain, s.adress
                FROM products p
                JOIN pricing pr ON p.Item_Code = pr.Item_Code  
                JOIN stores s ON pr.store_ID = s.store_ID
                WHERE p.Item_Name LIKE ?
                ORDER BY pr.item_price ASC
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("SQL prepare failed: " . $conn->error);
            continue;
        }
        
        $searchParam = "%$term%";
        $stmt->bind_param('s', $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $found = $result->fetch_assoc();
            error_log("Found product with data: " . json_encode($found));
            return $found;
        }
    }
    
    return null;
}

/**
 * חיפוש מוצרים במאגר הנתונים
 */
function searchProductsInDatabase($analysisResult, $conn) {
    $foundItems = [];
    $notFoundItems = [];
    
    error_log("Processing " . count($analysisResult['items']) . " items from Gemini");
    
    foreach ($analysisResult['items'] as $index => $item) {
        error_log("Processing item #$index: " . json_encode($item));
        
        $searchResult = searchSingleProduct($item, $conn);
        
        if ($searchResult) {
            $foundItems[] = [
                'originalRequest' => $item['originalText'],
                'product' => $searchResult,
                'quantity' => $item['quantity']
            ];
            error_log("Found product #$index: " . $searchResult['Item_Name']);
        } else {
            $notFoundItems[] = $item['originalText'];
            error_log("Not found item #$index: " . $item['originalText']);
        }
    }
    
    error_log("Search complete: " . count($foundItems) . " found, " . count($notFoundItems) . " not found");
    
    return [
        'found' => $foundItems,
        'notFound' => $notFoundItems,
        'city' => $analysisResult['city']
    ];
}

/**
 * מציאת הסופר הזול ביותר
 */
function findBestStore($searchResults, $conn) {
    $foundItems = $searchResults['found'];
    $notFoundItems = $searchResults['notFound'];
    $city = $searchResults['city'];
    
    if (empty($foundItems)) {
        return [
            'foundItems' => [],
            'notFoundItems' => $notFoundItems,
            'bestStore' => null,
            'totalPrice' => 0,
            'city' => $city,
            'storeAddress' => null,
            'storeId' => null
        ];
    }
    
    // עבד על כל המוצרים שנמצאו
    $finalFoundItems = [];
    $totalPrice = 0;
    $firstStore = null;
    
    foreach ($foundItems as $item) {
        $product = $item['product'];
        $quantity = $item['quantity'];
        $itemTotal = ($product['item_price'] ?? 0) * $quantity;
        $totalPrice += $itemTotal;
        
        if (!$firstStore) {
            $firstStore = $product; // שמור את החנות הראשונה
        }
        
        $finalFoundItems[] = [
            'name' => $product['Item_Name'] ?? '',
            'brand' => $product['Manufacture_Name'] ?? '',
            'price' => $product['item_price'] ?? 0,
            'quantity' => $quantity,
            'total' => $itemTotal,
            'itemCode' => $product['Item_Code'] ?? ''
        ];
    }
    
    return [
        'foundItems' => $finalFoundItems,
        'notFoundItems' => $notFoundItems,
        'bestStore' => $firstStore['chain'] ?? '',
        'totalPrice' => $totalPrice,
        'city' => $city,
        'storeAddress' => $firstStore['adress'] ?? '',
        'storeId' => $firstStore['store_ID'] ?? null
    ];
}

// ========== הלוגיקה הראשית ==========

try {
    // שלב 1: שליחת הודעה ל-Gemini לניתוח
    $analysisResult = analyzeShoppingListWithGemini($userMessage);
    
    // ===== הוסף את זה כאן =====
    // דיבוג - לראות מה Gemini מחזיר
    error_log("Gemini Result: " . json_encode($analysisResult));
    
    // בדיקה דינמית - האם יש מוצרים עם המילה במאגר
    $firstSearchTerm = isset($analysisResult['items'][0]['searchTerms'][0]) ? $analysisResult['items'][0]['searchTerms'][0] : 'חלב';
    $testSql = "SELECT Item_Name, Manufacture_Name FROM products WHERE Item_Name LIKE '%$firstSearchTerm%' LIMIT 5";
    $testResult = $conn->query($testSql);
    $testProducts = [];
    while ($row = $testResult->fetch_assoc()) {
        $testProducts[] = $row;
    }
    error_log("Test Products for '$firstSearchTerm': " . json_encode($testProducts));
    // ===== עד כאן =====
    
     // בדיקה ידנית - נראה אם החיפוש הבסיסי עובד
    $manualTest = "SELECT Item_Name, Manufacture_Name FROM products WHERE Item_Name LIKE '%חלב%' LIMIT 3";
    $manualResult = $conn->query($manualTest);
    if ($manualResult) {
        $manualProducts = [];
        while ($row = $manualResult->fetch_assoc()) {
            $manualProducts[] = $row;
        }
        error_log("Manual test result: " . json_encode($manualProducts));
    } else {
        error_log("Manual test failed: " . $conn->error);
    }
    // ===== עד כאן =====
    
    // שלב 2: חיפוש מוצרים במאגר
    $searchResults = searchProductsInDatabase($analysisResult, $conn);
    // שלב 2: חיפוש מוצרים במאגר
    $searchResults = searchProductsInDatabase($analysisResult, $conn);    
    
    // שלב 2: חיפוש מוצרים במאגר
    $searchResults = searchProductsInDatabase($analysisResult, $conn);
    
    // שלב 3: מציאת הסופר הזול ביותר
    $recommendation = findBestStore($searchResults, $conn);
    
    // שלב 4: החזרת התוצאות
    echo json_encode([
        'success' => true,
        'foundItems' => $recommendation['foundItems'],
        'notFoundItems' => $recommendation['notFoundItems'],
        'bestStore' => $recommendation['bestStore'],
        'totalPrice' => $recommendation['totalPrice'],
        'city' => $recommendation['city'],
        'storeAddress' => $recommendation['storeAddress'],
        'itemCount' => count($recommendation['foundItems']),
        'storeId' => $recommendation['storeId'],
        'originalRequest' => $userMessage
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Processing failed',
        'details' => $e->getMessage()
    ]);
}

// סגירת החיבור למאגר
$conn->close();
?>
