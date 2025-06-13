<?php
// בדיקה אם נשלח קוד מוצר
$code = $_GET['code'] ?? '';
if (!$code) {
    die("קוד מוצר חסר.");
}

// שליפת פרטי המוצר
$product_sql = "SELECT Item_Code, Item_Name, Manufacture_Name FROM products WHERE Item_Code = ?";
$stmt = $conn->prepare($product_sql);
$stmt->bind_param("s", $code);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    die("מוצר לא נמצא.");
}
$product = $product_result->fetch_assoc();
$image_path = "/WISECART/images/{$product['Item_Code']}.jpg";
$image_full_path = $_SERVER['DOCUMENT_ROOT'] . $image_path;
$image_exists = file_exists($image_full_path);

// שליפת פרטי מבצע עבור המוצר הנוכחי - השאילתה המתוקנת
$price_sql = "
SELECT 
  s.chain, 
  p.item_price,
  (
    SELECT prm.description
    FROM promo_items pi
    JOIN promotions prm ON prm.promotion_id = pi.promotion_id
    WHERE pi.Item_Code = p.Item_Code 
      AND prm.store_ID = p.store_ID
      AND (prm.end_date IS NULL OR prm.end_date >= CURDATE())
    ORDER BY prm.end_date DESC
    LIMIT 1
  ) AS promo_description,
  (
    SELECT prm.end_date
    FROM promo_items pi
    JOIN promotions prm ON prm.promotion_id = pi.promotion_id
    WHERE pi.Item_Code = p.Item_Code 
      AND prm.store_ID = p.store_ID
      AND (prm.end_date IS NULL OR prm.end_date >= CURDATE())
    ORDER BY prm.end_date DESC
    LIMIT 1
  ) AS end_date
FROM pricing p
JOIN stores s ON p.store_ID = s.store_ID
WHERE p.Item_Code = ?
ORDER BY p.item_price ASC
";

$stmt = $conn->prepare($price_sql);
if (!$stmt) {
    die("שגיאה ב-prepared statement: " . $conn->error);
}
$stmt->bind_param("s", $code);
$stmt->execute();
$price_result = $stmt->get_result();

// חישוב פער באחוזים
$min_price = PHP_FLOAT_MAX;
$max_price = 0;
$prices_array = [];

if ($price_result && $price_result->num_rows > 0) {
    // שמירת התוצאות במערך כדי להשתמש בהן פעמיים
    while ($row = $price_result->fetch_assoc()) {
        $prices_array[] = $row;
        $price = (float)$row['item_price'];
        if ($price > 0) {
            $min_price = min($min_price, $price);
            $max_price = max($max_price, $price);
        }
    }
}
?>