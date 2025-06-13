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
    
    // עדכון כותרת הסל
    const cartTitle = document.getElementById('cartTitle');
    const cartTitleMobile = document.getElementById('cartTitleMobile');
    
    // קבלת שם הרשימה מה-localStorage
    const savedListName = localStorage.getItem('current_list_name');
    const title = savedListName || 'הרשימה שלי';
    
    console.log('Title:', title); // עכשיו השורה במקום הנכון
    
    if (cartTitle) cartTitle.textContent = title;
    if (cartTitleMobile) cartTitleMobile.textContent = title;
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

document.addEventListener('DOMContentLoaded', () => {
    loadCartFromStorage(); // טוען את הסל אם יש פריטים
});