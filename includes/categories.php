<?php
// שליפת קטגוריות
$categories = [];
$cat_result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_id ASC");
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

// שליפת מידע על המוצרים
$sql = "
SELECT 
  p.Item_Code, 
  p.Item_Name, 
  pc.category_id,
  CASE 
    WHEN promos.Item_Code IS NOT NULL THEN 1 
    ELSE 0 
  END AS is_on_sale
FROM products p
JOIN product_categories pc ON p.Item_Code = pc.Item_Code
LEFT JOIN (
    SELECT DISTINCT Item_Code FROM promo_items
) AS promos ON promos.Item_Code = p.Item_Code
";

$result = $conn->query($sql);
?>