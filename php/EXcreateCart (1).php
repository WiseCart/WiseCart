<?php
// הפעלת הצגת שגיאות
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$sql = "SELECT product_code, product_name FROM product_prices;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>WiseCart - יצירת סל קניות</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .main-content {
      padding: 100px 20px 60px;
    }
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    .product-card {
      background-color: #ecf0f1;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      transition: 0.3s;
    }
    .product-card:hover {
      background-color: #dfe6e9;
    }
    .product-img {
      height: 80px;
      width: 80px;
      margin: 0 auto 10px;
      background-color: #ddd;
      border-radius: 5px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 24px;
      color: #777;
    }
    .product-name {
      font-weight: 500;
    }
  </style>
</head>
<body>
  <header style='background:#fff; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000;'>
    <div style='display:flex; justify-content:space-between; align-items:center; max-width:1200px; margin:0 auto;'>
      <div style='font-size:24px; font-weight:bold; color:#3498db;'>WiseCart</div>
      <nav>
        <ul style='display:flex; list-style:none; gap:20px; padding:0; margin:0;'>
          <li><a href="#" style='text-decoration:none; color:#2c3e50;'>דף הבית</a></li>
          <li><a href="#" style='text-decoration:none; color:#2c3e50;'>יצירת סל קניות</a></li>
          <li><a href="#" style='text-decoration:none; color:#2c3e50;'>רשימות שמורות</a></li>
          <li><a href="#" style='text-decoration:none; color:#2c3e50;'>איזור אישי</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="main-content">
    <h1 style='text-align:center;'>מוצרים זמינים</h1>
    <div class="products-grid">
      <?php
         if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $name = htmlspecialchars($row['product_name']);
                $product_code = $row['product_code'];
        
                // יצירת נתיב לקובץ התמונה
                $relative_path = "WISECART/images/$product_code.jpg"; // נניח שסיומת .jpg
                $absolute_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative_path;
        
                // בדיקת קיום קובץ
                if (file_exists($absolute_path)) {
                    $img_html = "<img src='/$relative_path' alt='$name' style='max-height:100px;' />";
                } else {
                    $img_html = "🛒";
                }
        
                echo <<<HTML
                <div class="product-card">
                  <div class="product-img">$img_html</div>
                  <div class="product-name">$name</div>
                </div>
                HTML;
            }
        } else {
            echo "<p>לא נמצאו מוצרים</p>";
        }
      $conn->close();
      ?>
    </div>
  </main>

  <footer style='background:#2c3e50; color:#bbb; text-align:center; padding:30px; margin-top:40px;'>
    <p>© 2025 WiseCart - כל הזכויות שמורות</p>
  </footer>
</body>
</html>
