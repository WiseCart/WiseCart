<?php
session_start();
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiseCart - רשימות שמורות</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
            .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            margin-top:20px;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .page-header p {
            text-align: center;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            padding: 90px 0 60px;
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
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Saved Lists Section */
        .saved-lists {
            margin-bottom: 50px;
        }
        
        .lists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .list-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .list-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        
        .list-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .list-title {
            font-size: 18px;
            font-weight: 500;
        }
        
        .list-date {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .list-body {
            padding: 20px;
        }
        
        .list-stats {
            display: flex;
            margin-bottom: 15px;
        }
        
        .stat {
            flex: 1;
            text-align: center;
            padding: 10px;
            background-color: var(--light-color);
            border-radius: 5px;
            margin: 0 5px;
        }
        
        .stat:first-child {
            margin-right: 0;
        }
        
        .stat:last-child {
            margin-left: 0;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: var(--dark-color);
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .list-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .list-btn {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-view {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-view:hover {
            background-color: #2980b9;
        }
        
        .btn-delete {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        /* Create New List */
        .create-new {
            text-align: center;
            margin: 40px 0;
        }
        
        .create-new .btn {
            padding: 12px 30px;
            font-size: 16px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .empty-icon {
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .empty-state p {
            color: #777;
            margin-bottom: 20px;
        }
        
        /* Loading State */
        .loading {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #777;
        }
        
        /* Custom Modal/Popup */
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
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <!-- Main Content -->
    <section class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>הרשימות השמורות שלך</h1>
                <p>נהל את כל רשימות הקניות השמורות שלך במקום אחד</p>
            </div>

            <!-- Create New List -->
            <div class="create-new">
                <button class="btn btn-primary" onclick="showCreateListModal()">+ צור רשימה חדשה</button>
            </div>
            
            <!-- Saved Lists Section -->
            <section class="saved-lists">
                <div class="lists-grid" id="listsGrid">
                    <div class="loading">טוען רשימות...</div>
                </div>
            </section>
        </div>
    </section>

    <!-- Create List Modal -->
    <div class="modal-overlay" id="createListModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">צור רשימה חדשה</h3>
                <button class="modal-close" onclick="hideCreateListModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">שם הרשימה:</label>
                    <input type="text" class="form-input" id="listNameInput" placeholder="הזן שם לרשימה..." maxlength="100">
                </div>
                <div class="cart-summary" id="cartSummary">
                    <div class="cart-summary-title">מוצרים בסל:</div>
                    <div id="cartItems">אין מוצרים בסל</div>
                </div>
                <div class="modal-actions">
                    <button class="modal-btn btn-cancel" onclick="hideCreateListModal()">ביטול</button>
                    <button class="modal-btn btn-save" onclick="saveNewList()">שמור רשימה</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script>
        let userLists = [];

        // טעינת הרשימות בעת עליית הדף
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedLists();
        });

        // טעינת רשימות שמורות
        async function loadSavedLists() {
            try {
                const response = await fetch('get_list.php?action=get_lists');
                const data = await response.json();
                
                if (data.success) {
                    userLists = data.lists;
                    displayLists();
                } else {
                    showError('שגיאה בטעינת הרשימות');
                    displayEmptyState();
                }
            } catch (error) {
                console.error('Error loading lists:', error);
                showError('שגיאה בחיבור לשרת');
                displayEmptyState();
            }
        }

        // הצגת הרשימות
        function displayLists() {
            const listsGrid = document.getElementById('listsGrid');
            
            if (!userLists || userLists.length === 0) {
                displayEmptyState();
                return;
            }

            listsGrid.innerHTML = userLists.map(list => `
                <div class="list-card" onclick="viewList(${list.id})">
                    <div class="list-header">
                        <div class="list-title">${list.name}</div>
                        <div class="list-date">עודכן: ${list.date}</div>
                    </div>
                    <div class="list-body">
                        <div class="list-stats">
                            <div class="stat">
                                <div class="stat-value">${list.item_count}</div>
                                <div class="stat-label">מוצרים</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value">₪${list.total_price.toFixed(2)}</div>
                                <div class="stat-label">סה״כ מחיר</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value">₪${list.savings.toFixed(2)}</div>
                                <div class="stat-label">חסכון</div>
                            </div>
                        </div>
                        <div class="list-actions" onclick="event.stopPropagation()">
                            <button class="list-btn btn-view" onclick="viewList(${list.id})">צפייה</button>
                            <button class="list-btn btn-delete" onclick="deleteList(${list.id}, '${list.name}')">מחק</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // הצגת מצב ריק
        function displayEmptyState() {
            const listsGrid = document.getElementById('listsGrid');
            listsGrid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <h3>אין לך רשימות שמורות</h3>
                    <p>צור את הרשימה הראשונה שלך על ידי הוספת מוצרים לסל ושמירתם כרשימה</p>
                </div>
            `;
        }

        // הצגת מודל יצירת רשימה
        function showCreateListModal() {
            updateCartSummary();
            document.getElementById('createListModal').style.display = 'flex';
            document.getElementById('listNameInput').focus();
        }

        // הסתרת מודל יצירת רשימה
        function hideCreateListModal() {
            document.getElementById('createListModal').style.display = 'none';
            document.getElementById('listNameInput').value = '';
        }

        // עדכון סיכום הסל במודל
        function updateCartSummary() {
            let cart = [];
            try {
                const savedCart = localStorage.getItem('wisecart_cart');
                if (savedCart) {
                    cart = JSON.parse(savedCart);
                }
            } catch (e) {
                console.error('Error getting cart:', e);
            }

            const cartItemsDiv = document.getElementById('cartItems');
            
            if (!cart || cart.length === 0) {
                cartItemsDiv.innerHTML = '<p style="color: #777; text-align: center;">אין מוצרים בסל</p>';
                return;
            }

            cartItemsDiv.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <span>${item.name}</span>
                    <span>כמות: ${item.quantity}</span>
                </div>
            `).join('');
        }

        // שמירת רשימה חדשה
        async function saveNewList() {
            const listName = document.getElementById('listNameInput').value.trim();
            
            if (!listName) {
                showError('יש להזין שם לרשימה');
                return;
            }

            // בדיקת סל קניות
            let cart = [];
            try {
                const savedCart = localStorage.getItem('wisecart_cart');
                if (savedCart) {
                    cart = JSON.parse(savedCart);
                }
            } catch (e) {
                console.error('Error getting cart:', e);
            }

            if (!cart || cart.length === 0) {
                showError('הסל ריק - לא ניתן ליצור רשימה ריקה');
                return;
            }

            try {
                const response = await fetch('get_list.php?action=save_new_list', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        listName: listName,
                        items: cart.map(item => ({
                            Item_Code: item.code,
                            quantity: item.quantity
                        }))
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showSuccess('הרשימה נשמרה בהצלחה!');
                    hideCreateListModal();
                    
                    // מעבר לעמוד הצגת הרשימה
                    setTimeout(() => {
                        window.location.href = `view_saved_list.php?id=${data.listId}`;
                    }, 1500);
                } else {
                    showError('שגיאה בשמירת הרשימה: ' + (data.error || 'שגיאה לא ידועה'));
                }
            } catch (error) {
                console.error('Error saving list:', error);
                showError('שגיאה בחיבור לשרת');
            }
        }

        // צפייה ברשימה
        function viewList(listId) {
            window.location.href = `view_saved_list.php?id=${listId}`;
        }

        // מחיקת רשימה
        async function deleteList(listId, listName) {
            if (!showConfirm(`האם אתה בטוח שברצונך למחוק את הרשימה "${listName}"?`)) {
                return;
            }

            try {
                const response = await fetch('get_list.php?action=delete_list', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ listId: listId })
                });

                const data = await response.json();
                
                if (data.success) {
                    showSuccess('הרשימה נמחקה בהצלחה');
                    loadSavedLists(); // טעינה מחדש של הרשימות
                } else {
                    showError('שגיאה במחיקת הרשימה: ' + (data.error || 'שגיאה לא ידועה'));
                }
            } catch (error) {
                console.error('Error deleting list:', error);
                showError('שגיאה בחיבור לשרת');
            }
        }

        // הצגת הודעת הצלחה
        function showSuccess(message) {
            showMessage(message, 'success');
        }

        // הצגת הודעת שגיאה
        function showError(message) {
            showMessage(message, 'error');
        }

        // הצגת הודעה כללית
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

        // אישור מותאם אישית
        function showConfirm(message) {
            return confirm(message); // ניתן להחליף בפופ-אפ מותאם אישית בעתיד
        }

        // טיפול בלחיצה על Enter בשדה השם
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && document.getElementById('createListModal').style.display === 'flex') {
                saveNewList();
            }
            if (e.key === 'Escape') {
                hideCreateListModal();
            }
        });

        // סגירת מודל בלחיצה על הרקע
        document.getElementById('createListModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCreateListModal();
            }
        });
    </script>

    <script src="js/script.js"></script>
    <?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>