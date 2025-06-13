<?php
require_once('includes/config.php');
require_once('includes/products.php');

// שליפת רשימות הקניות של המשתמש
$user_lists = [];
if (isset($_SESSION['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT list_id, list_name FROM user_shopping_lists WHERE id = ? ORDER BY list_name");
        $stmt->execute([$_SESSION['id']]);
        $user_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user lists: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($product['Item_Name']) ? htmlspecialchars($product['Item_Name']) : 'מוצר' ?> - פרטי מוצר</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
    .btn-outline {
        background-color: transparent;
        color: var(--dark-color);
        border: 1px solid var(--dark-color);
    }

    .btn-outline:hover {
        background-color: var(--dark-color);
        color: white;
    }

    .cart-icon {
        position: relative;
        font-size: 24px;
        cursor: pointer;
        color: var(--dark-color);
        line-height: 1;
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

    /* Main Content */
    main {
        flex: 1;
        padding-top: 60px;
    }

    .product-page-wrapper {
        max-width: 1000px;
        margin: 0 auto 40px;
        padding: 30px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .product-info h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: var(--dark-color);
    }

    .product-info .label {
        font-weight: bold;
        color: var(--dark-color);
    }

    .product-info p {
        margin-bottom: 10px;
        font-size: 16px;
    }

    /* טבלת מחירים */
    .price-section {
        margin-top: 30px;
    }

    .price-section h3 {
        margin-bottom: 15px;
        color: var(--dark-color);
        font-size: 22px;
    }

    .price-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .price-table th,
    .price-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #ddd;
        text-align: right;
    }

    .price-table th {
        background-color: var(--light-color);
        font-weight: bold;
        color: var(--dark-color);
    }

    .price-table td {
        font-size: 16px;
        color: #333;
    }

    .price-table td.promo {
        color: #2d5a2d;
        font-weight: bold;
    }

    /* כפתור הוספה לרשימה החדש */
    .add-to-list-section {
        margin-top: 30px;
        padding: 25px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        border: 1px solid #dee2e6;
        text-align: center;
    }

    .add-to-list-section h4 {
        margin-bottom: 15px;
        color: var(--dark-color);
        font-size: 18px;
        font-weight: 600;
    }

    .add-to-list-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .add-to-list-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }

    .add-to-list-btn:active {
        transform: translateY(0);
    }

    .add-to-list-icon {
        font-size: 18px;
    }

    /* Modal Styles - Copy from header */
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

    .product-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        border: 1px solid #e9ecef;
    }

    .product-summary-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--dark-color);
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e1e8ed;
    }

    .product-item:last-child {
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

    /* כפתור חזרה */
    .back-button-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0px 30px 20px;
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

    /* רספונסיביות */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
            padding: 10px 15px;
        }

        nav ul {
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        main {
            padding-top: 110px;
        }

        .product-page-wrapper {
            margin: 0 15px 30px;
            padding: 20px 15px;
        }

        .product-info h1 {
            font-size: 24px;
        }

        .price-table th,
        .price-table td {
            padding: 10px;
            font-size: 14px;
        }

        .footer-content {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .social-links {
            justify-content: center;
        }

        .back-button-container {
            padding: 0 15px 15px;
        }

        .back-button {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 18px;
        }

        .back-icon {
            font-size: 14px;
        }

        .add-to-list-section {
            padding: 20px 15px;
        }

        .add-to-list-btn {
            padding: 12px 25px;
            font-size: 15px;
        }

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
</head>
<body>
<!-- Header -->
 <?php include('includes/header.php'); ?>
<main>
    <!-- כפתור חזרה -->
    <div class="back-button-container">
        <button class="back-button" onclick="goBack()">
            <span class="back-icon">⮕</span>
            חזרה
        </button>
    </div>
    
    <div class="product-page-wrapper">
        <div class="product-info">
            <h1><?= htmlspecialchars($product['Item_Name']) ?></h1>
            <?php if ($image_exists): ?>
            <img src="/WISECART/images/<?= $product['Item_Code'] ?>.jpg" alt="<?= htmlspecialchars($product['Item_Name']) ?>" style="max-width: 200px; display:block; margin-bottom:20px;">
            <?php endif; ?>
            <p><span class="label">קוד מוצר:</span> <?= htmlspecialchars($product['Item_Code']) ?></p>
            <p><span class="label">מותג/יצרן:</span> <?= htmlspecialchars($product['Manufacture_Name']) ?></p>
        </div>

        <div class="price-section">
            <h3>השוואת מחירים ברשתות:</h3>
            
            <?php
            // הצגת פער באחוזים
            if (!empty($prices_array) && $min_price > 0 && $max_price > 0 && $max_price > $min_price) {
                $diff_percent = round((($max_price - $min_price) / $min_price) * 100, 1);
                echo "<p style='font-weight:bold; color:#e74c3c; font-size:16px; margin-bottom:15px;'>פער בין המחיר היקר ביותר לזול ביותר: $diff_percent%</p>";
            }
            ?>

            <table class="price-table">
                <thead>
                    <tr>
                        <th>רשת</th>
                        <th>מחיר</th>
                        <th>מבצע</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prices_array)): ?>
                        <?php foreach ($prices_array as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['chain']) ?></td>
                                <td>₪<?= number_format($row['item_price'], 2) ?></td>
                                <td class="<?= !empty($row['promo_description']) ? 'promo' : '' ?>">
                                    <?php if (!empty($row['promo_description'])): ?>
                                        <?= htmlspecialchars($row['promo_description']) ?>
                                        <?php if (!empty($row['end_date'])): ?>
                                            (עד <?= date('d/m/Y', strtotime($row['end_date'])) ?>)
                                        <?php endif; ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">לא נמצאו מחירים למוצר זה.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal הוספה לרשימה -->
<div class="modal-overlay" id="productAddToListModal" style="display: none;">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">הוסף מוצר לרשימה שמורה</h3>
            <button class="modal-close" onclick="hideProductAddToListModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- בחירת רשימה קיימת -->
            <div class="form-group">
                <label class="form-label">בחר רשימה קיימת:</label>
                <select class="form-input" id="productExistingListSelect">
                    <option value="">טוען רשימות...</option>
                </select>
            </div>
            
            <div class="or-divider">
                <span>או</span>
            </div>
            
            <!-- יצירת רשימה חדשה -->
            <div class="form-group">
                <label class="form-label">צור רשימה חדשה:</label>
                <input type="text" class="form-input" id="productNewListNameInput" placeholder="הזן שם לרשימה החדשה..." maxlength="100">
            </div>
            
            <!-- כמות -->
            <div class="form-group">
                <label class="form-label">כמות:</label>
                <input type="number" class="form-input" id="productQuantityInput" value="1" min="1" max="99">
            </div>
            
            <!-- סיכום מוצר -->
            <div class="product-summary">
                <div class="product-summary-title">המוצר שיתווסף:</div>
                <div class="product-item">
                    <span><?= htmlspecialchars($product['Item_Name']) ?></span>
                    <span id="productQuantityDisplay">כמות: 1</span>
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="modal-btn btn-cancel" onclick="hideProductAddToListModal()">ביטול</button>
                <button class="modal-btn btn-save" onclick="addProductToList()">הוסף לרשימה</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include('includes/footer.php'); ?>
<script src="js/script.js"></script>
<script>
    // JavaScript לטיפול בהוספה לרשימה של מוצר בודד
    let productUserSavedLists = [];

    // הצגת Modal להוספה לרשימה
    async function showProductAddToListModal() {
        <?php if (!isset($_SESSION['id'])): ?>
            showError("יש להתחבר כדי להוסיף מוצרים לרשימה");
            return;
        <?php endif; ?>

        // טעינת רשימות המשתמש
        await loadProductUserLists();
        
        // הצגת ה-Modal
        document.getElementById('productAddToListModal').style.display = 'flex';
        
        // מיקוד על בחירת רשימה קיימת
        document.getElementById('productExistingListSelect').focus();
    }

    // הסתרת Modal
    function hideProductAddToListModal() {
        document.getElementById('productAddToListModal').style.display = 'none';
        document.getElementById('productNewListNameInput').value = '';
        document.getElementById('productExistingListSelect').value = '';
        document.getElementById('productQuantityInput').value = '1';
        updateProductQuantityDisplay();
    }

    // טעינת רשימות המשתמש
    async function loadProductUserLists() {
        try {
            const response = await fetch('get_list.php?action=get_lists');
            const data = await response.json();
            
            const select = document.getElementById('productExistingListSelect');
            
            if (data.success && data.lists && data.lists.length > 0) {
                productUserSavedLists = data.lists;
                
                select.innerHTML = '<option value="">בחר רשימה קיימת...</option>' +
                    data.lists.map(list => 
                        `<option value="${list.id}">${list.name} (${list.item_count} מוצרים)</option>`
                    ).join('');
            } else {
                select.innerHTML = '<option value="">אין רשימות שמורות</option>';
            }
        } catch (error) {
            console.error('Error loading lists:', error);
            document.getElementById('productExistingListSelect').innerHTML = '<option value="">שגיאה בטעינת רשימות</option>';
        }
    }

    // עדכון תצוגת הכמות
    function updateProductQuantityDisplay() {
        const quantity = document.getElementById('productQuantityInput').value;
        document.getElementById('productQuantityDisplay').textContent = `כמות: ${quantity}`;
    }

    // הוספת המוצר לרשימה
    async function addProductToList() {
        const existingListId = document.getElementById('productExistingListSelect').value;
        const newListName = document.getElementById('productNewListNameInput').value.trim();
        const quantity = parseInt(document.getElementById('productQuantityInput').value) || 1;
        
        // בדיקת תקינות קלט
        if (!existingListId && !newListName) {
            showError('יש לבחור רשימה קיימת או להזין שם לרשימה חדשה');
            return;
        }
        
        if (existingListId && newListName) {
            showError('יש לבחור אפשרות אחת בלבד - רשימה קיימת או חדשה');
            return;
        }

        if (quantity < 1 || quantity > 99) {
            showError('הכמות חייבת להיות בין 1 ל-99');
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

            // הוספת המוצר לרשימה
            const addResponse = await fetch('get_list.php?action=add_to_list', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    list_id: parseInt(targetListId),
                    item_code: '<?= htmlspecialchars($product['Item_Code']) ?>',
                    quantity: quantity
                })
            });

            const addData = await addResponse.json();
            
            if (addData.success) {
                const listName = newListName || (productUserSavedLists.find(list => list.id == targetListId)?.name || 'הרשימה');
                showSuccess(`המוצר נוסף בהצלחה לרשימה "${listName}" (כמות: ${quantity})`);
                hideProductAddToListModal();
            } else {
                showError('שגיאה בהוספת המוצר: ' + (addData.message || 'שגיאה לא ידועה'));
            }

        } catch (error) {
            console.error('Error adding to list:', error);
            showError('שגיאה בהוספה לרשימה');
        }
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

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // עדכון כמות
        const quantityInput = document.getElementById('productQuantityInput');
        if (quantityInput) {
            quantityInput.addEventListener('input', updateProductQuantityDisplay);
        }

        // איפוס שדות כשבוחרים אפשרות אחרת
        const existingListSelect = document.getElementById('productExistingListSelect');
        const newListNameInput = document.getElementById('productNewListNameInput');
        
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

    // סגירת Modal בלחיצה על הרקע
    document.addEventListener('click', function(e) {
        if (e.target.id === 'productAddToListModal') {
            hideProductAddToListModal();
        }
    });

    // טיפול ב-ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('productAddToListModal').style.display === 'flex') {
            hideProductAddToListModal();
        }
    });
    
    // פונקציית חזרה חכמה
    function goBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = 'EXcreateCart.php';
        }
    }
    
    // קיצור דרך מקלדת לחזרה
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' || (event.key === 'Backspace' && !event.target.matches('input, textarea'))) {
            event.preventDefault();
            goBack();
        }
    });
</script>
<?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>