<?php
// search_results.php - עמוד תוצאות חיפוש עם מחירים
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// חיבור למסד הנתונים
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

// קבלת מילת החיפוש
$search_query = $_GET['q'] ?? '';
$search_query = trim($search_query);

if (empty($search_query)) {
    header('Location: EXcreateCart.php');
    exit;
}

// חיפוש מוצרים עם מחירים ואיחוד קטגוריות
$search_param = '%' . $search_query . '%';
$sql = "
SELECT 
  p.Item_Code, 
  p.Item_Name, 
  GROUP_CONCAT(DISTINCT c.category_name SEPARATOR ', ') AS categories,
  MIN(pr.item_price) AS min_price,
  MAX(pr.item_price) AS max_price,
  COUNT(DISTINCT pr.store_ID) AS store_count,
  CASE 
    WHEN promos.Item_Code IS NOT NULL THEN 1 
    ELSE 0 
  END AS is_on_sale
FROM products p
JOIN product_categories pc ON p.Item_Code = pc.Item_Code
JOIN categories c ON pc.category_id = c.category_id
LEFT JOIN pricing pr ON p.Item_Code = pr.Item_Code AND pr.item_price > 0
LEFT JOIN (
    SELECT DISTINCT Item_Code FROM promo_items
) AS promos ON promos.Item_Code = p.Item_Code
WHERE p.Item_Name LIKE ?
GROUP BY p.Item_Code, p.Item_Name
HAVING min_price IS NOT NULL
ORDER BY min_price ASC, p.Item_Name ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("שגיאה בהכנת השאילתה: " . $conn->error);
}

if (!$stmt->bind_param("s", $search_param)) {
    die("שגיאה בקשירת הפרמטרים: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("שגיאה בביצוע השאילתה: " . $stmt->error);
}

$result = $stmt->get_result();
if (!$result) {
    die("שגיאה בקבלת התוצאות: " . $stmt->error);
}

$products_found = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תוצאות חיפוש עבור "<?= htmlspecialchars($search_query) ?>" - WiseCart</title>
     <link rel="stylesheet" href="CSS/styles.css">
    <style>
        /* Main Content */
        .main-content {
            padding: 100px 0 60px;
        }
        
        /* כפתור חזרה */
        .back-button-container {
            margin-bottom: 20px;
        }
        
        .back-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.2);
        }
        
        .back-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .back-icon {
            font-size: 16px;
            transition: transform 0.3s;
        }
        
        .back-button:hover .back-icon {
            transform: translateX(3px);
        }
        
        /* Search Results Header */
        .search-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .search-header h1 {
            font-size: 28px;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .search-info {
            color: #666;
            font-size: 16px;
        }
        
        .search-query {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .sort-info {
            color: var(--success-color);
            font-size: 14px;
            margin-top: 8px;
            font-weight: 500;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            position: relative;
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }
        
        .add-to-cart-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            border: 2px solid var(--primary-color);
            border-radius: 50%;
            width: 35px;
            height: 35px;
            font-size: 16px;
            cursor: pointer;
            z-index: 3;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.2);
        }
        
        .add-to-cart-icon:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }
        
        .product-img {
            height: 100px;
            width: 100px;
            margin: 0 auto 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 32px;
            color: #adb5bd;
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }
        
        .product-img img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 6px;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
            border-color: var(--primary-color);
            background-color: #f0f8ff;
        }
        
        .product-name {
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--dark-color);
            font-size: 15px;
            line-height: 1.3;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        /* Price Range Styling */
        .price-range {
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 8px 0;
        }
        
        .stores-count {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            margin-top: 4px;
            font-style: italic;
        }
        
        .best-price-badge {
            position: absolute;
            top: -5px;
            left: 1px;
            background: linear-gradient(135deg, var(--success-color), #229954);
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 10px;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .product-category {
            font-size: 12px;
            color: #6c757d;
            background-color: #f8f9fa;
            padding: 4px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 8px;
        }
        
        .tag-sale {
            position: absolute;
            top: 35px;
            left: 0;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 0 0 12px 0;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .tag-sale::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            width: 0;
            height: 0;
            border-left: 5px solid #a93226;
            border-bottom: 5px solid transparent;
        }
        
        /* במקרה שאין תגית "המחיר הטוב ביותר" */
        .product-card:not(.best-price) .tag-sale {
            top: 0;
        }
        
        .product-link {
            display: block;
            text-decoration: none;
            color: inherit;
            height: 100%;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        
        .no-results h2 {
            font-size: 24px;
            color: var(--dark-color);
            margin-bottom: 15px;
        }
        
        .no-results p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .no-results .btn {
            margin: 0 10px;
        }
        
        /* רספונסיביות */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            nav ul {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .product-card {
                padding: 15px;
            }
            
            .product-img {
                height: 80px;
                width: 80px;
                font-size: 24px;
            }
            
            .product-name {
                font-size: 14px;
                min-height: 35px;
            }
            
            .search-header {
                padding: 20px 15px;
            }
            
            .search-header h1 {
                font-size: 24px;
            }
            
            .price-values {
                flex-direction: column;
                gap: 4px;
            }
            
            .min-price, .max-price {
                flex: none;
                width: 100%;
            }
            
            .price-separator {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .product-card {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- כפתור חזרה -->
            <div class="back-button-container">
                <button class="back-button" onclick="goBack()">
                    <span class="back-icon">⮕</span>
                    חזרה
                </button>
            </div>

            <!-- Search Results Header -->
            <div class="search-header">
                <h1>תוצאות חיפוש</h1>
                <div class="search-info">
                    נמצאו <strong><?= $products_found ?></strong> מוצרים עבור 
                    "<span class="search-query"><?= htmlspecialchars($search_query) ?></span>"
                </div>
                <?php if ($products_found > 0): ?>
                    <div class="sort-info">
                        ✓ מוצרים מסודרים מהמחיר הזול ביותר ליקר ביותר
                    </div>
                <?php endif; ?>
            </div>

            <!-- Products Grid -->
            <?php if ($products_found > 0): ?>
                <div class="products-grid">
                    <?php 
                    $first_product = true;
                    while ($row = $result->fetch_assoc()): 
                        $code = $row['Item_Code'];
                        $name = htmlspecialchars($row['Item_Name']);
                        $categories = htmlspecialchars($row['categories']);
                        $min_price = $row['min_price'];
                        $max_price = $row['max_price'];
                        $store_count = $row['store_count'];
                        $relative_path = "WISECART/images/$code.jpg";
                        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative_path;
                        $img_html = file_exists($absolute_path)
                            ? "<img src='/$relative_path' alt='$name' />"
                            : "🛒";
                    ?>
                        
                        <div class="product-card<?php if ($first_product): ?> best-price<?php endif; ?>">
                            <button class="add-to-cart-icon" onclick="addToCart('<?= $code ?>', '<?= $name ?>'); event.stopPropagation();">+🛒</button>
                            
                            <?php if ($first_product): ?>
                                <div class="best-price-badge">המחיר הטוב ביותר</div>
                                <?php $first_product = false; ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($row['is_on_sale']) && (int)$row['is_on_sale'] === 1): ?>
                                <div class="tag-sale">במבצע!</div>
                            <?php endif; ?>
                            
                            <a href="product.php?code=<?= $code ?>" class="product-link">
                                <div class="product-img"><?= $img_html ?></div>
                                <div class="product-name"><?= $name ?></div>
                                
                                <!-- Price Range -->
                                <div class="price-range">
                                    <?= number_format($min_price, 2) ?>-<?= number_format($max_price, 2) ?> ₪
                                </div>
                                <div class="stores-count">
                                    <?= $store_count ?> <?= $store_count == 1 ? 'סניף' : 'סניפים' ?>
                                </div>
                                
                                <div class="product-category"><?= $categories ?></div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <h2>לא נמצאו מוצרים</h2>
                    <p>לא נמצאו מוצרים עבור החיפוש "<strong><?= htmlspecialchars($search_query) ?></strong>"</p>
                    <a href="EXcreateCart.php" class="btn btn-primary">חזרה לעמוד הראשי</a>
                    <button class="btn btn-outline" onclick="history.back()">חזרה אחורה</button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
    <script src="js/script.js"></script>
    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = 'EXcreateCart.php';
            }
        }
        
        let cart = [];
        function addToCart(code, name) {
            cart.push({ code, name });
            document.getElementById('cart-count').textContent = cart.length;
            
            // הודעת הצלחה משופרת
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--success-color);
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
                z-index: 10000;
                font-weight: 500;
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            notification.innerHTML = `✓ ${name} נוסף לסל הקניות`;
            document.body.appendChild(notification);
            
            // אנימציה
            setTimeout(() => notification.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }
    </script>
    <?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>