<?php
// הפעלת הצגת שגיאות
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// התחלת סשן לשמירת סל הקניות
session_start();

// יצירת מערך סל קניות אם לא קיים
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

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

// טיפול בחיפוש מוצרים
$search_results = [];
$search_term = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $search_query = "%" . $conn->real_escape_string($search_term) . "%";
    
    $sql = "SELECT * FROM products WHERE Item_Name LIKE ? OR Item_Code LIKE ? LIMIT 50";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
    $stmt->close();
}

// טיפול בהוספה לסל הקניות
$message = '';
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($quantity < 1) $quantity = 1;
    
    // בדיקה אם המוצר קיים
    $sql = "SELECT * FROM products WHERE Item_Code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // בדיקה אם המוצר כבר קיים בסל
        $product_exists = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                $_SESSION['cart'][$key]['quantity'] += $quantity;
                $product_exists = true;
                break;
            }
        }
        
        // אם המוצר לא קיים, הוסף אותו לסל
        if (!$product_exists) {
            $_SESSION['cart'][] = [
                'id' => $product_id,
                'name' => $row['Item_Name'],
                'quantity' => $quantity
            ];
        }
        
        $message = "<div style='background:#dff0d8; color:#3c763d; padding:10px; border-radius:5px; margin-bottom:15px;'>
                      המוצר \"" . htmlspecialchars($row['Item_Name']) . "\" התווסף לסל הקניות!
                    </div>";
    }
    $stmt->close();
}

// טיפול בהסרה מסל הקניות
if (isset($_POST['remove_from_cart']) && isset($_POST['cart_index'])) {
    $index = (int)$_POST['cart_index'];
    if (isset($_SESSION['cart'][$index])) {
        $removed_product = $_SESSION['cart'][$index]['name'];
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // סידור מחדש של המערך
        $message = "<div style='background:#fcf8e3; color:#8a6d3b; padding:10px; border-radius:5px; margin-bottom:15px;'>
                      המוצר \"" . htmlspecialchars($removed_product) . "\" הוסר מסל הקניות!
                    </div>";
    }
}

// טיפול בעדכון כמויות בסל הקניות
if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $index => $qty) {
        if (isset($_SESSION['cart'][$index])) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $_SESSION['cart'][$index]['quantity'] = $qty;
            } else {
                unset($_SESSION['cart'][$index]);
            }
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // סידור מחדש של המערך
    $message = "<div style='background:#d9edf7; color:#31708f; padding:10px; border-radius:5px; margin-bottom:15px;'>
                  סל הקניות עודכן בהצלחה!
                </div>";
}

// טיפול בריקון סל הקניות
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    $message = "<div style='background:#fcf8e3; color:#8a6d3b; padding:10px; border-radius:5px; margin-bottom:15px;'>
                  סל הקניות רוקן בהצלחה!
                </div>";
}

// שליפת רשימת המוצרים המלאה (אם לא מחפשים)
if (empty($search_term)) {
    $sql = "SELECT * FROM products LIMIT 16"; // מגביל ל-16 מוצרים בדף הראשי
    $result = $conn->query($sql);
    $all_products = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $all_products[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WiseCart - יצירת סל קניות חכם</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .main-content {
      padding: 100px 20px 60px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .product-card {
      background-color: #fff;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      transition: 0.3s;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
    }
    .product-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transform: translateY(-3px);
    }
    .product-img {
      height: 100px;
      width: 100px;
      margin: 0 auto 15px;
      background-color: #f8f9fa;
      border-radius: 5px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 36px;
      color: #777;
    }
    .product-name {
      font-weight: 500;
      margin-bottom: 10px;
      min-height: 40px;
    }
    .product-code {
      color: #666;
      font-size: 0.8em;
      margin-bottom: 15px;
    }
    .search-container {
      margin-bottom: 30px;
      display: flex;
      gap: 10px;
    }
    .search-container input {
      flex: 1;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    .search-container button, .add-to-cart-btn {
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 12px 20px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.2s;
    }
    .search-container button:hover, .add-to-cart-btn:hover {
      background-color: #2980b9;
    }
    .add-to-cart-btn {
      margin-top: auto;
      width: 100%;
    }
    .quantity-input {
      width: 60px;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 10px;
    }
    .cart-section {
      background-color: #fff;
      border-radius: 8px;
      padding: 20px;
      margin-top: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .cart-table {
      width: 100%;
      border-collapse: collapse;
    }
    .cart-table th, .cart-table td {
      padding: 12px;
      text-align: right;
      border-bottom: 1px solid #eee;
    }
    .cart-table th {
      background-color: #f8f9fa;
    }
    .cart-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    .cart-actions button {
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    .update-cart-btn {
      background-color: #3498db;
      color: white;
    }
    .clear-cart-btn {
      background-color: #e74c3c;
      color: white;
    }
    .save-cart-btn {
      background-color: #2ecc71;
      color: white;
    }
    .tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 1px solid #ddd;
    }
    .tab {
      padding: 15px 25px;
      background-color: #f8f9fa;
      border: none;
      cursor: pointer;
      font-weight: bold;
      border-radius: 5px 5px 0 0;
    }
    .tab.active {
      background-color: #3498db;
      color: white;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .manufacturer {
      color: #666;
      font-size: 0.85em;
      margin: 5px 0 15px;
    }
    @media (max-width: 768px) {
      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      }
      .cart-table thead {
        display: none;
      }
      .cart-table tr {
        margin-bottom: 15px;
        display: block;
        border: 1px solid #ddd;
        border-radius: 5px;
      }
      .cart-table td {
        display: block;
        text-align: left;
        padding-left: 50%;
        position: relative;
      }
      .cart-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 0;
        width: 45%; 
        padding-right: 10px;
        font-weight: bold;
        text-align: right;
      }
    }
  </style>
</head>
<body>
  <header style='background:#fff; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000;'>
    <div style='display:flex; justify-content:space-between; align-items:center; max-width:1200px; margin:0 auto;'>
      <div style='font-size:24px; font-weight:bold; color:#3498db;'>WiseCart</div>
      <nav>
        <ul style='display:flex; list-style:none; gap:20px; padding:0; margin:0;'>
          <li><a href="#" id="home-tab" class="tab-link" style='text-decoration:none; color:#2c3e50;'>דף הבית</a></li>
          <li><a href="#" id="search-tab" class="tab-link" style='text-decoration:none; color:#2c3e50;'>חיפוש מוצרים</a></li>
          <li><a href="#" id="cart-tab" class="tab-link" style='text-decoration:none; color:#2c3e50;'>סל הקניות (<?php echo count($_SESSION['cart']); ?>)</a></li>
          <li><a href="#" id="saved-tab" class="tab-link" style='text-decoration:none; color:#2c3e50;'>רשימות שמורות</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="main-content">
    <!-- הודעות למשתמש -->
    <?php if (!empty($message)) echo $message; ?>
    
    <!-- תפריט לשוניות -->
    <div class="tabs">
      <button class="tab active" data-tab="home">דף הבית</button>
      <button class="tab" data-tab="search">חיפוש מוצרים</button>
      <button class="tab" data-tab="cart">סל הקניות (<?php echo count($_SESSION['cart']); ?>)</button>
      <button class="tab" data-tab="saved">רשימות שמורות</button>
    </div>
    
    <!-- דף הבית -->
    <div id="home-content" class="tab-content active">
      <h1>ברוכים הבאים ל-WiseCart</h1>
      <p>WiseCart מאפשר לך ליצור סלי קניות חכמים, להשוות מחירים בין רשתות שיווק שונות, ולחסוך זמן וכסף בקניות שלך!</p>
      
      <h2>מוצרים פופולריים</h2>
      <div class="products-grid">
        <?php if (!empty($all_products)): ?>
          <?php foreach ($all_products as $product): ?>
            <div class="product-card">
              <div class="product-img">🛒</div>
              <div class="product-name"><?php echo htmlspecialchars($product['Item_Name']); ?></div>
              <div class="product-code">מק"ט: <?php echo htmlspecialchars($product['Item_Code']); ?></div>
              <div class="manufacturer">יצרן: <?php echo htmlspecialchars($product['Manufacture_Name']); ?></div>
              
              <form method="post">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['Item_Code']); ?>">
                <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                <button type="submit" name="add_to_cart" class="add-to-cart-btn">הוסף לסל</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>לא נמצאו מוצרים להצגה.</p>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- חיפוש מוצרים -->
    <div id="search-content" class="tab-content">
      <h1>חיפוש מוצרים</h1>
      
      <form method="get" class="search-container">
        <input type="text" name="search" placeholder="הזן שם מוצר או מק״ט לחיפוש..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit">חפש</button>
      </form>
      
      <?php if (!empty($search_term)): ?>
        <h2>תוצאות חיפוש עבור: "<?php echo htmlspecialchars($search_term); ?>"</h2>
        
        <?php if (!empty($search_results)): ?>
          <div class="products-grid">
            <?php foreach ($search_results as $product): ?>
              <div class="product-card">
                <div class="product-img">🛒</div>
                <div class="product-name"><?php echo htmlspecialchars($product['Item_Name']); ?></div>
                <div class="product-code">מק"ט: <?php echo htmlspecialchars($product['Item_Code']); ?></div>
                <div class="manufacturer">יצרן: <?php echo htmlspecialchars($product['Manufacture_Name']); ?></div>
                
                <form method="post">
                  <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['Item_Code']); ?>">
                  <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                  <button type="submit" name="add_to_cart" class="add-to-cart-btn">הוסף לסל</button>
                </form>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>לא נמצאו תוצאות. נסה לחפש מונח אחר.</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    
    <!-- סל הקניות -->
    <div id="cart-content" class="tab-content">
      <h1>סל הקניות שלך</h1>
      
      <?php if (!empty($_SESSION['cart'])): ?>
        <div class="cart-section">
          <form method="post">
            <table class="cart-table">
              <thead>
                <tr>
                  <th>מוצר</th>
                  <th>מק"ט</th>
                  <th>כמות</th>
                  <th>פעולות</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                  <tr>
                    <td data-label="מוצר"><?php echo htmlspecialchars($item['name']); ?></td>
                    <td data-label="מק&quot;ט"><?php echo htmlspecialchars($item['id']); ?></td>
                    <td data-label="כמות">
                      <input type="number" name="qty[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" class="quantity-input">
                    </td>
                    <td data-label="פעולות">
                      <button type="submit" name="remove_from_cart" value="1" class="clear-cart-btn" style="padding: 5px 10px;" onclick="this.form.cart_index.value=<?php echo $index; ?>">הסר</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            
            <input type="hidden" name="cart_index" value="">
            
            <div class="cart-actions">
              <button type="submit" name="update_cart" class="update-cart-btn">עדכן סל</button>
              <button type="submit" name="clear_cart" class="clear-cart-btn">רוקן סל</button>
              <button type="button" class="save-cart-btn">שמור רשימה</button>
            </div>
          </form>
        </div>
      <?php else: ?>
        <p>סל הקניות שלך ריק.</p>
        <button class="tab" data-tab="search" style="cursor:pointer; margin-top:10px; background:#3498db; color:white; padding:10px 15px; border:none; border-radius:5px;">התחל בקניות</button>
      <?php endif; ?>
    </div>
    
    <!-- רשימות שמורות -->
    <div id="saved-content" class="tab-content">
      <h1>רשימות שמורות</h1>
      <p>בקרוב תוכלו לשמור רשימות קניות לשימוש עתידי. פיצ'ר זה בפיתוח.</p>
    </div>
  </main>

  <footer style='background:#2c3e50; color:#bbb; text-align:center; padding:30px; margin-top:40px;'>
    <p>© 2025 WiseCart - כל הזכויות שמורות</p>
  </footer>

  <script>
    // ניהול לשוניות
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.tab');
      const tabLinks = document.querySelectorAll('.tab-link');
      const tabContents = document.querySelectorAll('.tab-content');
      
      function activateTab(tabName) {
        // הסרת מצב פעיל מכל הלשוניות
        tabs.forEach(tab => tab.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // הפעלת הלשונית הרצויה
        document.querySelector(`.tab[data-tab="${tabName}"]`)?.classList.add('active');
        document.getElementById(`${tabName}-content`)?.classList.add('active');
      }
      
      // אירועי לחיצה על לשוניות
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          activateTab(this.dataset.tab);
        });
      });
      
      // אירועי לחיצה על קישורי תפריט
      tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const tabName = this.id.replace('-tab', '');
          activateTab(tabName);
        });
      });
      
      // הפעלת לשונית על פי פרמטר URL
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('search')) {
        activateTab('search');
      }
    });
  </script>
</body>
</html>
<?php $conn->close(); ?>