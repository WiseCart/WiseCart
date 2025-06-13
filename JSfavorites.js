// WiseCart - מערכת ניהול מוצרים מועדפים
document.addEventListener('DOMContentLoaded', function() {
    // אובייקט לניהול המוצרים המועדפים
    const FavoritesManager = {
        // רשימת המוצרים המועדפים
        favorites: [],
        
        // אתחול המערכת
        init: function() {
            this.loadFavorites();
            this.setupEventListeners();
            this.renderFavorites();
            this.setupFilters();
        },
        
        // טעינת המוצרים המועדפים מה-localStorage
        loadFavorites: function() {
            const savedFavorites = localStorage.getItem('wisecart-favorites');
            if (savedFavorites) {
                try {
                    this.favorites = JSON.parse(savedFavorites);
                } catch (e) {
                    console.error('שגיאה בטעינת המוצרים המועדפים:', e);
                    this.favorites = [];
                }
            } else {
                // דוגמאות מוצרים לדף הראשון
                this.favorites = this.getSampleProducts();
            }
        },
        
        // שמירת המוצרים המועדפים ב-localStorage
        saveFavorites: function() {
            localStorage.setItem('wisecart-favorites', JSON.stringify(this.favorites));
        },
        
        // הגדרת מאזיני אירועים
        setupEventListeners: function() {
            // הוספת מאזיני אירועים לכפתורי המועדפים
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const productCard = btn.closest('.product-card');
                    const productId = productCard.dataset.productId || this.generateProductId(productCard);
                    this.toggleFavorite(productId, productCard);
                });
            });
            
            // הוספת מאזיני אירועים לכפתורי הוספה לסל
            document.querySelectorAll('.add-to-cart').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productCard = btn.closest('.product-card');
                    const productId = productCard.dataset.productId || this.generateProductId(productCard);
                    const productName = productCard.querySelector('.product-title').textContent;
                    this.addToCart(productId, productName);
                });
            });
            
            // הוספת מאזיני אירועים לכפתורי השוואה
            document.querySelectorAll('.compare-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productCard = btn.closest('.product-card');
                    const productId = productCard.dataset.productId || this.generateProductId(productCard);
                    const productName = productCard.querySelector('.product-title').textContent;
                    this.addToCompare(productId, productName);
                });
            });
            
            // כפתורי תצוגה (רשת/רשימה)
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    // שינוי תצוגת המוצרים
                    if (btn.querySelector('svg path')) { // בדיקה אם זה כפתור תצוגת רשימה
                        document.querySelector('.products-grid').classList.add('list-view');
                    } else {
                        document.querySelector('.products-grid').classList.remove('list-view');
                    }
                });
            });
            
            // מיון מוצרים
            const sortingSelect = document.querySelector('.sorting select');
            if (sortingSelect) {
                sortingSelect.addEventListener('change', () => {
                    this.sortProducts(sortingSelect.value);
                });
            }
            
            // תגיות
            document.querySelectorAll('.tag').forEach(tag => {
                tag.addEventListener('click', () => {
                    tag.classList.toggle('active');
                    this.applyFilters();
                });
            });
            
            // טווח מחירים
            const minPriceInput = document.querySelector('.price-input:first-child input');
            const maxPriceInput = document.querySelector('.price-input:last-child input');
            const leftHandle = document.querySelector('.range-handle.left');
            const rightHandle = document.querySelector('.range-handle.right');
            
            // עדכון טווח מחירים כאשר משנים את הערכים בשדות
            if (minPriceInput && maxPriceInput) {
                minPriceInput.addEventListener('change', () => {
                    this.updatePriceRange(minPriceInput.value, maxPriceInput.value);
                    this.applyFilters();
                });
                
                maxPriceInput.addEventListener('change', () => {
                    this.updatePriceRange(minPriceInput.value, maxPriceInput.value);
                    this.applyFilters();
                });
            }
            
            // קטגוריות
            document.querySelectorAll('.category-list a').forEach(categoryLink => {
                categoryLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    const category = categoryLink.textContent.split(' ')[0]; // לוקח רק את שם הקטגוריה, לא את המספר
                    this.filterByCategory(category);
                });
            });
            
            // כפתור "טען עוד מוצרים"
            const loadMoreBtn = document.querySelector('.view-more .btn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.loadMoreProducts();
                });
            }
        },
        
        // הצגת המוצרים המועדפים
        renderFavorites: function() {
            const productsGrid = document.querySelector('.products-grid');
            if (!productsGrid) return;
            
            // אם אין מוצרים מועדפים, הצג הודעה
            if (this.favorites.length === 0) {
                productsGrid.innerHTML = this.getEmptyStateHTML();
                document.querySelector('.view-more').style.display = 'none';
                return;
            }
            
            // הצג את המוצרים
            productsGrid.innerHTML = '';
            const displayLimit = 8; // מספר המוצרים להצגה בדף
            
            this.favorites.slice(0, displayLimit).forEach(product => {
                productsGrid.appendChild(this.createProductCard(product));
            });
            
            // הסתר/הצג כפתור "טען עוד" בהתאם
            document.querySelector('.view-more').style.display = 
                this.favorites.length > displayLimit ? 'block' : 'none';
            
            // הוספת נתוני מוצר לכרטיסים
            document.querySelectorAll('.product-card').forEach((card, index) => {
                if (index < this.favorites.length) {
                    card.dataset.productId = this.favorites[index].id;
                }
            });
        },
        
        // יצירת כרטיס מוצר
        createProductCard: function(product) {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.dataset.productId = product.id;
            card.dataset.price = product.price;
            card.dataset.category = product.category;
            
            // הוספת תגיות למוצר (אם יש)
            if (product.tags && product.tags.length) {
                card.dataset.tags = product.tags.join(',');
            }
            
            card.innerHTML = `
                <div class="favorite-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                <div class="product-image">
                    <img src="${product.image || '/api/placeholder/160/160'}" alt="${product.name}">
                </div>
                <div class="product-info">
                    <div class="product-category">${product.category}</div>
                    <h3 class="product-title">${product.name}</h3>
                    <div class="product-price">
                        <div class="price-info">
                            <span class="price">${product.price.toFixed(2)} ₪</span>
                            <span class="store">הזול ביותר: <span class="store-highlight">${product.cheapestStore}</span></span>
                        </div>
                    </div>
                    <div class="action-btns">
                        <button class="add-to-cart">הוסף לסל</button>
                        <button class="compare-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="17 1 21 5 17 9"></polyline>
                                <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                                <polyline points="7 23 3 19 7 15"></polyline>
                                <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            return card;
        },
        
        // הוספה/הסרה של מוצר מהמועדפים
        toggleFavorite: function(productId, productCard) {
            const index = this.favorites.findIndex(p => p.id === productId);
            
            if (index !== -1) {
                // הסרה מהמועדפים
                this.favorites.splice(index, 1);
                this.showNotification('המוצר הוסר מהמועדפים', 'success');
                
                // הסרת הכרטיס מהתצוגה בהנפשה
                productCard.style.opacity = '1';
                setTimeout(() => {
                    productCard.style.transition = 'all 0.3s ease-out';
                    productCard.style.opacity = '0';
                    productCard.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        productCard.remove();
                        
                        // אם אין יותר מוצרים, הצג מצב ריק
                        if (this.favorites.length === 0) {
                            document.querySelector('.products-grid').innerHTML = this.getEmptyStateHTML();
                            document.querySelector('.view-more').style.display = 'none';
                        }
                    }, 300);
                }, 10);
            } else {
                // במקרה שמנסים להוסיף מוצר חדש למועדפים - במקרה של דף מועדפים זה לא אמור לקרות
                console.warn('ניסיון להוסיף מוצר חדש למועדפים בדף המועדפים');
            }
            
            this.saveFavorites();
            this.updateCategoryCounts();
        },
        
        // הוספת מוצר לסל הקניות
        addToCart: function(productId, productName) {
            // במציאות היה צריך להוסיף לסל קניות ששמור ב-localStorage או שליחה לשרת
            // כאן נציג רק הודעה
            this.showNotification(`המוצר "${productName}" נוסף לסל הקניות`, 'success');
            
            // כאן יש להוסיף קוד שמוסיף את המוצר לסל הקניות הנוכחי
            const cart = JSON.parse(localStorage.getItem('wisecart-cart') || '[]');
            const product = this.favorites.find(p => p.id === productId);
            
            if (product) {
                // בדיקה אם המוצר כבר קיים בסל
                const existingProduct = cart.find(p => p.id === productId);
                if (existingProduct) {
                    existingProduct.quantity = (existingProduct.quantity || 1) + 1;
                } else {
                    cart.push({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        category: product.category,
                        image: product.image,
                        quantity: 1
                    });
                }
                
                localStorage.setItem('wisecart-cart', JSON.stringify(cart));
                this.updateCartCounter();
            }
        },
        
        // הוספת מוצר להשוואה
        addToCompare: function(productId, productName) {
            // במציאות היה צריך להוסיף למערכת השוואה ששמורה ב-localStorage או שליחה לשרת
            // כאן נציג רק הודעה
            this.showNotification(`המוצר "${productName}" נוסף להשוואה`, 'info');
            
            const compare = JSON.parse(localStorage.getItem('wisecart-compare') || '[]');
            const product = this.favorites.find(p => p.id === productId);
            
            if (product) {
                // בדיקה אם המוצר כבר קיים בהשוואה
                if (!compare.some(p => p.id === productId)) {
                    // מגבלה של 4 מוצרים בהשוואה
                    if (compare.length >= 4) {
                        this.showNotification('ניתן להשוות עד 4 מוצרים בו-זמנית', 'warning');
                        return;
                    }
                    
                    compare.push({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        category: product.category,
                        image: product.image
                    });
                    
                    localStorage.setItem('wisecart-compare', JSON.stringify(compare));
                    this.updateCompareCounter();
                } else {
                    this.showNotification('המוצר כבר נמצא בהשוואה', 'info');
                }
            }
        },
        
        // עדכון מספר מוצרים בסל
        updateCartCounter: function() {
            // כאן אפשר להוסיף קוד שמעדכן את מספר המוצרים בסל שמוצג בכותרת העליונה
            const cart = JSON.parse(localStorage.getItem('wisecart-cart') || '[]');
            const cartCounter = document.querySelector('#cart-counter');
            
            if (cartCounter) {
                const itemCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);
                cartCounter.textContent = itemCount;
                cartCounter.style.display = itemCount > 0 ? 'block' : 'none';
            }
        },
        
        // עדכון מספר מוצרים בהשוואה
        updateCompareCounter: function() {
            // כאן אפשר להוסיף קוד שמעדכן את מספר המוצרים בהשוואה שמוצג בכותרת העליונה
            const compare = JSON.parse(localStorage.getItem('wisecart-compare') || '[]');
            const compareCounter = document.querySelector('#compare-counter');
            
            if (compareCounter) {
                const itemCount = compare.length;
                compareCounter.textContent = itemCount;
                compareCounter.style.display = itemCount > 0 ? 'block' : 'none';
            }
        },
        
        // מיון מוצרים
        sortProducts: function(sortBy) {
            const productsGrid = document.querySelector('.products-grid');
            if (!productsGrid) return;
            
            const cards = Array.from(productsGrid.querySelectorAll('.product-card'));
            
            cards.sort((a, b) => {
                switch (sortBy) {
                    case 'price-low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'popularity':
                        // במציאות היה צריך להיות שדה פופולריות, כאן נשתמש במספר אקראי לדוגמה
                        return Math.random() - 0.5;
                    case 'sale':
                        // במציאות היה צריך להיות סימון של מבצע, כאן נשתמש בבדיקה אם יש תגית 'מבצע'
                        const aHasSale = a.dataset.tags && a.dataset.tags.includes('מבצע');
                        const bHasSale = b.dataset.tags && b.dataset.tags.includes('מבצע');
                        return bHasSale - aHasSale;
                    case 'recent':
                        // במציאות היה צריך להיות תאריך הוספה, כאן נשתמש בסדר הנוכחי
                        return 0;
                    default:
                        return 0;
                }
            });
            
            // עדכון התצוגה לפי המיון החדש
            cards.forEach(card => {
                productsGrid.appendChild(card);
            });
        },
        
        // הגדרת מסננים
        setupFilters: function() {
            // סידור ידיות טווח המחירים
            const rangeSlider = document.querySelector('.range-slider');
            const leftHandle = document.querySelector('.range-handle.left');
            const rightHandle = document.querySelector('.range-handle.right');
            const rangeSelected = document.querySelector('.range-selected');
            
            if (rangeSlider && leftHandle && rightHandle && rangeSelected) {
                // הגדרת משתנים לנגררים
                let isDragging = false;
                let currentHandle = null;
                let startX = 0;
                let startLeft = 0;
                
                // פונקציה לעדכון מראה הסליידר
                const updateRangeSlider = (handle, newPosition) => {
                    const sliderWidth = rangeSlider.offsetWidth;
                    let newLeft = Math.max(0, Math.min(newPosition, sliderWidth));
                    
                    if (handle === leftHandle) {
                        // הגבלת הידית השמאלית שלא תעבור את הידית הימנית
                        const rightPos = parseInt(rightHandle.style.left || '70', 10);
                        newLeft = Math.min(newLeft, rightPos - 10);
                        
                        leftHandle.style.left = newLeft + 'px';
                        rangeSelected.style.left = newLeft + 'px';
                        rangeSelected.style.width = (rightPos - newLeft) + 'px';
                        
                        // עדכון ערך מינימום
                        const minValue = Math.round((newLeft / sliderWidth) * 100);
                        document.querySelector('.price-input:first-child input').value = minValue;
                    } else {
                        // הגבלת הידית הימנית שלא תעבור את הידית השמאלית
                        const leftPos = parseInt(leftHandle.style.left || '0', 10);
                        newLeft = Math.max(newLeft, leftPos + 10);
                        
                        rightHandle.style.left = newLeft + 'px';
                        rangeSelected.style.width = (newLeft - parseInt(rangeSelected.style.left || '0', 10)) + 'px';
                        
                        // עדכון ערך מקסימום
                        const maxValue = Math.round((newLeft / sliderWidth) * 100);
                        document.querySelector('.price-input:last-child input').value = maxValue;
                    }
                };
                
                // אירועי עכבר לנגרר
                [leftHandle, rightHandle].forEach(handle => {
                    handle.addEventListener('mousedown', function(e) {
                        isDragging = true;
                        currentHandle = this;
                        startX = e.clientX;
                        startLeft = parseInt(this.style.left || (this === leftHandle ? '0' : '70'), 10);
                        e.preventDefault();
                    });
                });
                
                document.addEventListener('mousemove', function(e) {
                    if (!isDragging) return;
                    
                    const deltaX = e.clientX - startX;
                    const newPosition = startLeft + deltaX;
                    
                    updateRangeSlider(currentHandle, newPosition);
                });
                
                document.addEventListener('mouseup', function() {
                    if (isDragging) {
                        isDragging = false;
                        // הפעלת סינון לאחר שחרור הידית
                        FavoritesManager.applyFilters();
                    }
                });
                
                // אירועי מגע לתמיכה במובייל
                [leftHandle, rightHandle].forEach(handle => {
                    handle.addEventListener('touchstart', function(e) {
                        isDragging = true;
                        currentHandle = this;
                        startX = e.touches[0].clientX;
                        startLeft = parseInt(this.style.left || (this === leftHandle ? '0' : '70'), 10);
                        e.preventDefault();
                    });
                });
                
                document.addEventListener('touchmove', function(e) {
                    if (!isDragging) return;
                    
                    const deltaX = e.touches[0].clientX - startX;
                    const newPosition = startLeft + deltaX;
                    
                    updateRangeSlider(currentHandle, newPosition);
                });
                
                document.addEventListener('touchend', function() {
                    if (isDragging) {
                        isDragging = false;
                        // הפעלת סינון לאחר שחרור הידית
                        FavoritesManager.applyFilters();
                    }
                });
            }
        },
        
        // עדכון טווח מחירים
        updatePriceRange: function(min, max) {
            const rangeSlider = document.querySelector('.range-slider');
            const leftHandle = document.querySelector('.range-handle.left');
            const rightHandle = document.querySelector('.range-handle.right');
            const rangeSelected = document.querySelector('.range-selected');
            
            if (rangeSlider && leftHandle && rightHandle && rangeSelected) {
                const sliderWidth = rangeSlider.offsetWidth;
                
                // חישוב המיקום החדש של הידיות
                const leftPos = (min / 100) * sliderWidth;
                const rightPos = (max / 100) * sliderWidth;
                
                // עדכון מיקום הידיות והקטע הנבחר
                leftHandle.style.left = leftPos + 'px';
                rightHandle.style.left = rightPos + 'px';
                rangeSelected.style.left = leftPos + 'px';
                rangeSelected.style.width = (rightPos - leftPos) + 'px';
            }
        },
        
        // הפעלת כל המסננים
        applyFilters: function() {
            const productsGrid = document.querySelector('.products-grid');
            if (!productsGrid) return;
            
            const cards = Array.from(productsGrid.querySelectorAll('.product-card'));
            
            // מסנן מחיר
            const minPrice = parseInt(document.querySelector('.price-input:first-child input').value || '0', 10);
            const maxPrice = parseInt(document.querySelector('.price-input:last-child input').value || '100', 10);
            
            // מסנן תגיות
            const activeTags = Array.from(document.querySelectorAll('.tag.active')).map(tag => tag.textContent.trim());
            
            cards.forEach(card => {
                const price = parseFloat(card.dataset.price || '0');
                const cardTags = card.dataset.tags ? card.dataset.tags.split(',') : [];
                
                let isVisible = true;
                
                // בדיקת טווח מחירים
                if (price < minPrice || price > maxPrice) {
                    isVisible = false;
                }
                
                // בדיקת תגיות
                if (activeTags.length > 0 && !activeTags.some(tag => cardTags.includes(tag))) {
                    isVisible = false;
                }
                
                // עדכון נראות הכרטיס
                card.style.display = isVisible ? '' : 'none';
            });
        },
        
        // סינון לפי קטגוריה
        filterByCategory: function(category) {
            const productsGrid = document.querySelector('.products-grid');
            if (!productsGrid) return;
            
            const cards = Array.from(productsGrid.querySelectorAll('.product-card'));
            const allCategoriesOption = category === 'כל' || category === 'כל הקטגוריות';
            
            cards.forEach(card => {
                const cardCategory = card.dataset.category;
                card.style.display = (allCategoriesOption || cardCategory === category) ? '' : 'none';
            });
            
            // עדכון כותרת בהתאם לקטגוריה שנבחרה
            const categoryTitle = document.querySelector('.page-header h1');
            if (categoryTitle) {
                categoryTitle.textContent = allCategoriesOption ? 
                    'המוצרים המועדפים שלי' : 
                    `המוצרים המועדפים שלי - ${category}`;
            }
        },
        
        // טעינת מוצרים נוספים
        loadMoreProducts: function() {
            const productsGrid = document.querySelector('.products-grid');
            if (!productsGrid) return;
            
            const currentCount = productsGrid.querySelectorAll('.product-card').length;
            const nextBatch = 8; // מספר המוצרים לטעינה בכל פעם
            
            // אם אין עוד מוצרים להציג
            if (currentCount >= this.favorites.length) {
                this.showNotification('אין מוצרים נוספים להצגה', 'info');
                document.querySelector('.view-more').style.display = 'none';
                return;
            }
            
            // הוספת המוצרים הבאים
            const endIndex = Math.min(currentCount + nextBatch, this.favorites.length);
            
            for (let i = currentCount; i < endIndex; i++) {
                productsGrid.appendChild(this.createProductCard(this.favorites[i]));
            }
            
            // הסתרת כפתור "טען עוד" אם הגענו לסוף הרשימה
            if (endIndex >= this.favorites.length) {
                document.querySelector('.view-more').style.display = 'none';
            }
            
            // הוספת מאזיני אירועים לכרטיסים החדשים
            this.setupEventListeners();
        },
        
        // עדכון מספרי מוצרים בקטגוריות
        updateCategoryCounts: function() {
            const categoryCounts = {};
            
            // ספירת מוצרים בכל קטגוריה
            this.favorites.forEach(product => {
                categoryCounts[product.category] = (categoryCounts[product.category] || 0) + 1;
            });
            
            // עדכון המספרים בתפריט הקטגוריות
            document.querySelectorAll('.category-list a').forEach(link => {
                const category = link.textContent.split(' ')[0]; // לוקח רק את שם הקטגוריה
                const countSpan = link.querySelector('.category-count');
                
                if (countSpan) {
                    if (category === 'כל') {
                        // עדכון "כל הקטגוריות"
                        countSpan.textContent = this.favorites.length;
                    } else {
                        countSpan.textContent = categoryCounts[category] || 0;
                    }
                }
            });
        },
        
       // יצירת מזהה ייחודי למוצר
        generateProductId: function(productCard) {
            const name = productCard.querySelector('.product-title').textContent;
            // יצירת מזהה מבוסס על שם המוצר + תאריך אקראי
            return 'product_' + name.replace(/\s+/g, '_').toLowerCase() + '_' + Date.now();
        },
        
        // שליפת מוצרים לדוגמה
        getSampleProducts: function() {
            return [
                {
                    id: 'product_1',
                    name: 'חלב טרי 3%',
                    price: 5.90,
                    category: 'מוצרי חלב',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'שופרסל',
                    tags: ['יומיומי', 'בריא']
                },
                {
                    id: 'product_2',
                    name: 'לחם אחיד פרוס',
                    price: 7.50,
                    category: 'מאפים',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'רמי לוי',
                    tags: ['יומיומי']
                },
                {
                    id: 'product_3',
                    name: 'קוקה קולה 1.5 ליטר',
                    price: 6.90,
                    category: 'משקאות',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'יינות ביתן',
                    tags: ['משקאות', 'מבצע']
                },
                {
                    id: 'product_4',
                    name: 'ביצים L אורגניות',
                    price: 24.90,
                    category: 'ביצים',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'שופרסל',
                    tags: ['אורגני', 'בריא']
                },
                {
                    id: 'product_5',
                    name: 'גבינה צהובה 28%',
                    price: 24.90,
                    category: 'מוצרי חלב',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'מגה',
                    tags: ['יומיומי']
                },
                {
                    id: 'product_6',
                    name: 'שמן זית כתית מעולה',
                    price: 39.90,
                    category: 'שמנים',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'רמי לוי',
                    tags: ['בריא']
                },
                {
                    id: 'product_7',
                    name: 'דגני בוקר משפחתיים',
                    price: 19.90,
                    category: 'מזון יבש',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'שופרסל',
                    tags: ['יומיומי', 'מבצע']
                },
                {
                    id: 'product_8',
                    name: 'תה ירוק אורגני',
                    price: 18.50,
                    category: 'משקאות',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'עדן טבע',
                    tags: ['אורגני', 'בריא']
                },
                {
                    id: 'product_9',
                    name: 'מלפפונים אורגניים',
                    price: 12.90,
                    category: 'ירקות',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'עדן טבע',
                    tags: ['אורגני', 'בריא']
                },
                {
                    id: 'product_10',
                    name: 'נייר טואלט רך 40 גלילים',
                    price: 45.90,
                    category: 'ניקיון',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'יש חסד',
                    tags: ['חסכוני']
                },
                {
                    id: 'product_11',
                    name: 'אבקת כביסה 5 ק"ג',
                    price: 39.90,
                    category: 'ניקיון',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'רמי לוי',
                    tags: ['חסכוני']
                },
                {
                    id: 'product_12',
                    name: 'מיץ תפוזים סחוט טרי',
                    price: 14.90,
                    category: 'משקאות',
                    image: '/api/placeholder/150/150',
                    cheapestStore: 'ויקטורי',
                    tags: ['בריא', 'מבצע']
                }
            ];
        },
        
        // הצגת מצב ריק (כשאין מוצרים מועדפים)
        getEmptyStateHTML: function() {
            return `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </div>
                    <h3>אין מוצרים מועדפים</h3>
                    <p>המוצרים המועדפים שלך יופיעו כאן לאחר שתסמן אותם בדפי המוצרים</p>
                    <a href="index.html" class="btn primary">לדף הראשי</a>
                </div>
            `;
        },
        
        // הצגת הודעות למשתמש
        showNotification: function(message, type = 'info') {
            // יצירת אלמנט ההודעה
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon">
                        ${this.getNotificationIcon(type)}
                    </div>
                    <div class="notification-message">${message}</div>
                </div>
                <div class="notification-close">×</div>
            `;
            
            // הוספת ההודעה לדף
            const notificationsContainer = document.querySelector('.notifications-container');
            if (!notificationsContainer) {
                // אם אין מיכל להודעות, צור אחד
                const container = document.createElement('div');
                container.className = 'notifications-container';
                document.body.appendChild(container);
                container.appendChild(notification);
            } else {
                notificationsContainer.appendChild(notification);
            }
            
            // הוספת מאזין אירועים לכפתור הסגירה
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.classList.add('hiding');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            });
            
            // הסרה אוטומטית של ההודעה לאחר 4 שניות
            setTimeout(() => {
                notification.classList.add('hiding');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 4000);
        },
        
        // איקון להודעה לפי סוג
        getNotificationIcon: function(type) {
            switch (type) {
                case 'success':
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>`;
                case 'warning':
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>`;
                case 'error':
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>`;
                case 'info':
                default:
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>`;
            }
        }
    };
    
    // אתחול מערכת המועדפים
    FavoritesManager.init();
    
    // עדכון מספרי המוצרים בסל ובהשוואה בעת טעינת הדף
    FavoritesManager.updateCartCounter();
    FavoritesManager.updateCompareCounter();
    
    // טיפול בלחצן החיפוש
    const searchButton = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');
    
    if (searchButton && searchInput) {
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // בדיקה אם תיבת החיפוש פתוחה
            const isExpanded = searchInput.classList.contains('expanded');
            
            if (isExpanded && searchInput.value.trim()) {
                // ביצוע חיפוש
                performSearch(searchInput.value.trim());
            } else {
                // החלפת מצב תיבת החיפוש
                searchInput.classList.toggle('expanded');
                
                if (!isExpanded) {
                    // אם תיבת החיפוש נפתחה, מיקוד בה
                    setTimeout(() => {
                        searchInput.focus();
                    }, 100);
                }
            }
        });
        
        // אפשר חיפוש גם בלחיצה על Enter
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter' && this.value.trim()) {
                performSearch(this.value.trim());
            }
        });
    }
    
    // פונקציית חיפוש
    function performSearch(query) {
        // המרת החיפוש לאותיות קטנות לצורך השוואה (case-insensitive)
        query = query.toLowerCase();
        
        const productsGrid = document.querySelector('.products-grid');
        if (!productsGrid) return;
        
        // בדיקה אם יש תוצאות חיפוש
        let hasResults = false;
        
        // חיפוש בכל המוצרים
        const cards = Array.from(productsGrid.querySelectorAll('.product-card'));
        
        cards.forEach(card => {
            const productName = card.querySelector('.product-title').textContent.toLowerCase();
            const productCategory = card.querySelector('.product-category').textContent.toLowerCase();
            
            // בדיקה אם המוצר מתאים לחיפוש
            if (productName.includes(query) || productCategory.includes(query)) {
                card.style.display = '';
                hasResults = true;
            } else {
                card.style.display = 'none';
            }
        });
        
        // אם אין תוצאות, הצג הודעה מתאימה
        if (!hasResults) {
            FavoritesManager.showNotification(`לא נמצאו תוצאות עבור "${query}"`, 'info');
        } else {
            FavoritesManager.showNotification(`נמצאו תוצאות עבור "${query}"`, 'success');
        }
    }
    
    // טיפול בתפריט הניווט במובייל
    const menuButton = document.querySelector('.menu-button');
    const mobileNav = document.querySelector('.mobile-nav');
    
    if (menuButton && mobileNav) {
        menuButton.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
        
        // סגירת התפריט בלחיצה מחוץ לתפריט
        document.addEventListener('click', function(e) {
            if (mobileNav.classList.contains('active') && 
                !e.target.closest('.mobile-nav') && 
                !e.target.closest('.menu-button')) {
                mobileNav.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
    
    // אירועי גלילה לשינוי סטייל הכותרת
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (header) {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });
    
    // אירועי לחיצה על הכרטיסיות במידע על המוצר
    const productTabs = document.querySelectorAll('.product-tabs .tab');
    if (productTabs.length > 0) {
        productTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // הסרת הכרטיסייה הפעילה הנוכחית
                const activeTab = document.querySelector('.product-tabs .tab.active');
                if (activeTab) activeTab.classList.remove('active');
                
                // הסרת התוכן הפעיל הנוכחי
                const activeContent = document.querySelector('.tab-content.active');
                if (activeContent) activeContent.classList.remove('active');
                
                // הפעלת הכרטיסייה והתוכן החדשים
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                const tabContent = document.querySelector(`.tab-content[data-tab="${tabId}"]`);
                if (tabContent) tabContent.classList.add('active');
            });
        });
    }
    
    // אירועי לחיצה על כפתור "חזרה למעלה"
    const backToTopBtn = document.querySelector('.back-to-top');
    if (backToTopBtn) {
        // הצגת הכפתור בגלילה למטה
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        // חזרה לראש הדף בלחיצה על הכפתור
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});