<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>הרשמה - WiseCart</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>

        /* REGISTRATION SECTION */
        .registration{padding:60px 0 80px;background:linear-gradient(135deg,var(--primary-color),var(--secondary-color))}
        .registration-container{max-width:600px;margin:0 auto;background:#fff;border-radius:10px;padding:40px;box-shadow:0 10px 30px rgba(0,0,0,.1)}
        .registration-title{text-align:center;margin-bottom:30px}
        .registration-title h1{font-size:32px;color:var(--dark-color);margin-bottom:10px}
        .form-row{display:flex;gap:20px}
        .form-group{margin-bottom:20px;flex:1}
        .form-group label{display:block;margin-bottom:8px;font-weight:500;color:var(--dark-color)}
        .form-control{width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:5px;font-size:16px;transition:border-color .3s, transform 0.2s, box-shadow 0.2s}
        .form-control:focus{border-color:var(--primary-color);outline:none;transform:translateY(-1px);box-shadow:0 4px 8px rgba(0,0,0,.1)}

        /* סגנונות לשדות סיסמה עם כפתור הצגה/הסתרה */
        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            z-index: 2;
            color: #666;
            padding: 4px 8px;
            border-radius: 3px;
            transition: all 0.2s;
            font-weight: 500;
        }
        .password-toggle:hover {
            color: var(--primary-color);
            background-color: #f0f8ff;
        }
        .form-control.has-toggle {
            padding-left: 80px;
        }

        /* אינדיקטורים לחוזק סיסמה והתאמה */
        .password-strength-container {
            margin-top: 8px;
        }
        
        /* סרגל התקדמות חוזק סיסמה */
        .password-strength-bar {
            height: 5px;
            width: 100%;
            background: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .password-strength-fill {
            height: 100%;
            width: 0%;
            background: #e74c3c;
            transition: width 0.3s, background 0.3s;
            border-radius: 3px;
        }
        
        .password-strength,
        .password-match {
            font-size: 14px;
            margin-top: 5px;
            padding: 8px;
            border-radius: 4px;
            display: block;
            transition: all 0.3s ease;
        }
        .password-strength.weak {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .password-strength.medium {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
        }
        .password-strength.strong {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .password-match.success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .password-match.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        /* סגנונות לשדות עם הצלחה או שגיאה */
        .form-control.success {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .form-control.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* עיצוב מיוחד לדרופ דאון ערים - תיקון חצים כפולים */
        #city {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: left 12px center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
            padding-left: 40px;
        }

        .terms-checkbox{display:flex;align-items:flex-start;gap:10px;margin-bottom:20px}
        .submit-btn{width:100%;padding:14px;background:var(--secondary-color);color:#fff;border:none;border-radius:5px;font-size:16px;font-weight:500;cursor:pointer;transition:.3s}
        .submit-btn:hover{background:#27ae60}
        .submit-btn:disabled{background:#ccc;cursor:not-allowed}
        .divider{display:flex;align-items:center;margin:25px 0;color:#999}
        .divider::before,.divider::after{content:"";flex:1;height:1px;background:#ddd}
        .divider span{padding:0 15px}
        .social-login{display:flex;gap:15px}
        .social-btn{flex:1;padding:10px;border:1px solid #ddd;border-radius:5px;background:#fff;display:flex;justify-content:center;align-items:center;gap:10px;cursor:pointer;transition:.3s}
        .social-btn:hover{background:#f5f5f5}
        .login-link{text-align:center;margin-top:20px;color:#666}
        .login-link a{color:var(--primary-color);text-decoration:none;font-weight:500}
        .login-link a:hover{text-decoration:underline}

        /* הודעת שגיאה/הצלחה */
        .message{text-align:center;margin-top:15px;padding:12px;border-radius:5px;font-weight:600;display:none}
        .message.error{color:#721c24;background-color:#f8d7da;border:1px solid #f5c6cb}
        .message.success{color:#155724;background-color:#d4edda;border:1px solid #c3e6cb}

        /* RESPONSIVE */
        @media(max-width:768px){
          .header-content{flex-direction:column;gap:15px}
          nav ul{gap:15px}
          .form-row{flex-direction:column;gap:0}
          .registration-container{padding:25px}
          .social-login{flex-direction:column}
          .password-toggle{left:10px}
          .form-control.has-toggle{padding-left:70px}
        }
        
        /* וידוא שסיסמאות מוסתרות כברירת מחדל */
        input[name="password"], 
        input[name="confirm_password"] {
            -webkit-text-security: disc;
            text-security: disc;
        }
        
        /* כשהסיסמה מוצגת (type="text") - מבטל את ההסתרה */
        input[name="password"][type="text"], 
        input[name="confirm_password"][type="text"] {
            -webkit-text-security: none !important;
            text-security: none !important;
        }
    </style>
</head>
<body>

<!-- ===== Registration ===== -->
<section class="registration">
  <div class="container">
    <div class="registration-container">
      <div class="registration-title">
        <h1>הרשמה ל-WiseCart</h1>
        <p>הצטרפו אלינו וגלו כיצד לחסוך בקניות</p>
      </div>

      <form id="registration-form" action="register.php" method="post">
        <div class="form-row">
          <div class="form-group">
            <label for="first_name">שם פרטי</label>
            <input id="first_name" class="form-control" name="first_name" type="text" required 
                   title="שדה חובה - אנא הזן שם פרטי" 
                   oninvalid="this.setCustomValidity('אנא הזן שם פרטי')" 
                   oninput="this.setCustomValidity('')" />
          </div>
          <div class="form-group">
            <label for="last_name">שם משפחה</label>
            <input id="last_name" class="form-control" name="last_name" type="text" required 
                   title="שדה חובה - אנא הזן שם משפחה"
                   oninvalid="this.setCustomValidity('אנא הזן שם משפחה')" 
                   oninput="this.setCustomValidity('')" />
          </div>
        </div>

        <div class="form-group">
          <label for="email">כתובת אימייל</label>
          <input id="email" class="form-control" type="email" name="email" placeholder="example@domain.com" required 
                 title="שדה חובה - אנא הזן כתובת אימייל תקינה"
                 oninvalid="this.setCustomValidity('אנא הזן כתובת אימייל תקינה')" 
                 oninput="this.setCustomValidity('')" />
        </div>

        <div class="form-group">
          <label for="phone">מספר טלפון</label>
          <input id="phone" class="form-control" type="tel" name="phone" placeholder="0501234567" required 
                 title="שדה חובה - אנא הזן מספר טלפון"
                 oninvalid="this.setCustomValidity('אנא הזן מספר טלפון')" 
                 oninput="this.setCustomValidity('')" />
        </div>

        <div class="form-group">
          <label for="city">עיר מגורים</label>
          <select id="city" name="city" class="form-control" required
                  title="שדה חובה - אנא בחר עיר מגורים"
                  oninvalid="this.setCustomValidity('אנא בחר עיר מגורים')" 
                  onchange="this.setCustomValidity('')">>
            <option value="">בחר עיר מגורים</option>
            <option value="ירושלים">ירושלים</option>
            <option value="תל אביב-יפו">תל אביב-יפו</option>
            <option value="חיפה">חיפה</option>
            <option value="ראשון לציון">ראשון לציון</option>
            <option value="אשדוד">אשדוד</option>
            <option value="פתח תקווה">פתח תקווה</option>
            <option value="באר שבע">באר שבע</option>
            <option value="נתניה">נתניה</option>
            <option value="בני ברק">בני ברק</option>
            <option value="חולון">חולון</option>
            <option value="רמת גן">רמת גן</option>
            <option value="אשקלון">אשקלון</option>
            <option value="רחובות">רחובות</option>
            <option value="בת ים">בת ים</option>
            <option value="בית שמש">בית שמש</option>
            <option value="כפר סבא">כפר סבא</option>
            <option value="הרצליה">הרצליה</option>
            <option value="חדרה">חדרה</option>
            <option value="מודיעין-מכבים-רעות">מודיעין-מכבים-רעות</option>
            <option value="נצרת">נצרת</option>
            <option value="רמלה">רמלה</option>
            <option value="לוד">לוד</option>
            <option value="קריית גת">קריית גת</option>
            <option value="נהריה">נהריה</option>
            <option value="עכו">עכו</option>
            <option value="אילת">אילת</option>
            <option value="קריית מוצקין">קריית מוצקין</option>
            <option value="קריית ביאליק">קריית ביאליק</option>
            <option value="קריית אתא">קריית אתא</option>
            <option value="קריית ים">קריית ים</option>
            <option value="גבעתיים">גבעתיים</option>
            <option value="רעננה">רעננה</option>
            <option value="דימונה">דימונה</option>
            <option value="אום אל-פחם">אום אל-פחם</option>
            <option value="עפולה">עפולה</option>
            <option value="אריאל">אריאל</option>
            <option value="מעלה אדומים">מעלה אדומים</option>
            <option value="ביתר עילית">ביתר עילית</option>
            <option value="מודיעין עילית">מודיעין עילית</option>
            <option value="טבריה">טבריה</option>
            <option value="קריית שמונה">קריית שמונה</option>
            <option value="שדרות">שדרות</option>
            <option value="אופקים">אופקים</option>
            <option value="נתיבות">נתיבות</option>
            <option value="רהט">רהט</option>
            <option value="ערד">ערד</option>
            <option value="מגדל העמק">מגדל העמק</option>
            <option value="סח'נין">סח'נין</option>
            <option value="אור יהודה">אור יהודה</option>
            <option value="יבנה">יבנה</option>
            <option value="טירת כרמל">טירת כרמל</option>
            <option value="אלעד">אלעד</option>
            <option value="נשר">נשר</option>
            <option value="גני תקווה">גני תקווה</option>
            <option value="אחר">אחר</option>
          </select>
        </div>

        <div class="form-group">
          <label for="password">סיסמה</label>
          <div class="password-container">
            <input id="password" class="form-control has-toggle" type="password" name="password" required 
                   autocomplete="new-password"
                   title="שדה חובה - אנא הזן סיסמה"
                   oninvalid="this.setCustomValidity('אנא הזן סיסמה')" 
                   oninput="this.setCustomValidity('')" />
            <button type="button" id="password-toggle" class="password-toggle" title="הצג סיסמה">הצג</button>
          </div>
          <div class="password-strength-container">
            <div class="password-strength-bar">
              <div id="password-strength-fill" class="password-strength-fill"></div>
            </div>
            <div id="password-strength" class="password-strength"></div>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_password">אימות סיסמה</label>
          <div class="password-container">
            <input id="confirm_password" class="form-control has-toggle" type="password" name="confirm_password" required 
                   autocomplete="new-password"
                   title="שדה חובה - אנא הזן אימות סיסמה"
                   oninvalid="this.setCustomValidity('אנא הזן אימות סיסמה')" 
                   oninput="this.setCustomValidity('')" />
            <button type="button" id="confirm-password-toggle" class="password-toggle" title="הצג סיסמה">הצג</button>
          </div>
          <div id="password-match" class="password-match"></div>
        </div>

        <div class="terms-checkbox" style="align-items: center;">
          <input type="checkbox" id="terms" required 
                 title="שדה חובה - יש לאשר את תנאי השימוש"
                 oninvalid="this.setCustomValidity('יש לאשר את תנאי השימוש ומדיניות הפרטיות')" 
                 onchange="this.setCustomValidity('')"
                 style="margin-top: 4px;" />
          <label for="terms">אני מסכים/ה ל-<a href="terms.php">תנאי השימוש</a> ו-<a href="privacy.php">מדיניות הפרטיות</a></label>
        </div>

        <button type="submit" id="submit-btn" class="submit-btn">הרשמה</button>

        <div id="message" class="message"></div>

        <p class="login-link">
          כבר יש לכם חשבון? <a href="EXloginPage.php">התחברו עכשיו</a>
        </p>
      </form>
    </div>
  </div>
</section>

<!-- ===== Footer ===== -->
<?php include('includes/footer.php'); ?>

<!-- ===== JavaScript ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const passwordToggle = document.getElementById('password-toggle');
    const confirmPasswordToggle = document.getElementById('confirm-password-toggle');
    const passwordMatch = document.getElementById('password-match');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('registration-form');

    // וידוא שסיסמאות מוסתרות בטעינת הדף (כברירת מחדל)
    passwordField.type = 'password';
    confirmPasswordField.type = 'password';
    passwordToggle.textContent = 'הצג';
    confirmPasswordToggle.textContent = 'הצג';
    
    // מניעת auto-complete
    passwordField.setAttribute('autocomplete', 'new-password');
    confirmPasswordField.setAttribute('autocomplete', 'new-password');

    // פונקציה להצגה/הסתרה של סיסמה
    function togglePasswordVisibility(field, toggle) {
        if (field.type === 'password') {
            field.type = 'text';
            toggle.textContent = 'הסתר';
            toggle.title = 'הסתר סיסמה';
            // מבטל הסתרה כשמציגים
            field.style.webkitTextSecurity = 'none';
            field.style.textSecurity = 'none';
        } else {
            field.type = 'password';
            toggle.textContent = 'הצג';
            toggle.title = 'הצג סיסמה';
            // מחזיר הסתרה כשמסתירים
            field.style.webkitTextSecurity = 'disc';
            field.style.textSecurity = 'disc';
        }
    }

    // הוספת כפתורי הצגה/הסתרה
    passwordToggle.addEventListener('click', function() {
        togglePasswordVisibility(passwordField, passwordToggle);
    });

    confirmPasswordToggle.addEventListener('click', function() {
        togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });

    // בדיקת חוזק סיסמה
    function checkPasswordStrength() {
        const password = passwordField.value;
        const strengthIndicator = document.getElementById('password-strength');
        const strengthFill = document.getElementById('password-strength-fill');
        
        if (password.length === 0) {
            strengthIndicator.textContent = '';
            strengthIndicator.className = 'password-strength';
            strengthFill.style.width = '0%';
            return;
        }

        let strength = 0;
        let messages = [];

        // בדיקות חוזק
        if (password.length >= 8) {
            strength++;
        } else {
            messages.push('לפחות 8 תווים');
        }

        if (/[a-z]/.test(password)) {
            strength++;
        } else {
            messages.push('אות קטנה באנגלית');
        }

        if (/[A-Z]/.test(password)) {
            strength++;
        } else {
            messages.push('אות גדולה באנגלית');
        }

        if (/[0-9]/.test(password)) {
            strength++;
        } else {
            messages.push('ספרה');
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            strength++;
        } else {
            messages.push('תו מיוחד');
        }

        // עדכון הסרגל
        const percentage = (strength / 5) * 100;
        strengthFill.style.width = percentage + '%';

        // הצגת תוצאה
        if (strength < 3) {
            strengthIndicator.textContent = '🔴 סיסמה חלשה - נדרש: ' + messages.join(', ');
            strengthIndicator.className = 'password-strength weak';
            strengthFill.style.background = '#e74c3c';
        } else if (strength < 4) {
            strengthIndicator.textContent = '🟡 סיסמה בינונית - מומלץ להוסיף: ' + messages.join(', ');
            strengthIndicator.className = 'password-strength medium';
            strengthFill.style.background = '#f39c12';
        } else {
            strengthIndicator.textContent = '🟢 סיסמה חזקה - מצוינת!';
            strengthIndicator.className = 'password-strength strong';
            strengthFill.style.background = '#2ecc71';
        }
    }

    // בדיקת התאמת סיסמאות
    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        if (confirmPassword === '') {
            passwordMatch.textContent = '';
            passwordMatch.className = 'password-match';
            confirmPasswordField.className = 'form-control has-toggle';
            return;
        }

        if (password === confirmPassword) {
            passwordMatch.textContent = '✅ הסיסמאות זהות';
            passwordMatch.className = 'password-match success';
            confirmPasswordField.className = 'form-control has-toggle success';
        } else {
            passwordMatch.textContent = '❌ הסיסמאות אינן זהות';
            passwordMatch.className = 'password-match error';
            confirmPasswordField.className = 'form-control has-toggle error';
        }
    }

    // הוספת מאזיני אירועים
    passwordField.addEventListener('input', function() {
        checkPasswordStrength();
        checkPasswordMatch();
    });

    confirmPasswordField.addEventListener('input', checkPasswordMatch);

    // וידוא הטופס לפני שליחה
    form.addEventListener('submit', function(e) {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        const email = document.getElementById('email').value;
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const phone = document.getElementById('phone').value;
        const city = document.getElementById('city').value;
        const terms = document.getElementById('terms').checked;

        // בדיקות custom בעברית
        if (!firstName.trim()) {
            e.preventDefault();
            showMessage('אנא הזן שם פרטי', 'error');
            document.getElementById('first_name').focus();
            return false;
        }

        if (!lastName.trim()) {
            e.preventDefault();
            showMessage('אנא הזן שם משפחה', 'error');
            document.getElementById('last_name').focus();
            return false;
        }

        if (!email.trim()) {
            e.preventDefault();
            showMessage('אנא הזן כתובת אימייל', 'error');
            document.getElementById('email').focus();
            return false;
        }

        if (!phone.trim()) {
            e.preventDefault();
            showMessage('אנא הזן מספר טלפון', 'error');
            document.getElementById('phone').focus();
            return false;
        }

        if (!city) {
            e.preventDefault();
            showMessage('אנא בחר עיר מגורים', 'error');
            document.getElementById('city').focus();
            return false;
        }

        if (!terms) {
            e.preventDefault();
            showMessage('יש לאשר את תנאי השימוש ומדיניות הפרטיות', 'error');
            document.getElementById('terms').focus();
            return false;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            showMessage('הסיסמאות אינן זהות. אנא תקן זאת לפני המשך.', 'error');
            confirmPasswordField.focus();
            return false;
        }

        if (password.length < 6) {
            e.preventDefault();
            showMessage('הסיסמה חייבת להכיל לפחות 6 תווים.', 'error');
            passwordField.focus();
            return false;
        }

        // הצגת אנימציית טעינה
        submitBtn.disabled = true;
        submitBtn.textContent = 'רושם...';
    });

    // פונקציה להצגת הודעות
    function showMessage(text, type) {
        const messageBox = document.getElementById('message');
        messageBox.textContent = text;
        messageBox.className = 'message ' + type;
        messageBox.style.display = 'block';
    }

    // מילוי שדות מה-URL (במקרה של שגיאה)
    const params = new URLSearchParams(window.location.search);
    
    if (params.get('first_name')) document.getElementById('first_name').value = params.get('first_name');
    if (params.get('last_name')) document.getElementById('last_name').value = params.get('last_name');
    if (params.get('email')) document.getElementById('email').value = params.get('email');
    if (params.get('phone')) document.getElementById('phone').value = params.get('phone');
    if (params.get('city')) document.getElementById('city').value = params.get('city');

    // הצגת הודעות שגיאה
    if (params.has('error')) {
        const errorType = params.get('error');
        let errorMessage = "";

        switch (errorType) {
            case 'validation':
                const messages = params.get('messages');
                if (messages) {
                    errorMessage = decodeURIComponent(messages).split('|').join('<br>');
                } else {
                    errorMessage = "שגיאה בנתונים שהוזנו.";
                }
                break;
            case 'missing_fields':
                errorMessage = "אנא מלאו את כל השדות הנדרשים.";
                break;
            case 'pass_mismatch':
                errorMessage = "הסיסמאות אינן תואמות.";
                break;
            case 'email_exists':
                errorMessage = "האימייל כבר רשום במערכת.";
                break;
            case 'weak_password':
                errorMessage = "הסיסמה חייבת להכיל לפחות 8 תווים.";
                break;
            case 'invalid_email':
                errorMessage = "כתובת האימייל אינה תקינה.";
                break;
            case 'database':
                errorMessage = params.get('message') || "שגיאה בשמירת הנתונים. נסו שוב.";
                break;
            default:
                errorMessage = "אירעה שגיאה. אנא נסו שוב.";
        }

        showMessage(errorMessage, 'error');
        
        // ניקוי ה-URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // פונקציה נוספת לוידוא הסתרת סיסמאות
    function ensurePasswordsHidden() {
        const allPasswordFields = document.querySelectorAll('input[type="password"]');
        allPasswordFields.forEach(field => {
            field.style.webkitTextSecurity = 'disc';
            field.style.textSecurity = 'disc';
        });
    }

    // הפעלה מיידית
    ensurePasswordsHidden();

    // וידוא שהסיסמאות מוסתרות גם אחרי focus/blur
    passwordField.addEventListener('focus', ensurePasswordsHidden);
    confirmPasswordField.addEventListener('focus', ensurePasswordsHidden);
    passwordField.addEventListener('blur', ensurePasswordsHidden);
    confirmPasswordField.addEventListener('blur', ensurePasswordsHidden);
});
</script>

</body>
</html>