// WiseCart - קוד JavaScript למערכת הפרופיל האישי
document.addEventListener('DOMContentLoaded', function() {
    // ============= ניהול טאבים =================
    setupTabs();
    
    // ============= נתוני גרף =================
    initializeCharts();
    
    // ============= ניהול העדפות =================
    setupPreferences();
    
    // ============= הגדרות פרופיל =================
    setupProfileSettings();
    
    // ============= ניהול סל קניות =================
    setupCartManagement();
    
    // ============= טיפול באינטראקציות ניווט =================
    setupNavigation();
});

// ניהול טאבים - הפונקציה כבר מיושמת חלקית בדף המקורי
function setupTabs() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // הסרת מחלקת active מכל הטאבים
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            
            // הוספת מחלקת active לטאב שנלחץ
            tab.classList.add('active');
            
            // הסתרת כל תוכן הטאב
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // הצגת תוכן הטאב המתאים
            const tabId = tab.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
}

// יצירת גרף נתוני קניות עם נתונים לדוגמה
function initializeCharts() {
    const analyticsContainer = document.querySelector('.card:nth-child(4) div:nth-child(2)');
    
    if (analyticsContainer) {
        // נמחק את הריק "גרף נתוני קניות וחיסכון" ונחליף בגרף אמיתי
        analyticsContainer.innerHTML = '<canvas id="shopping-chart" style="width: 100%; height: 200px;"></canvas>';
        
        // נתונים לדוגמה
        const months = ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי'];
        const savingsData = [125, 165, 210, 190, 245];
        const spendingData = [1250, 1320, 1180, 1290, 1340];
        
        // יצירת גרף פשוט באמצעות JavaScript טהור (בלי ספריות חיצוניות)
        const canvas = document.getElementById('shopping-chart');
        if (canvas && canvas.getContext) {
            const ctx = canvas.getContext('2d');
            const width = canvas.width = canvas.offsetWidth;
            const height = canvas.height = 200;
            
            // רקע לגרף
            ctx.fillStyle = '#f4f6f8';
            ctx.fillRect(0, 0, width, height);
            
            // כותרות
            ctx.fillStyle = '#2c3e50';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            
            // ציר X - חודשים
            const xStep = width / months.length;
            months.forEach((month, i) => {
                ctx.fillText(month, (i + 0.5) * xStep, height - 10);
            });
            
            // קו חיסכון - כחול
            const maxSaving = Math.max(...savingsData);
            ctx.beginPath();
            ctx.moveTo(xStep/2, height - (savingsData[0] / maxSaving * (height - 40)) - 20);
            
            savingsData.forEach((saving, i) => {
                ctx.lineTo((i + 0.5) * xStep, height - (saving / maxSaving * (height - 40)) - 20);
            });
            
            ctx.strokeStyle = '#3498db';
            ctx.lineWidth = 3;
            ctx.stroke();
            
            // נקודות חיסכון
            savingsData.forEach((saving, i) => {
                ctx.beginPath();
                ctx.arc((i + 0.5) * xStep, height - (saving / maxSaving * (height - 40)) - 20, 4, 0, 2 * Math.PI);
                ctx.fillStyle = '#3498db';
                ctx.fill();
            });
            
            // תווית לגרף חיסכון
            ctx.fillStyle = '#3498db';
            ctx.textAlign = 'right';
            ctx.fillText('חיסכון (₪)', width - 10, 20);
            
            // קו הוצאות - ירוק
            const maxSpending = Math.max(...spendingData);
            ctx.beginPath();
            ctx.moveTo(xStep/2, height - (spendingData[0] / maxSpending * (height - 60) * 0.3) - 20);
            
            spendingData.forEach((spend, i) => {
                ctx.lineTo((i + 0.5) * xStep, height - (spend / maxSpending * (height - 60) * 0.3) - 20);
            });
            
            ctx.strokeStyle = '#2ecc71';
            ctx.lineWidth = 3;
            ctx.stroke();
            
            // נקודות הוצאות
            spendingData.forEach((spend, i) => {
                ctx.beginPath();
                ctx.arc((i + 0.5) * xStep, height - (spend / maxSpending * (height - 60) * 0.3) - 20, 4, 0, 2 * Math.PI);
                ctx.fillStyle = '#2ecc71';
                ctx.fill();
            });
            
            // תווית לגרף הוצאות
            ctx.fillStyle = '#2ecc71';
            ctx.textAlign = 'right';
            ctx.fillText('הוצאות (₪)', width - 10, 40);
        }
    }
}

// ניהול העדפות אישיות
function setupPreferences() {
    const preferenceCards = document.querySelectorAll('.preference-card');
    
    preferenceCards.forEach(card => {
        card.addEventListener('click', () => {
            // מעבר בין מצב פעיל ללא פעיל
            card.classList.toggle('active');
            
            // הודעת אישור קטנה כאשר העדפה מתעדכנת
            showNotification(`העדפת '${card.querySelector('h3').textContent}' עודכנה`);
        });
    });
}

// ניהול הגדרות פרופיל
function setupProfileSettings() {
    const settingsForm = document.querySelector('.settings-form');
    
    if (settingsForm) {
        const saveButton = settingsForm.querySelector('.btn-primary');
        const cancelButton = settingsForm.querySelector('.btn-outline');
        
        // אירוע לחיצה על כפתור שמירה
        saveButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // לוקח את הנתונים מהטופס
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const language = document.getElementById('language').value;
            const newsletter = document.getElementById('newsletter').checked;
            const smsAlerts = document.getElementById('sms-alerts').checked;
            
            // בדיקת תקינות בסיסית
            if (!email || !email.includes('@')) {
                showNotification('נא להזין כתובת אימייל תקינה', 'error');
                return;
            }
            
            if (!phone || phone.length < 9) {
                showNotification('נא להזין מספר טלפון תקין', 'error');
                return;
            }
            
            // עדכון פרטי המשתמש בכותרת הפרופיל
            document.querySelector('.profile-info h2').textContent = 'דני ישראלי'; // אם היה לנו שדה שם בטופס היינו מעדכנים
            document.querySelector('.profile-info p').textContent = `${email} | ${phone}`;
            
            // הודעת אישור
            showNotification('הגדרות הפרופיל נשמרו בהצלחה', 'success');
        });
        
        // אירוע לחיצה על כפתור ביטול
        cancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // איפוס הטופס לערכים המקוריים
            document.getElementById('email').value = 'dani@example.com';
            document.getElementById('phone').value = '054-123-4567';
            document.getElementById('password').value = '********';
            document.getElementById('language').value = 'he';
            document.getElementById('newsletter').checked = true;
            document.getElementById('sms-alerts').checked = true;
            
            showNotification('השינויים בוטלו', 'info');
        });
        
        // אירוע לחיצה על כפתור עריכת פרופיל בכרטיס הפרופיל הראשי
        const editProfileBtn = document.querySelector('.user-profile .btn-outline');
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // גלילה אל טופס הגדרות הפרופיל
                const settingsCard = document.querySelector('.card:last-child');
                settingsCard.scrollIntoView({ behavior: 'smooth' });
                
                // הדגשה של הטופס
                settingsCard.style.boxShadow = '0 0 0 3px rgba(52, 152, 219, 0.3)';
                setTimeout(() => {
                    settingsCard.style.boxShadow = '';
                }, 2000);
            });
        }
    }
}

// ניהול סל קניות והמלצות מוצרים
function setupCartManagement() {
    // הוספת פונקציונליות לכפתורי הוספה לסל ושמירה
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const addToCartBtn = card.querySelector('.btn-outline');
        const saveBtn = card.querySelector('.btn-secondary');
        const productName = card.querySelector('.product-title').textContent;
        
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showNotification(`המוצר ${productName} נוסף לסל הקניות`, 'success');
                
                // עדכון סטטיסטיקה של סלי קניות
                const cartStat = document.querySelector('.profile-stats .stat-item:first-child .stat-value');
                if (cartStat) {
                    const currentValue = parseInt(cartStat.textContent);
                    cartStat.textContent = currentValue + 1;
                }
            });
        }
        
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showNotification(`המוצר ${productName} נשמר למועדפים`, 'info');
                
                // עדכון סטטיסטיקה של מוצרים שמורים
                const savedStat = document.querySelector('.profile-stats .stat-item:last-child .stat-value');
                if (savedStat) {
                    const currentValue = parseInt(savedStat.textContent);
                    savedStat.textContent = currentValue + 1;
                }
                
                // שינוי הטקסט והסגנון של הכפתור לאחר שמירה
                saveBtn.textContent = 'נשמר ✓';
                saveBtn.style.backgroundColor = '#7f8c8d';
                saveBtn.style.color = 'white';
                
                // נמנע לחיצות נוספות
                saveBtn.disabled = true;
            });
        }
    });
}

// ניהול ניווט בתפריט הצד
function setupNavigation() {
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // הסרת קלאס active מכל הלינקים
            sidebarLinks.forEach(l => l.classList.remove('active'));
            
            // הוספת קלאס active ללינק הנוכחי
            this.classList.add('active');
            
            const linkText = this.textContent.trim();
            
            // במקרה אמיתי היינו מפנים לדף אחר או מעדכנים את התוכן
            // כאן יוצג רק הודעת הדגמה
            showNotification(`ניווט אל: ${linkText}`, 'info');
            
            // אם זה לוח בקרה, אין סיבה לעשות משהו כי אנחנו כבר שם
            if (linkText.includes('לוח בקרה')) {
                return;
            }
            
            // הדמיה של טעינת תוכן אחר - במקרה אמיתי היינו משנים את התוכן או מפנים לדף אחר
            const contentArea = document.querySelector('.content-area');
            if (contentArea) {
                contentArea.style.opacity = '0.5';
                setTimeout(() => {
                    contentArea.style.opacity = '1';
                    showNotification(`התוכן עבור "${linkText}" יוצג בהמשך`, 'info');
                }, 1000);
            }
        });
    });
    
    // טיפול בלינקים ב"הצג הכל"
    const viewAllLinks = document.querySelectorAll('.view-all');
    viewAllLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const header = this.closest('.card-header').querySelector('h2').textContent;
            showNotification(`מציג את כל הפריטים בקטגוריה: ${header}`, 'info');
        });
    });
}

// פונקציית עזר להצגת הודעות
function showNotification(message, type = 'info') {
    // בדיקה האם כבר קיים אלמנט להודעות
    let notificationContainer = document.getElementById('notification-container');
    
    if (!notificationContainer) {
        // יצירת מיכל להודעות אם אינו קיים
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        
        // סגנון למיכל ההודעות
        Object.assign(notificationContainer.style, {
            position: 'fixed',
            top: '20px',
            left: '20px',
            zIndex: '9999',
            display: 'flex',
            flexDirection: 'column',
            gap: '10px',
            direction: 'rtl',
            textAlign: 'right'
        });
        
        document.body.appendChild(notificationContainer);
    }
    
    // יצירת הודעה חדשה
    const notification = document.createElement('div');
    
    // קביעת סוג ההודעה וצבע
    let bgColor, textColor, borderColor;
    switch (type) {
        case 'success':
            bgColor = 'rgba(46, 204, 113, 0.9)';
            textColor = 'white';
            borderColor = '#27ae60';
            break;
        case 'error':
            bgColor = 'rgba(231, 76, 60, 0.9)';
            textColor = 'white';
            borderColor = '#c0392b';
            break;
        case 'warning':
            bgColor = 'rgba(241, 196, 15, 0.9)';
            textColor = '#222';
            borderColor = '#f39c12';
            break;
        case 'info':
        default:
            bgColor = 'rgba(52, 152, 219, 0.9)';
            textColor = 'white';
            borderColor = '#2980b9';
    }
    
    // עיצוב ההודעה
    Object.assign(notification.style, {
        backgroundColor: bgColor,
        color: textColor,
        padding: '12px 20px',
        borderRadius: '5px',
        boxShadow: '0 3px 10px rgba(0,0,0,0.2)',
        marginBottom: '10px',
        borderRight: `4px solid ${borderColor}`,
        minWidth: '250px',
        maxWidth: '350px',
        fontWeight: '500'
    });
    
    notification.textContent = message;
    
    // הוספת ההודעה למיכל
    notificationContainer.prepend(notification);
    
    // הנפשה לכניסת ההודעה
    notification.animate([
        { transform: 'translateX(-20px)', opacity: 0 },
        { transform: 'translateX(0)', opacity: 1 }
    ], {
        duration: 300,
        easing: 'ease-out'
    });
    
    // סגירה אוטומטית לאחר 5 שניות
    setTimeout(() => {
        // הנפשת יציאה
        const anim = notification.animate([
            { transform: 'translateX(0)', opacity: 1 },
            { transform: 'translateX(-20px)', opacity: 0 }
        ], {
            duration: 300,
            easing: 'ease-in'
        });
        
        anim.onfinish = () => notification.remove();
    }, 5000);
    
    // הוספת אפשרות סגירה בלחיצה
    notification.style.cursor = 'pointer';
    notification.addEventListener('click', () => {
        notification.remove();
    });
}