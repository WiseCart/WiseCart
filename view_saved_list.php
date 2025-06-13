<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>צפייה ברשימה שמורה - WiseCart</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        /* Main Content */
        .main-content {
            padding: 130px 0 60px;
        }
        
        .page-header {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-title-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .list-title {
            font-size: 28px;
            color: var(--dark-color);
            margin: 0;
        }

        .edit-title-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .edit-title-btn:hover {
            background-color: #2980b9;
        }

        .edit-title-section {
            display: none;
            align-items: center;
            gap: 10px;
        }

        .edit-title-input {
            background: #f8f9fa;
            border: 2px solid var(--primary-color);
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 18px;
            width: 300px;
        }

        .save-title-btn, .cancel-title-btn {
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .save-title-btn {
            background-color: var(--secondary-color);
            color: white;
        }

        .cancel-title-btn {
            background-color: #95a5a6;
            color: white;
        }

        .list-info {
            display: flex;
            gap: 30px;
            font-size: 16px;
            color: #666;
        }

        /* Products Section */
        .products-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 30px;
            font-size: 20px;
            font-weight: 600;
        }

        .products-grid {
            padding: 0;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s;
        }

        .product-item:hover {
            background-color: #f8f9fa;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            background-color: #f8f9fa;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 20px;
            position: relative;
            overflow: hidden;
            border: 2px dashed #dee2e6;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            position: absolute;
            top: 0;
            left: 0;
        }

        .product-image-placeholder {
            font-size: 30px;
            color: #adb5bd;
        }

        .product-details {
            flex: 1;
            padding: 0 20px;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .product-brand {
            color: #777;
            font-size: 14px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-section {
            display: flex;
            align-items: center;
            background-color: var(--light-color);
            border-radius: 25px;
            padding: 5px;
        }

        .quantity-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .quantity-btn:hover {
            background-color: #2980b9;
        }

        .quantity-number {
            padding: 0 15px;
            font-weight: 600;
            font-size: 16px;
            min-width: 40px;
            text-align: center;
        }

        .remove-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .remove-btn:hover {
            background-color: #c0392b;
        }

        /* Price Comparison */
        .price-comparison {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .comparison-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 20px 30px;
            font-size: 20px;
            font-weight: 600;
        }

        .store-comparison {
            padding: 30px;
        }

        .store-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .store-item:first-child {
            border-color: var(--secondary-color);
            background-color: rgba(46, 204, 113, 0.05);
        }

        .store-item:first-child .best-store-badge {
            display: inline-block;
        }

        .store-info {
            flex: 1;
        }

        .store-name {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .store-address {
            color: #777;
            font-size: 14px;
        }

        .best-store-badge {
            display: none;
            background-color: var(--secondary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            margin-right: 10px;
        }

        .store-price {
            text-align: left;
        }

        .price-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .currency {
            font-size: 16px;
            color: #777;
        }

        .whatsapp-btn {
            background-color: #25d366;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .whatsapp-btn:hover {
            background-color: #1ebe57;
        }

        .loading {
            text-align: center;
            padding: 40px;
            font-size: 18px;
            color: #777;
        }

        .error {
            background-color: var(--accent-color);
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
        }

        .empty-list {
            text-align: center;
            padding: 50px;
            color: #777;
        }

        .empty-list-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .list-info {
                justify-content: center;
            }

            .product-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .product-image {
                margin: 0;
            }

            .quantity-controls {
                justify-content: center;
            }

            .store-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
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
            <!-- כותרת הרשימה -->
            <div class="page-header">
                <div class="list-title-section">
                    <h1 class="list-title" id="listTitle">טוען רשימה...</h1>
                    <button class="edit-title-btn" id="editTitleBtn" onclick="editTitle()">✏️ ערוך שם</button>
                    <div class="edit-title-section" id="editTitleSection">
                        <input type="text" id="editTitleInput" class="edit-title-input" />
                        <button class="save-title-btn" onclick="saveTitle()">שמור</button>
                        <button class="cancel-title-btn" onclick="cancelEditTitle()">בטל</button>
                    </div>
                </div>
                <div class="list-info">
                    <span id="totalItems">0 פריטים</span>
                </div>
            </div>

            <!-- רשימת המוצרים -->
            <div class="products-section">
                <div class="section-header">
                    📝 המוצרים ברשימה
                </div>
                <div class="products-grid" id="productsGrid">
                    <div class="loading">טוען מוצרים...</div>
                </div>
            </div>

            <!-- השוואת מחירים -->
            <div class="price-comparison">
                <div class="comparison-header">
                    💰 השוואת מחירים בין חנויות
                </div>
                <div class="store-comparison" id="storeComparison">
                    <div class="loading">מחשב השוואת מחירים...</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script>
        let currentList = null;
        let listId = null;

        // קבלת ID הרשימה מה-URL
        function getListIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        // טעינת הרשימה השמורה (מקובץ get_list.php המעודכן)
        async function loadSavedList() {
            listId = getListIdFromUrl();
            if (!listId) {
                showError('לא נמצא מזהה רשימה');
                return;
            }

            try {
                const response = await fetch('get_list.php?action=get_single_list', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ listId: listId })
                });

                const data = await response.json();
                
                if (data.success) {
                    currentList = data.list;
                    displayList();
                    calculatePriceComparison();
                } else {
                    showError(data.error || 'שגיאה בטעינת הרשימה');
                }
            } catch (error) {
                console.error('Error loading list:', error);
                showError('שגיאה בחיבור לשרת');
            }
        }

        // הצגת הרשימה
        function displayList() {
            if (!currentList) return;

            // עדכון כותרת
            document.getElementById('listTitle').textContent = currentList.list_name;
            document.getElementById('totalItems').textContent = `${currentList.items.length} פריטים`;

            // הצגת מוצרים
            const productsGrid = document.getElementById('productsGrid');
            
            if (currentList.items.length === 0) {
                productsGrid.innerHTML = `
                    <div class="empty-list">
                        <div class="empty-list-icon">🛒</div>
                        <h3>הרשימה ריקה</h3>
                        <p>אין מוצרים ברשימה זו</p>
                    </div>
                `;
                return;
            }

            productsGrid.innerHTML = currentList.items.map(item => `
                <div class="product-item" data-item-code="${item.Item_Code}">
                    <div class="product-image">
                        <span class="product-image-placeholder">🛒</span>
                        <img src="images/${item.Item_Code}.jpg" 
                             alt="${item.Item_Name}"
                             onload="this.previousElementSibling.style.display='none'" 
                             onerror="this.style.display='none'">
                    </div>
                    <div class="product-details">
                        <div class="product-name">${item.Item_Name}</div>
                        <div class="product-brand">${item.Manufacture_Name || 'ללא מותג'}</div>
                    </div>
                    <div class="quantity-controls">
                        <div class="quantity-section">
                            <button class="quantity-btn" onclick="updateQuantity('${item.Item_Code}', -1)">-</button>
                            <span class="quantity-number">${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity('${item.Item_Code}', 1)">+</button>
                        </div>
                        <button class="remove-btn" onclick="removeProduct('${item.Item_Code}')" title="הסר מוצר">🗑️ הסר</button>
                    </div>
                </div>
            `).join('');
        }

        // עריכת שם הרשימה
        function editTitle() {
            document.getElementById('editTitleBtn').style.display = 'none';
            document.getElementById('editTitleSection').style.display = 'flex';
            document.getElementById('editTitleInput').value = currentList.list_name;
            document.getElementById('editTitleInput').focus();
        }

        function cancelEditTitle() {
            document.getElementById('editTitleBtn').style.display = 'inline-block';
            document.getElementById('editTitleSection').style.display = 'none';
        }

        async function saveTitle() {
            const newTitle = document.getElementById('editTitleInput').value.trim();
            if (!newTitle) {
                alert('יש להזין שם לרשימה');
                return;
            }

            try {
                const response = await fetch('get_list.php?action=update_list_name', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        listId: listId,
                        newName: newTitle
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    currentList.list_name = newTitle;
                    document.getElementById('listTitle').textContent = newTitle;
                    cancelEditTitle();
                } else {
                    alert(data.error || 'שגיאה בעדכון שם הרשימה');
                }
            } catch (error) {
                console.error('Error updating title:', error);
                alert('שגיאה בחיבור לשרת');
            }
        }

        // עדכון כמות מוצר
        async function updateQuantity(itemCode, change) {
            const item = currentList.items.find(item => item.Item_Code === itemCode);
            if (!item) return;

            const newQuantity = item.quantity + change;
            if (newQuantity <= 0) {
                removeProduct(itemCode);
                return;
            }

            try {
                const response = await fetch('get_list.php?action=update_item_quantity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        listId: listId,
                        itemCode: itemCode,
                        quantity: newQuantity
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    item.quantity = newQuantity;
                    displayList();
                    calculatePriceComparison();
                } else {
                    alert(data.error || 'שגיאה בעדכון הכמות');
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                alert('שגיאה בחיבור לשרת');
            }
        }

        // הסרת מוצר
        async function removeProduct(itemCode) {
            if (!confirm('האם אתה בטוח שברצונך להסיר מוצר זה?')) {
                return;
            }

            try {
                const response = await fetch('get_list.php?action=remove_item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        listId: listId,
                        itemCode: itemCode
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    currentList.items = currentList.items.filter(item => item.Item_Code !== itemCode);
                    displayList();
                    calculatePriceComparison();
                } else {
                    alert(data.error || 'שגיאה בהסרת המוצר');
                }
            } catch (error) {
                console.error('Error removing product:', error);
                alert('שגיאה בחיבור לשרת');
            }
        }

        // חישוב השוואת מחירים (בעיקר מהקוד שלך)
        async function calculatePriceComparison() {
            const comparisonDiv = document.getElementById('storeComparison');
            comparisonDiv.innerHTML = '<div class="loading">מחשב השוואת מחירים...</div>';

            if (!currentList || currentList.items.length === 0) {
                comparisonDiv.innerHTML = `
                    <div class="empty-list">
                        <p>אין מוצרים לחישוב השוואת מחירים</p>
                    </div>
                `;
                return;
            }

            try {
                const response = await fetch('get_list.php?action=compare_prices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        items: currentList.items.map(item => ({
                            Item_Code: item.Item_Code,
                            quantity: item.quantity
                        }))
                    })
                });

                const data = await response.json();
                
                if (data.success && data.stores) {
                    displayPriceComparison(data.stores);
                } else {
                    comparisonDiv.innerHTML = `
                        <div class="error">
                            שגיאה בחישוב השוואת המחירים: ${data.error || 'שגיאה לא ידועה'}
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error calculating price comparison:', error);
                comparisonDiv.innerHTML = `
                    <div class="error">
                        שגיאה בחיבור לשרת עבור השוואת מחירים
                    </div>
                `;
            }
        }

        // הצגת השוואת מחירים
        function displayPriceComparison(stores) {
            const comparisonDiv = document.getElementById('storeComparison');
            
            if (stores.length === 0) {
                comparisonDiv.innerHTML = `
                    <div class="empty-list">
                        <p>לא נמצאו נתוני מחירים עבור המוצרים ברשימה</p>
                    </div>
                `;
                return;
            }

            // מיון החנויות לפי מחיר
            stores.sort((a, b) => a.totalPrice - b.totalPrice);

            comparisonDiv.innerHTML = stores.map((store, index) => `
                <div class="store-item">
                    <div class="store-info">
                        <div class="store-name">
                            ${store.storeName}
                            <span class="best-store-badge">הכי זול!</span>
                        </div>
                        <div class="store-address">${store.address || 'כתובת לא זמינה'}</div>
                    </div>
                    <div class="store-price">
                        <div class="price-amount">₪${store.totalPrice.toFixed(2)}</div>
                        <div class="currency">סה"כ</div>
                        <button class="whatsapp-btn" onclick="sendToWhatsApp('${store.storeName}', ${store.totalPrice.toFixed(2)}, '${store.address}')">
                            📱 שלח לווטסאפ
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // שליחה לווטסאפ
        function sendToWhatsApp(storeName, totalPrice, address) {
            const listName = currentList.list_name;
            const itemsText = currentList.items.map(item => 
                `• ${item.Item_Name} (כמות: ${item.quantity})`
            ).join('\n');

            const message = `רשימת קניות: ${listName}

${itemsText}

החנות הזולה ביותר: ${storeName}
כתובת: ${address}
מחיר כולל: ₪${totalPrice}

נוצר באמצעות WiseCart 🛒`;

            const encodedMessage = encodeURIComponent(message);
            window.open(`https://wa.me/?text=${encodedMessage}`, '_blank');
        }

        // עזרים
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('he-IL') + ' ' + date.toLocaleTimeString('he-IL', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function showError(message) {
            const container = document.querySelector('.container');
            container.innerHTML = `
                <div class="error">
                    <h3>שגיאה</h3>
                    <p>${message}</p>
                    <button onclick="window.history.back()" style="margin-top: 15px; padding: 10px 20px; background: white; color: #e74c3c; border: none; border-radius: 5px; cursor: pointer;">חזור</button>
                </div>
            `;
        }

        // טעינה ראשונית
        document.addEventListener('DOMContentLoaded', () => {
            loadSavedList();
        });
    </script>
    
    <script src="js/script.js"></script>
    <?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>