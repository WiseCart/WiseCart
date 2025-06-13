<?php
session_start();
require_once('includes/config.php');
require_once('includes/categories.php');
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>יצירת סל קניות - WiseCart</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link rel="stylesheet" href="CSS/styles.css">
  <style>
/* Main Content */
.main-content {
    padding: 100px 0 60px;
}

.page-title {
    margin-bottom: 30px;
    text-align: center;
}

.page-title h1 {
    font-size: 32px;
    color: var(--dark-color);
    margin-bottom: 10px;
}

.page-title p {
    color: #666;
    max-width: 800px;
    margin: 0 auto;
}
        
/* Shopping Cart Creation */

.cart-creation {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 30px;
}

.cart-header {
    background-color: var(--primary-color);
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-header h2 {
    font-size: 20px;
}

.cart-actions {
    display: flex;
    gap: 10px;
}

.cart-content {
    padding: 25px;
}

.search-add {
    position: relative;
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.search-add input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    outline: none;
}

.search-add input:focus {
    border-color: var(--primary-color);
}

/* אזור סורק הברקוד - עדכון חשוב */
#reader {
    width: 100%;
    max-width: 400px;
    margin: 20px auto;
    border: 2px solid var(--primary-color);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    background: #f8f9fa;
}

.scanner-controls {
    text-align: center;
    margin: 15px 0;
}

.scanner-status {
    background: #e3f2fd;
    color: #1976d2;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0;
    text-align: center;
    font-weight: 500;
}

/* תוצאות החיפוש */
.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 100px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-manufacturer {
    font-size: 14px;
    color: #7f8c8d;
}

.search-result-code {
    font-size: 12px;
    color: #95a5a6;
    margin-top: 2px;
}

.search-result-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
}

.search-result-img {
    width: 40px;
    height: 40px;
    background-color: #f8f9fa;
    border-radius: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    color: #adb5bd;
    flex-shrink: 0;
}

.search-result-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 4px;
}

.search-result-content {
    flex: 1;
}

.search-result-name {
    font-weight: 500;
    color: #2c3e50;
    margin: 0;
}

/* כשהחיפוש פעיל */
.search-add.active input {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.search-add.active .search-results {
    display: block;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

/* הודעת "לא נמצאו תוצאות" */
.no-results {
    padding: 20px;
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
}

/* הדגשת טקסט בחיפוש */
.highlight {
    background-color: #fff3cd;
    font-weight: bold;
}

.product-categories {
    margin-bottom: 30px;
}

.category-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.category-title h3 {
    color: var(--dark-color);
}

.category-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap; 
}

.category-tab {
    padding: 8px 16px;
    background-color: #f1f1f1;
    border-radius: 20px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s;
}

.category-tab:hover {
    background-color: #e1e1e1;
}

.category-tab.active {
    background-color: var(--primary-color);
    color: white;
}

.category-tab.category-expand {
background-color: var(--primary-color);
color: white;
font-weight: bold;
border-radius: 50%;
width: 36px;
height: 36px;
display: flex;
align-items: center;
justify-content: center;
padding: 0;
font-size: 20px;
flex-shrink: 0;
transition: all 0.3s;
}

.category-tab.category-expand {
background-color: var(--primary-color);
color: white;
font-weight: bold;
border-radius: 20px;
min-width: 36px;
height: 36px;
padding: 0 16px;
display: flex;
align-items: center;
justify-content: center;
font-size: 18px;
line-height: 1;
flex-shrink: 0;
transition: all 0.3s;
cursor: pointer;
}

.cart-icon {
position: relative;
font-size: 24px;
}

.cart-count {
position: absolute;
top: -6px;
right: -8px;
background: red;
color: white;
border-radius: 50%;
font-size: 12px;
padding: 2px 6px;
line-height: 1;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.product-card {
  position: relative;
  background-color: white;
  border-radius: 12px;
  padding: 0;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #f0f0f0;
  overflow: hidden;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  border-color: var(--primary-color);
}

/* כפתור הוספה לסל */
.add-to-cart-icon {
  position: absolute;
  top: 10px;
  right: 10px;
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

/* תגית מבצע */
.tag-sale {
  position: absolute;
  top: 0;
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

/* תוכן הכרטיס */
.product-content {
  padding: 15px;
  padding-top: 20px;
}

.product-img {
  height: 90px;
  width: 90px;
  margin: 0 auto 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 28px;
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
  margin-bottom: 8px;
  color: var(--dark-color);
  font-size: 14px;
  line-height: 1.3;
  min-height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.product-manufacturer {
  font-size: 12px;
  color: #6c757d;
  margin-top: 5px;
  font-weight: 500;
}

/* קישור המוצר */
.product-link {
  display: block;
  text-decoration: none;
  color: inherit;
  height: 100%;
}

.cart-items {
    margin-top: 40px;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.item-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.item-thumb {
    width: 50px;
    height: 50px;
    background-color: #eee;
    border-radius: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #777;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin-bottom: 3px;
}

.item-details p {
    color: #777;
    font-size: 14px;
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 5px;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 1px solid #ddd;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s;
}

.quantity-btn:hover {
    background-color: #f1f1f1;
}

.item-quantity span {
    width: 30px;
    text-align: center;
    font-weight: 500;
}

.item-price {
    font-weight: 500;
    color: var(--dark-color);
    width: 80px;
    text-align: left;
}

.item-remove {
    color: var(--accent-color);
    cursor: pointer;
    margin-right: 10px;
}

.cart-summary {
    margin-top: 30px;
    text-align: left;
}

.total-items {
    font-size: 16px;
    margin-bottom: 10px;
    color: #666;
}

.total-price {
    font-size: 24px;
    font-weight: bold;
    color: var(--dark-color);
    margin-bottom: 20px;
}

.checkout-btns {
    display: flex;
    justify-content: space-between;
    gap: 15px;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
}

.cart-badge {
  position: absolute;
  top: -6px;
  left: -8px;
  background: var(--accent-color);
  color: white;
  font-size: 11px;
  font-weight: bold;
  border-radius: 50%;
  padding: 2px 6px;
  min-width: 18px;
  text-align: center;
}

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
    
    .search-add {
        flex-direction: column;
    }
    
    .checkout-btns {
        flex-direction: column;
    }

    .category-tabs,
    #more-categories {
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .product-card {
        padding: 10px;
    }

    .product-img {
        height: 60px;
        width: 60px;
        font-size: 18px;
    }

    .product-name {
        font-size: 14px;
    }

    .page-title h1 {
        font-size: 24px;
    }

    .page-title p {
        font-size: 14px;
    }

    .cart-header h2 {
        font-size: 18px;
    }

    .btn, .btn-lg {
        font-size: 14px;
        padding: 10px 16px;
    }

    .search-results {
        right: 0;
    }
    
    #reader {
        max-width: 100%;
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
    <div class="page-title">
      <h1>יצירת סל קניות</h1>
      <p>צרו את רשימת הקניות שלכם והשוו את המחירים בין הסופרים השונים</p>
    </div>

    <div class="cart-creation">
      <div class="cart-header">
        <h2>הרשימה שלי</h2>
        <div class="cart-actions">
        </div>
      </div>

      <div class="cart-content">
        <div class="search-add">
          <input type="text" id="productSearch" placeholder="חפש מוצר להוספה..." autocomplete="off">
          <div class="search-results" id="searchResults"></div>
          <button class="btn btn-primary" id="addButton">חיפוש</button>
        </div>

        <!-- אזור סריקת הברקוד -->
        <div id="reader" style="display: none;"></div>
        <div class="scanner-controls" style="display: none;" id="scannerControls">
          <div class="scanner-status" id="scannerStatus">מכוון את המצלמה לברקוד...</div>
          <button class="btn btn-secondary" id="stopScanBtn">עצור סריקה</button>
        </div>

        <div class="product-categories">
          <div class="category-title">
            <h3>קטגוריות מוצרים</h3>
            <span>בחרו קטגוריה כדי לראות מוצרים</span>
          </div>
                
      <div class="category-tabs">
      <?php foreach (array_slice($categories, 0, 5) as $cat): ?>
        <div class="category-tab" onclick="filterByCategory(<?= $cat['category_id'] ?>)">
          <?= htmlspecialchars($cat['category_name']) ?>
        </div>
      <?php endforeach; ?>
    
      <!-- כפתור פתיחה -->
      <div id="expand-btn" class="category-tab category-expand" onclick="toggleMoreCategories(true)" title="הצג קטגוריות נוספות">
        +
      </div>
    
      <!-- כפתור סגירה -->
      <div id="collapse-btn" class="category-tab category-expand" onclick="toggleMoreCategories(false)" title="הסתר קטגוריות" style="display: none;">
        ×
      </div>
    </div>
        
        <div id="more-categories" class="category-tabs" style="display: none;">
          <?php foreach (array_slice($categories, 5) as $cat): ?>
            <div class="category-tab" onclick="filterByCategory(<?= $cat['category_id'] ?>)">
              <?= htmlspecialchars($cat['category_name']) ?>
            </div>
          <?php endforeach; ?>
        </div>

        <h1 id="category-title" style='text-align:center; display: none;'></h1>

    <div class="products-grid" id="products-grid" style="display: none;">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $code = $row['Item_Code'];
            $name = htmlspecialchars($row['Item_Name']);
            $cat_id = $row['category_id'];
            $relative_path = "WISECART/images/$code.jpg";
            $absolute_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative_path;
            $img_html = file_exists($absolute_path)
              ? "<img src='/$relative_path' alt='$name' style='max-height:100px;' />"
              : "🛒";
          ?>
          
        <div class="product-card" data-category-id="<?= $cat_id ?>">
          <button class="add-to-cart-icon" onclick="addToCart('<?= $code ?>', '<?= $name ?>'); event.stopPropagation();">+🛒</button>
        
          <a href="product.php?code=<?= $code ?>" class="product-link" style="display:block; text-decoration:none; color:inherit;">
            <div class="product-img"><?= $img_html ?></div>
            <div class="product-name"><?= $name ?></div>
            
           <?php if (!empty($row['is_on_sale']) && (int)$row['is_on_sale'] === 1): ?>
          <!--  <div class="tag-sale">במבצע!</div>-->
            <?php endif; ?>
          </a>
        </div>
                  
        <?php endwhile; ?>
      <?php else: ?>
        <p>לא נמצאו מוצרים</p>
      <?php endif; ?>
            </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<?php include('includes/footer.php'); ?>
 
<script>
// משתנה גלובלי לסורק
let html5QrCode = null;
let isScanning = false;

// פונקציות קיימות לקטגוריות
function toggleMoreCategories(show) {
  const more = document.getElementById('more-categories');
  const expandBtn = document.getElementById('expand-btn');
  const collapseBtn = document.getElementById('collapse-btn');

  if (show) {
    more.style.display = 'flex';
    expandBtn.style.display = 'none';
    collapseBtn.style.display = 'flex';
  } else {
    more.style.display = 'none';
    expandBtn.style.display = 'flex';
    collapseBtn.style.display = 'none';
  }
}

function filterByCategory(categoryId) {
    const cards = document.querySelectorAll('.product-card');
    const tabs = document.querySelectorAll('.category-tab');
    const title = document.getElementById('category-title');
    const productsGrid = document.getElementById('products-grid');

    // הסרת מחלקת active מכל הכפתורים
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // מציאת הכפתור שנלחץ והוספת מחלקת active
    const clickedTab = Array.from(tabs).find(tab =>
        tab.getAttribute('onclick')?.includes(`(${categoryId})`) && 
        !tab.classList.contains('category-expand')
    );
    
    if (clickedTab) {
        clickedTab.classList.add('active');
        
        // עדכון הכותרת עם שם הקטגוריה האמיתי והצגתה
        const categoryName = clickedTab.textContent.trim();
        title.textContent = categoryName;
        title.style.display = 'block';
        
        // הצגת הגריד
        productsGrid.style.display = 'grid';
        
        // סינון המוצרים
        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category-id');
            const show = cardCat == categoryId;
            card.style.display = show ? 'block' : 'none';
        });
    }
}

// עגלת קניות
let cart = [];

function addToCart(code, name) {
    const existingItem = cart.find(item => item.code === code);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ code: code, name: name, quantity: 1 });
    }
    
    updateCartDisplay();
    saveCartToStorage(); 
    showAddToCartNotification(name);
}

function removeFromCart(code) {
    cart = cart.filter(item => item.code !== code);
    updateCartDisplay();
    saveCartToStorage(); 
}

function updateQuantity(code, change) {
    const item = cart.find(item => item.code === code);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(code);
        } else {
            updateCartDisplay();
            saveCartToStorage();
        }
    }
}

function updateCartDisplay() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    updateCartCount(totalItems);
    updateCartPopupContent();
}

function updateCartCount(count) {
    const desktopCount = document.getElementById('cart-count');
    const mobileCount = document.getElementById('cart-count-mobile');
    
    if (desktopCount) desktopCount.textContent = count;
    if (mobileCount) mobileCount.textContent = count;
}

function updateCartPopupContent() {
    const desktopContent = document.getElementById('cartPopupContent');
    const mobileContent = document.getElementById('cartPopupContentMobile');
    const desktopTotal = document.getElementById('cartTotal');
    const mobileTotal = document.getElementById('cartTotalMobile');
    
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalText = `${totalItems} ${totalItems === 1 ? 'פריט' : 'פריטים'} ברשימה`;
    
    if (desktopTotal) desktopTotal.textContent = totalText;
    if (mobileTotal) mobileTotal.textContent = totalText;
    
    const content = generateCartContent();
    if (desktopContent) desktopContent.innerHTML = content;
    if (mobileContent) mobileContent.innerHTML = content;
}

function generateCartContent() {
    if (cart.length === 0) {
        return `
            <div class="cart-popup-empty">
                <div class="cart-popup-empty-icon">🛒</div>
                <p>הרשימה ריקה</p>
            </div>
        `;
    }
    
    return cart.map(item => {
        const imagePath = `/WISECART/images/${item.code}.jpg`;
        
        return `
            <div class="cart-item-popup">
                <div class="cart-item-image">
                    <span class="cart-item-image-placeholder">🛒</span>
                    <img src="${imagePath}" alt="${item.name}" 
                         onload="this.previousElementSibling.style.display='none'" 
                         onerror="this.style.display='none'">
                </div>
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn" onclick="updateQuantity('${item.code}', -1); event.stopPropagation();">-</button>
                        <span class="quantity-number">${item.quantity}</span>
                        <button class="quantity-btn" onclick="updateQuantity('${item.code}', 1); event.stopPropagation();">+</button>
                    </div>
                </div>
                <div class="cart-item-remove" onclick="removeFromCart('${item.code}'); event.stopPropagation();" title="הסר מהרשימה">🗑️</div>
            </div>
        `;
    }).join('');
}

function showAddToCartNotification(productName) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--secondary-color);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        z-index: 10000;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
    `;
    notification.innerHTML = `✓ ${productName} נוסף לרשימה`;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.style.transform = 'translateX(0)', 100);
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// שמירה וטעינת הסל
function saveCartToStorage() {
    localStorage.setItem('wisecart_cart', JSON.stringify(cart));
}

function loadCartFromStorage() {
    const savedCart = localStorage.getItem('wisecart_cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartDisplay();
    }
}

// ===== פונקציות סריקת ברקוד - תוקן! =====

function startBarcodeScanner() {
    console.log('מתחיל סריקת ברקוד...');
    
    if (isScanning) {
        console.log('הסריקה כבר פעילה');
        return;
    }

    // בדיקה אם html5-qrcode נטען
    if (typeof Html5Qrcode === 'undefined') {
        alert('ספריית הסריקה לא נטענה. אנא רענן את הדף ונסה שוב.');
        return;
    }

    // בקשת הרשאות מצלמה
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: "environment" // מצלמה אחורית במובייל
        } 
    })
    .then(() => {
        console.log('הרשאה למצלמה התקבלה');
        initializeScanner();
    })
    .catch(err => {
        console.error('שגיאה בגישה למצלמה:', err);
        if (err.name === 'NotAllowedError') {
            alert('עליך לאשר גישה למצלמה כדי לסרוק ברקוד');
        } else if (err.name === 'NotFoundError') {
            alert('לא נמצאה מצלמה במכשיר');
        } else {
            alert('שגיאה בגישה למצלמה: ' + err.message);
        }
    });
}

function initializeScanner() {
    const readerElement = document.getElementById("reader");
    const scannerControls = document.getElementById("scannerControls");
    const scannerStatus = document.getElementById("scannerStatus");
    
    // הצגת אלמנטי הסריקה
    readerElement.style.display = "block";
    scannerControls.style.display = "block";
    scannerStatus.textContent = "מכוון את המצלמה לברקוד...";
    
    html5QrCode = new Html5Qrcode("reader");
    
    Html5Qrcode.getCameras()
    .then(devices => {
        console.log('מצלמות זמינות:', devices);
        
        if (devices && devices.length) {
            // העדפה למצלמה אחורית
            let cameraId = devices[0].id;
            const backCamera = devices.find(device => 
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('rear') ||
                device.label.toLowerCase().includes('environment')
            );
            
            if (backCamera) {
                cameraId = backCamera.id;
                console.log('נבחרה מצלמה אחורית:', backCamera.label);
            }

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                // הגדרות נוספות לזיהוי ברקודים
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE, Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39, Html5QrcodeSupportedFormats.EAN_13, Html5QrcodeSupportedFormats.EAN_8],
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            html5QrCode.start(
                cameraId,
                config,
                (decodedText, decodedResult) => {
                    console.log("ברקוד/QR נסרק:", decodedText);
                    console.log("פרטי הסריקה:", decodedResult);
                    
                    scannerStatus.textContent = "ברקוד נסרק! מחפש מוצר...";
                    
                    // עצירת הסריקה מיד כשמוצא ברקוד
                    stopBarcodeScanner();
                    
                    // טיפול בברקוד שנסרק
                    handleScannedBarcode(decodedText);
                },
                errorMessage => {
                    // לא להציג שגיאות רגילות של חיפוש
                    if (!errorMessage.includes('No QR code found') && 
                        !errorMessage.includes('NotFoundException') &&
                        !errorMessage.includes('No MultiFormat Readers')) {
                        console.warn("שגיאה בסריקה:", errorMessage);
                    }
                }
            )
            .then(() => {
                isScanning = true;
                console.log("סורק הברקוד הופעל בהצלחה");
                scannerStatus.textContent = "מכוון את המצלמה לברקוד...";
            })
            .catch(err => {
                console.error("שגיאה בהפעלת הסורק:", err);
                alert("שגיאה בהפעלת הסורק: " + err.message);
                stopBarcodeScanner();
            });
        } else {
            alert("לא נמצאו מצלמות במכשיר");
        }
    })
    .catch(err => {
        console.error("שגיאה בטעינת רשימת המצלמות:", err);
        alert("שגיאה בגישה למצלמות: " + err.message);
        stopBarcodeScanner();
    });
}

function stopBarcodeScanner() {
    console.log('עוצר סריקת ברקוד...');
    
    if (html5QrCode && isScanning) {
        html5QrCode.stop()
        .then(() => {
            console.log('הסריקה הופסקה בהצלחה');
            html5QrCode.clear();
            
            // הסתרת אלמנטי הסריקה
            document.getElementById("reader").style.display = "none";
            document.getElementById("scannerControls").style.display = "none";
            
            isScanning = false;
            html5QrCode = null;
        })
        .catch(err => {
            console.error('שגיאה בעצירת הסריקה:', err);
            // גם אם יש שגיאה, נסתיר את האלמנטים
            document.getElementById("reader").style.display = "none";
            document.getElementById("scannerControls").style.display = "none";
            isScanning = false;
            html5QrCode = null;
        });
    }
}

function handleScannedBarcode(code) {
    console.log("מעבד ברקוד:", code);

    // ניקוי הברקוד (הסרת רווחים מיותרים)
    const cleanCode = code.trim();
    
    // הצגת הודעת טעינה
    const scannerStatus = document.getElementById("scannerStatus");
    if (scannerStatus) {
        scannerStatus.textContent = `מחפש מוצר עם קוד: ${cleanCode}`;
    }

    // הצגת הודעת טעינה למשתמש
    const loadingNotification = document.createElement('div');
    loadingNotification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #3498db;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        z-index: 10000;
        font-weight: 500;
        max-width: 300px;
    `;
    loadingNotification.innerHTML = `🔍 מחפש מוצר...`;
    document.body.appendChild(loadingNotification);

    // חיפוש המוצר בשרת
    fetch(`search_by_barcode.php?code=${encodeURIComponent(cleanCode)}`)
        .then(response => {
            console.log('סטטוס תגובה:', response.status);
            if (!response.ok) {
                throw new Error(`שגיאה בשרת: ${response.status} - ${response.statusText}`);
            }
            return response.text(); // קבלת הטקסט תחילה
        })
        .then(text => {
            console.log('תגובה גולמית מהשרת:', text);
            
            // ניסיון לפרסר JSON
            let product;
            try {
                product = JSON.parse(text);
            } catch (e) {
                console.error('שגיאה בפירוש JSON:', e);
                throw new Error('תגובה לא תקינה מהשרת');
            }
            
            console.log('תוצאת חיפוש:', product);
            
            // הסרת הודעת הטעינה
            document.body.removeChild(loadingNotification);
            
            if (product && product.success) {
                // מוצר נמצא - מעבר לדף המוצר
                if (scannerStatus) {
                    scannerStatus.textContent = `המוצר "${product.name}" נמצא! מעביר לדף המוצר...`;
                }
                
                // הצגת הודעת הצלחה
                const successNotification = document.createElement('div');
                successNotification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #27ae60;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
                    z-index: 10000;
                    font-weight: 500;
                    max-width: 300px;
                `;
                successNotification.innerHTML = `✅ מוצר נמצא: ${product.name}`;
                document.body.appendChild(successNotification);
                
                // מעבר לדף המוצר אחרי 1.5 שניות
                setTimeout(() => {
                    window.location.href = `product.php?code=${encodeURIComponent(product.code)}`;
                }, 1500);
                
            } else {
                // מוצר לא נמצא
                const message = product && product.message ? product.message : "המוצר לא נמצא במערכת";
                
                if (scannerStatus) {
                    scannerStatus.textContent = "המוצר לא נמצא במערכת";
                }
                
                // הצגת הודעת שגיאה
                const errorNotification = document.createElement('div');
                errorNotification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #e74c3c;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
                    z-index: 10000;
                    font-weight: 500;
                    max-width: 300px;
                `;
                errorNotification.innerHTML = `❌ ${message}<br><small>קוד: ${cleanCode}</small>`;
                document.body.appendChild(errorNotification);
                
                // הסרת ההודעה אחרי 5 שניות
                setTimeout(() => {
                    if (document.body.contains(errorNotification)) {
                        document.body.removeChild(errorNotification);
                    }
                }, 5000);
                
                console.log(`מוצר לא נמצא עבור קוד: ${cleanCode}`);
            }
        })
        .catch(error => {
            console.error('שגיאה בחיפוש מוצר:', error);
            
            // הסרת הודעת הטעינה אם עדיין קיימת
            if (document.body.contains(loadingNotification)) {
                document.body.removeChild(loadingNotification);
            }
            
            if (scannerStatus) {
                scannerStatus.textContent = "שגיאה בחיפוש המוצר";
            }
            
            // הצגת הודעת שגיאה
            const errorNotification = document.createElement('div');
            errorNotification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #e74c3c;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
                z-index: 10000;
                font-weight: 500;
                max-width: 300px;
            `;
            errorNotification.innerHTML = `❌ שגיאה בחיפוש המוצר<br><small>${error.message}</small>`;
            document.body.appendChild(errorNotification);
            
            setTimeout(() => {
                if (document.body.contains(errorNotification)) {
                    document.body.removeChild(errorNotification);
                }
            }, 5000);
        });
}

// ===== פונקציות חיפוש טקסט =====

document.addEventListener('DOMContentLoaded', function() {
    // טעינת הסל מה-localStorage
    loadCartFromStorage();
    
    // הגדרת event listeners
    setupEventListeners();
    setupSearchFunctionality();
});

function setupEventListeners() {
    // כפתור סריקת ברקוד
    const scanBarcodeBtn = document.getElementById('scanBarcodeBtn');
    if (scanBarcodeBtn) {
        scanBarcodeBtn.addEventListener('click', startBarcodeScanner);
    }
    
    // כפתור עצירת סריקה
    const stopScanBtn = document.getElementById('stopScanBtn');
    if (stopScanBtn) {
        stopScanBtn.addEventListener('click', stopBarcodeScanner);
    }
    
    // כפתור חיפוש
    const addButton = document.getElementById('addButton');
    if (addButton) {
        addButton.addEventListener('click', function() {
            const searchInput = document.getElementById('productSearch');
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                window.location.href = `search_results.php?q=${encodeURIComponent(query)}`;
            } else {
                alert('אנא הקלד לפחות 2 תווים לחיפוש');
            }
        });
    }
}

function setupSearchFunctionality() {
    const searchInput = document.getElementById('productSearch');
    const searchResults = document.getElementById('searchResults');
    const searchContainer = document.querySelector('.search-add');
    let currentTimeout;

    if (!searchInput || !searchResults) return;

    // פונקציות עזר לחיפוש
    function showResults() {
        if (searchContainer) searchContainer.classList.add('active');
    }

    function hideResults() {
        if (searchContainer) searchContainer.classList.remove('active');
    }

    function highlightText(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    // פונקציית החיפוש
    function searchProducts(query) {
        fetch(`search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                console.log('תוצאות חיפוש:', data);
                displayResults(data, query);
            })
            .catch(error => {
                console.error('שגיאה בחיפוש:', error);
                hideResults();
            });
    }

    // הצגת תוצאות חיפוש
    function displayResults(products, query) {
        if (products.length === 0) {
            searchResults.innerHTML = '<div class="no-results">לא נמצאו מוצרים</div>';
            showResults();
            return;
        }

        const resultsHTML = products.map(product => {
            const highlightedName = highlightText(product.name, query);
            const imageHTML = product.has_image 
                ? `<img src="/WISECART/images/${product.code}.jpg" alt="${product.name}">` 
                : '🛒';
            
            return `
                <div class="search-result-item" data-code="${product.code}">
                    <div class="search-result-img">${imageHTML}</div>
                    <div class="search-result-content">
                        <div class="search-result-name">${highlightedName}</div>
                    </div>
                </div>
            `;
        }).join('');

        searchResults.innerHTML = resultsHTML;
        showResults();

        // הוספת event listeners לתוצאות
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const code = this.dataset.code;
                window.location.href = `product.php?code=${code}`;
            });
        });
    }

    // Event listeners לחיפוש
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(currentTimeout);
        
        if (query.length < 2) {
            hideResults();
            return;
        }

        currentTimeout = setTimeout(() => {
            searchProducts(query);
        }, 300);
    });

    // טיפול במקש Enter
    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const query = searchInput.value.trim();
            
            if (query.length >= 2) {
                window.location.href = `search_results.php?q=${encodeURIComponent(query)}`;
            } else {
                alert('אנא הקלד לפחות 2 תווים לחיפוש');
            }
        }
    });

    // סגירת תוצאות חיפוש בלחיצה מחוץ
    document.addEventListener('click', function(event) {
        if (searchContainer && !searchContainer.contains(event.target)) {
            hideResults();
        }
    });
}

// פונקציות נוספות
function toggleMobileMenu() {
    const mobileNav = document.getElementById('mobile-nav');
    const menuBtn = document.querySelector('.mobile-menu-btn');
    
    if (mobileNav && menuBtn) {
        mobileNav.classList.toggle('show');
        
        if (mobileNav.classList.contains('show')) {
            menuBtn.innerHTML = '✕';
        } else {
            menuBtn.innerHTML = '☰';
        }
    }
}

function toggleCartPopup(event) {
    if (event) {
        event.stopPropagation();
    }
    
    const desktopPopup = document.getElementById('cartPopup');
    const mobilePopup = document.getElementById('cartPopupMobile');
    
    if (window.innerWidth <= 768) {
        if (mobilePopup) {
            mobilePopup.classList.toggle('show');
            if (desktopPopup) desktopPopup.classList.remove('show');
        }
    } else {
        if (desktopPopup) {
            desktopPopup.classList.toggle('show');
            if (mobilePopup) mobilePopup.classList.remove('show');
        }
    }
}

function closeCartPopup(event) {
    if (event) {
        event.stopPropagation();
    }
    
    const desktopPopup = document.getElementById('cartPopup');
    const mobilePopup = document.getElementById('cartPopupMobile');
    
    if (desktopPopup) desktopPopup.classList.remove('show');
    if (mobilePopup) mobilePopup.classList.remove('show');
}

function goToShoppingLists() {
    localStorage.setItem('currentCart', JSON.stringify(cart));
    window.location.href = 'shopping_lists.php';
}

// סגירת החלונית בלחיצה מחוץ לאזור
document.addEventListener('click', function(event) {
    const cartIcons = document.querySelectorAll('.cart-icon');
    let clickedInsideCart = false;
    
    cartIcons.forEach(icon => {
        if (icon.contains(event.target)) {
            clickedInsideCart = true;
        }
    });
    
    if (!clickedInsideCart) {
        closeCartPopup();
    }
});

</script>
<?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>