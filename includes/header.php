<?php
$current_file = basename($_SERVER['PHP_SELF'], '.php');
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// פונקציה לבדיקה אם הקישור אקטיבי
function isActive($page) {
    global $current_file;
    return $current_file === $page ? 'active' : '';
}
?>

<!-- CSS עבור Modal הוספה לרשימה -->
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 10000;
}

.modal {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color), #2980b9);
    color: white;
    padding: 20px 25px;
    border-radius: 15px 15px 0 0;
    position: relative;
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0;
}

.modal-close {
    position: absolute;
    top: 15px;
    left: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background-color 0.3s;
}

.modal-close:hover {
    background-color: rgba(255,255,255,0.2);
}

.modal-body {
    padding: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-color);
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.cart-summary {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin: 15px 0;
}

.cart-summary-title {
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--dark-color);
}

.cart-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #e1e8ed;
}

.cart-item:last-child {
    border-bottom: none;
}

.modal-actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.modal-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel {
    background-color: #95a5a6;
    color: white;
}

.btn-cancel:hover {
    background-color: #7f8c8d;
}

.btn-save {
    background-color: var(--secondary-color);
    color: white;
}

.btn-save:hover {
    background-color: #27ae60;
}

.or-divider {
    position: relative;
    text-align: center;
    margin: 20px 0;
}

.or-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #ddd;
    z-index: -1;
}

.or-divider span {
    background: white;
    padding: 0 15px;
    color: #666;
}

/* Success/Error Messages */
.message {
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    z-index: 10001;
    animation: messageSlideIn 0.3s ease-out;
    max-width: 400px;
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.message.success {
    background-color: var(--secondary-color);
}

.message.error {
    background-color: var(--accent-color);
}

.message-close {
    background: none;
    border: none;
    color: white;
    float: left;
    margin-left: 10px;
    cursor: pointer;
    font-size: 18px;
}

@media (max-width: 768px) {
    .modal {
        width: 95%;
        margin: 10px;
    }
    
    .modal-actions {
        flex-direction: column;
    }
    
    .message {
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
 
<header>
  <div class="header-content">
    <div class="mobile-header">
      <!-- Toggle (ימין) -->
      <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>

      <!-- לוגו (מרכז) -->
      <div class="mobile-logo">
        <span class="logo-icon">W</span>
        WiseCart
      </div>

      <!-- אייקונים (שמאל) -->
      <div class="mobile-actions">
        <div class="cart-icon" onclick="toggleCartPopup(event)" title="עגלת קניות">
        🛒<span id="cart-count-mobile" class="cart-badge">0</span>
        <div class="cart-popup" id="cartPopupMobile">
          <div class="cart-popup-header">
           <h3 class="cart-popup-title-mobile" id="cartTitleMobile">הרשימה שלי</h3>
              <button class="cart-popup-close" onclick="event.stopPropagation(); closeCartPopup();">&times;</button>
          </div>
          <div class="cart-popup-content" id="cartPopupContentMobile">
            <div class="cart-popup-empty">
              <div class="cart-popup-empty-icon">🛒</div>
              <p>הרשימה ריקה</p>
            </div>
          </div>
          <div class="cart-popup-footer">
            <div class="cart-total-items" id="cartTotalMobile">0 פריטים ברשימה</div>
            <button class="cart-view-list-btn" onclick="showAddToListModal()">הוספה לרשימה</button>
          </div>
        </div>
        </div>
      </div>
    </div>

    <!-- לוגו דסקטופ -->
    <div class="logo desktop-logo">
      <span class="logo-icon">W</span>
      <span>WiseCart</span>
    </div>

    <!-- ניווט דסקטופ -->
    <nav class="desktop-nav">
      <ul>
        <li><a href="./EXhomePage.php" class="nav-button <?php echo isActive('EXhomePage'); ?>">דף הבית</a></li>
        <li><a href="./EXcreateCart.php" class="nav-button <?php echo isActive('EXcreateCart'); ?>">יצירת סל קניות</a></li>
        <li><a href="./EXsavedList.php" class="nav-button <?php echo isActive('EXsavedList'); ?>">רשימות שמורות</a></li>
        <li><a href="./EXpersonalProfile.php" class="nav-button <?php echo isActive('EXpersonalProfile'); ?>">איזור אישי</a></li>
        <li><a href="./EXcontactPage.php" class="nav-button <?php echo isActive('EXcontactPage'); ?>">צור קשר</a></li>
      </ul>
    </nav>

    <!-- פעולות משתמש דסקטופ -->
    <div class="user-actions desktop-actions">
      <?php if ($logged_in): ?>
        <form action="./logout.php" method="post" style="display: inline;">
          <button type="submit" class="btn btn-outline">התנתקות</button>
        </form>
      <?php endif; ?>
      <div class="cart-icon" onclick="toggleCartPopup(event)" title="עגלת קניות">
        🛒<span id="cart-count" class="cart-badge">0</span>
        <div class="cart-popup" id="cartPopup">
          <div class="cart-popup-header">
            <h3 class="cart-popup-title">הרשימה שלי</h3>
            <button class="cart-popup-close" onclick="event.stopPropagation(); closeCartPopup();">&times;</button>
          </div>
          <div class="cart-popup-content" id="cartPopupContent">
            <div class="cart-popup-empty">  
                <div class="cart-popup-empty-icon">🛒</div>
                <p>הרשימה ריקה</p>
            </div>
          </div>
          <div class="cart-popup-footer">
            <div class="cart-total-items" id="cartTotal">0 פריטים ברשימה</div>
            <button class="cart-view-list-btn" onclick="showAddToListModal()">הוספה לרשימה</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- תפריט נפתח במובייל -->
  <nav class="mobile-nav" id="mobile-nav">
    <a href="./EXhomePage.php" class="mobile-nav-link <?php echo isActive('EXhomePage'); ?>">דף הבית</a>
    <a href="./EXcreateCart.php" class="mobile-nav-link <?php echo isActive('EXcreateCart'); ?>">יצירת סל קניות</a>
    <a href="./EXsavedList.php" class="mobile-nav-link <?php echo isActive('EXsavedList'); ?>">רשימות שמורות</a>
    <a href="./EXpersonalProfile.php" class="mobile-nav-link <?php echo isActive('EXpersonalProfile'); ?>">איזור אישי</a>
    <a href="./EXcontactPage.php" class="mobile-nav-link <?php echo isActive('EXcontactPage'); ?>">צור קשר</a>
    <?php if ($logged_in): ?>
      <form action="./logout.php" method="post" style="display: inline; width: 100%;">
        <button type="submit" class="mobile-nav-link" style="background: none; border: none; width: 100%; text-align: right; cursor: pointer;">התנתקות</button>
      </form>
    <?php endif; ?>
  </nav>
</header>

<!-- Modal הוספה לרשימה -->
<div class="modal-overlay" id="addToListModal" style="display: none;">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">הוסף לרשימה שמורה</h3>
            <button class="modal-close" onclick="hideAddToListModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- בחירת רשימה קיימת -->
            <div class="form-group">
                <label class="form-label">בחר רשימה קיימת:</label>
                <select class="form-input" id="existingListSelect">
                    <option value="">טוען רשימות...</option>
                </select>
            </div>
            
            <div class="or-divider">
                <span>או</span>
            </div>
            
            <!-- יצירת רשימה חדשה -->
            <div class="form-group">
                <label class="form-label">צור רשימה חדשה:</label>
                <input type="text" class="form-input" id="newListNameInput" placeholder="הזן שם לרשימה החדשה..." maxlength="100">
            </div>
            
            <!-- סיכום מוצרים -->
            <div class="cart-summary" style="margin-top: 20px;">
                <div class="cart-summary-title">מוצרים שיתווספו:</div>
                <div id="cartItemsToAdd">
                    <!-- מוצרים יתווספו כאן -->
                </div>
            </div>
            
            <div class="modal-actions" style="margin-top: 25px;">
                <button class="modal-btn btn-cancel" onclick="hideAddToListModal()">ביטול</button>
                <button class="modal-btn btn-save" onclick="addCartToList()">הוסף לרשימה</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript לטיפול בהוספה לרשימה -->
<script>
// JavaScript לטיפול בהוספה לרשימה
let userSavedLists = [];

// הצגת Modal להוספה לרשימה
async function showAddToListModal() {
    // בדיקת סל קניות
    let cart = getCartItems();
    if (!cart || cart.length === 0) {
        showError('הסל ריק - אין מוצרים להוספה');
        return;
    }

    // טעינת רשימות המשתמש
    await loadUserLists();
    
    // עדכון סיכום המוצרים
    updateCartItemsToAdd(cart);
    
    // הצגת ה-Modal
    document.getElementById('addToListModal').style.display = 'flex';
    
    // מיקוד על בחירת רשימה קיימת
    document.getElementById('existingListSelect').focus();
}

// הסתרת Modal
function hideAddToListModal() {
    document.getElementById('addToListModal').style.display = 'none';
    document.getElementById('newListNameInput').value = '';
    document.getElementById('existingListSelect').value = '';
}

// טעינת רשימות המשתמש
async function loadUserLists() {
    try {
        const response = await fetch('get_list.php?action=get_lists');
        const data = await response.json();
        
        const select = document.getElementById('existingListSelect');
        
        if (data.success && data.lists && data.lists.length > 0) {
            userSavedLists = data.lists;
            
            select.innerHTML = '<option value="">בחר רשימה קיימת...</option>' +
                data.lists.map(list => 
                    `<option value="${list.id}">${list.name} (${list.item_count} מוצרים)</option>`
                ).join('');
        } else {
            select.innerHTML = '<option value="">אין רשימות שמורות</option>';
        }
    } catch (error) {
        console.error('Error loading lists:', error);
        document.getElementById('existingListSelect').innerHTML = '<option value="">שגיאה בטעינת רשימות</option>';
    }
}

// עדכון רשימת המוצרים שיתווספו
function updateCartItemsToAdd(cart) {
    const container = document.getElementById('cartItemsToAdd');
    
    if (!cart || cart.length === 0) {
        container.innerHTML = '<p style="color: #777; text-align: center;">אין מוצרים בסל</p>';
        return;
    }

    container.innerHTML = cart.map(item => `
        <div class="cart-item">
            <span>${item.name}</span>
            <span>כמות: ${item.quantity}</span>
        </div>
    `).join('');
}

// קבלת מוצרי הסל
function getCartItems() {
    try {
        const savedCart = localStorage.getItem('wisecart_cart');
        return savedCart ? JSON.parse(savedCart) : [];
    } catch (e) {
        console.error('Error getting cart:', e);
        return [];
    }
}

// הוספת הסל לרשימה
async function addCartToList() {
    const existingListId = document.getElementById('existingListSelect').value;
    const newListName = document.getElementById('newListNameInput').value.trim();
    
    // בדיקת תקינות קלט
    if (!existingListId && !newListName) {
        showError('יש לבחור רשימה קיימת או להזין שם לרשימה חדשה');
        return;
    }
    
    if (existingListId && newListName) {
        showError('יש לבחור אפשרות אחת בלבד - רשימה קיימת או חדשה');
        return;
    }

    // קבלת מוצרי הסל
    const cart = getCartItems();
    if (!cart || cart.length === 0) {
        showError('הסל ריק - אין מוצרים להוספה');
        return;
    }

    try {
        let targetListId = existingListId;
        
        // אם צריך ליצור רשימה חדשה
        if (newListName && !existingListId) {
            const createResponse = await fetch('get_list.php?action=create_list', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    list_name: newListName
                })
            });

            const createData = await createResponse.json();
            
            if (!createData.success) {
                showError('שגיאה ביצירת הרשימה: ' + (createData.message || 'שגיאה לא ידועה'));
                return;
            }
            
            targetListId = createData.list_id;
        }

        // הוספת כל המוצרים לרשימה
        let successCount = 0;
        let errorCount = 0;

        for (const item of cart) {
            try {
                const addResponse = await fetch('get_list.php?action=add_to_list', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        list_id: parseInt(targetListId),
                        item_code: item.code,
                        quantity: parseInt(item.quantity)
                    })
                });

                const addData = await addResponse.json();
                
                if (addData.success) {
                    successCount++;
                } else {
                    errorCount++;
                    console.error('Error adding item:', item.name, addData.message);
                }
            } catch (error) {
                errorCount++;
                console.error('Error adding item:', item.name, error);
            }
        }

        // הצגת תוצאות
        if (successCount > 0) {
            const listName = newListName || (userSavedLists.find(list => list.id == targetListId)?.name || 'הרשימה');
            showSuccess(`${successCount} מוצרים נוספו בהצלחה לרשימה "${listName}"${errorCount > 0 ? ` (${errorCount} מוצרים נכשלו)` : ''}`);
            
            // ניקוי הסל אחרי הוספה מוצלחת
            localStorage.removeItem('wisecart_cart');
            updateCartDisplay();
            
            hideAddToListModal();
        } else {
            showError('לא ניתן היה להוסיף אף מוצר לרשימה');
        }
        
        setTimeout(() => {
    window.location.reload();
}, 1500); // או 2000 לשתי שניות

    } catch (error) {
        console.error('Error adding to list:', error);
        showError('שגיאה בהוספה לרשימה');
    }
}

// עדכון תצוגת הסל
function updateCartDisplay() {
    // עדכון מונה הסל
    const cartCount = document.getElementById('cart-count');
    const cartCountMobile = document.getElementById('cart-count-mobile');
    
    if (cartCount) cartCount.textContent = '0';
    if (cartCountMobile) cartCountMobile.textContent = '0';
    
    // עדכון תוכן הסל
    const cartContent = document.getElementById('cartPopupContent');
    const cartContentMobile = document.getElementById('cartPopupContentMobile');
    
    const emptyContent = `
        <div class="cart-popup-empty">
            <div class="cart-popup-empty-icon">🛒</div>
            <p>הרשימה ריקה</p>
        </div>
    `;
    
    if (cartContent) cartContent.innerHTML = emptyContent;
    if (cartContentMobile) cartContentMobile.innerHTML = emptyContent;
}

// הצגת הודעות
function showSuccess(message) {
    showMessage(message, 'success');
}

function showError(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.innerHTML = `
        ${message}
        <button class="message-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.body.appendChild(messageDiv);
    
    // הסרה אוטומטית אחרי 5 שניות
    setTimeout(() => {
        if (messageDiv.parentElement) {
            messageDiv.remove();
        }
    }, 5000);
}

// סגירת Modal בלחיצה על הרקע
document.addEventListener('click', function(e) {
    if (e.target.id === 'addToListModal') {
        hideAddToListModal();
    }
});

// טיפול ב-ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('addToListModal').style.display === 'flex') {
        hideAddToListModal();
    }
});

// איפוס שדה השם החדש כשבוחרים רשימה קיימת
document.addEventListener('DOMContentLoaded', function() {
    const existingListSelect = document.getElementById('existingListSelect');
    const newListNameInput = document.getElementById('newListNameInput');
    
    if (existingListSelect) {
        existingListSelect.addEventListener('change', function() {
            if (this.value && newListNameInput) {
                newListNameInput.value = '';
            }
        });
    }
    
    if (newListNameInput) {
        newListNameInput.addEventListener('input', function() {
            if (this.value.trim() && existingListSelect) {
                existingListSelect.value = '';
            }
        });
    }
});
</script>