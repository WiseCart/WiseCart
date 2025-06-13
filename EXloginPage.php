<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>התחברות - WiseCart</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        /* LOGIN SECTION */
        .login{padding:60px 0 80px;background:linear-gradient(135deg,var(--primary-color),var(--secondary-color))}
        .login-container{max-width:500px;margin:0 auto;background:#fff;border-radius:10px;padding:40px;box-shadow:0 10px 30px rgba(0,0,0,.1)}
        .login-title{text-align:center;margin-bottom:30px}
        .login-title h1{font-size:32px;color:var(--dark-color);margin-bottom:10px}
        .login-title p{color:#666;font-size:16px}
        
        .form-group{margin-bottom:20px}
        .form-group label{display:block;margin-bottom:8px;font-weight:500;color:var(--dark-color)}
        .form-control{width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:5px;font-size:16px;transition:border-color .3s, transform 0.2s, box-shadow 0.2s}
        .form-control:focus{border-color:var(--primary-color);outline:none;transform:translateY(-1px);box-shadow:0 4px 8px rgba(0,0,0,.1)}

        /* סגנונות לשדה סיסמה עם כפתור הצגה/הסתרה */
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

        .form-options{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px}
        .remember-me{display:flex;align-items:center;gap:8px}
        .remember-me input{margin:0}
        .forgot-password{color:var(--primary-color);text-decoration:none;font-weight:500}
        .forgot-password:hover{text-decoration:underline}

        .login-btn{width:100%;padding:14px;background:var(--primary-color);color:#fff;border:none;border-radius:5px;font-size:16px;font-weight:500;cursor:pointer;transition:.3s}
        .login-btn:hover{background:#2980b9}
        .login-btn:disabled{background:#ccc;cursor:not-allowed}

        .divider{display:flex;align-items:center;margin:25px 0;color:#999}
        .divider::before,.divider::after{content:"";flex:1;height:1px;background:#ddd}
        .divider span{padding:0 15px}

        .social-login{display:flex;gap:15px;margin-bottom:20px}
        .social-btn{flex:1;padding:10px;border:1px solid #ddd;border-radius:5px;background:#fff;display:flex;justify-content:center;align-items:center;gap:10px;cursor:pointer;transition:.3s;text-decoration:none;color:#666}
        .social-btn:hover{background:#f5f5f5}

        .register-link{text-align:center;margin-top:20px;color:#666}
        .register-link a{color:var(--primary-color);text-decoration:none;font-weight:500}
        .register-link a:hover{text-decoration:underline}

        /* הודעת שגיאה/הצלחה */
        .message{text-align:center;margin-top:15px;padding:12px;border-radius:5px;font-weight:600;display:none}
        .message.error{color:#721c24;background-color:#f8d7da;border:1px solid #f5c6cb}
        .message.success{color:#155724;background-color:#d4edda;border:1px solid #c3e6cb}

        /* סגנונות לשדות עם הצלחה או שגיאה */
        .form-control.success {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .form-control.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* RESPONSIVE */
        @media(max-width:768px){
          .header-content{flex-direction:column;gap:15px}
          nav ul{gap:15px}
          .login-container{padding:25px}
          .social-login{flex-direction:column}
          .form-options{flex-direction:column;gap:15px;align-items:stretch}
          .password-toggle{left:10px}
          .form-control.has-toggle{padding-left:70px}
        }

        /* וידוא שסיסמה מוסתרת כברירת מחדל */
        input[name="password"] {
            -webkit-text-security: disc;
            text-security: disc;
        }
        
        /* כשהסיסמה מוצגת (type="text") - מבטל את ההסתרה */
        input[name="password"][type="text"] {
            -webkit-text-security: none !important;
            text-security: none !important;
        }
    </style>
</head>
<body>

<!-- ===== Login ===== -->
<section class="login">
  <div class="container">
    <div class="login-container">
      <div class="login-title">
        <h1>התחברות ל-WiseCart</h1>
        <p>ברוכים הבאים חזרה! התחברו כדי להמשיך לחסוך</p>
      </div>

      <form id="login-form" action="login.php" method="post">
        <div class="form-group">
          <label for="email">כתובת אימייל</label>
          <input id="email" class="form-control" type="email" name="email" placeholder="example@domain.com" required 
                 title="שדה חובה - אנא הזן כתובת אימייל תקינה"
                 oninvalid="this.setCustomValidity('אנא הזן כתובת אימייל תקינה')" 
                 oninput="this.setCustomValidity('')" />
        </div>

        <div class="form-group">
          <label for="password">סיסמה</label>
          <div class="password-container">
            <input id="password" class="form-control has-toggle" type="password" name="password" required 
                   autocomplete="current-password"
                   title="שדה חובה - אנא הזן סיסמה"
                   oninvalid="this.setCustomValidity('אנא הזן סיסמה')" 
                   oninput="this.setCustomValidity('')" />
            <button type="button" id="password-toggle" class="password-toggle" title="הצג סיסמה">הצג</button>
          </div>
        </div>

        <div class="form-options">
          <div class="remember-me">
            <input type="checkbox" id="remember" name="remember" />
            <label for="remember">זכור אותי</label>
          </div>
          <a href="forgot-password-process.php" class="forgot-password">שכחת סיסמה?</a>
        </div>

        <button type="submit" id="login-btn" class="login-btn">התחברות</button>

        <div id="message" class="message"></div>

        <p class="register-link">
          עדיין אין לכם חשבון? <a href="EXsigninPage.php">הירשמו עכשיו</a>
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
    const passwordToggle = document.getElementById('password-toggle');
    const loginBtn = document.getElementById('login-btn');
    const form = document.getElementById('login-form');

    // וידוא שסיסמה מוסתרת בטעינת הדף
    passwordField.type = 'password';
    passwordToggle.textContent = 'הצג';
    
    // מניעת auto-complete
    passwordField.setAttribute('autocomplete', 'current-password');

    // פונקציה להצגה/הסתרה של סיסמה
    function togglePasswordVisibility() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordToggle.textContent = 'הסתר';
            passwordToggle.title = 'הסתר סיסמה';
            // מבטל הסתרה כשמציגים
            passwordField.style.webkitTextSecurity = 'none';
            passwordField.style.textSecurity = 'none';
        } else {
            passwordField.type = 'password';
            passwordToggle.textContent = 'הצג';
            passwordToggle.title = 'הצג סיסמה';
            // מחזיר הסתרה כשמסתירים
            passwordField.style.webkitTextSecurity = 'disc';
            passwordField.style.textSecurity = 'disc';
        }
    }

    // הוספת כפתור הצגה/הסתרה
    passwordToggle.addEventListener('click', togglePasswordVisibility);

    // וידוא הטופס לפני שליחה
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = passwordField.value;

        // בדיקות custom בעברית
        if (!email.trim()) {
            e.preventDefault();
            showMessage('אנא הזן כתובת אימייל', 'error');
            document.getElementById('email').focus();
            return false;
        }

        if (!password.trim()) {
            e.preventDefault();
            showMessage('אנא הזן סיסמה', 'error');
            passwordField.focus();
            return false;
        }

        // בדיקת תקינות אימייל
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showMessage('כתובת האימייל אינה תקינה', 'error');
            document.getElementById('email').focus();
            return false;
        }

        // הצגת אנימציית טעינה
        loginBtn.disabled = true;
        loginBtn.textContent = 'מתחבר...';
    });

    // פונקציה להצגת הודעות
    function showMessage(text, type) {
        const messageBox = document.getElementById('message');
        messageBox.textContent = text;
        messageBox.className = 'message ' + type;
        messageBox.style.display = 'block';
    }

    // טיפול בפרמטרים מה-URL
    const params = new URLSearchParams(window.location.search);
    
    // מילוי שדה האימייל אם הועבר
    if (params.get('email')) {
        document.getElementById('email').value = params.get('email');
    }

    // הצגת הודעות
    if (params.has('registered')) {
        showMessage('נרשמת בהצלחה! עכשיו אפשר להתחבר', 'success');
    } else if (params.has('error')) {
        const errorType = params.get('error');
        let errorMessage = "";

        switch (errorType) {
            case 'empty_fields':
                errorMessage = "אנא מלאו את כל השדות הנדרשים";
                break;
            case 'invalid_email':
                errorMessage = "כתובת האימייל שהוזנה אינה תקינה";
                break;
            case 'user_not_found':
                errorMessage = "משתמש עם כתובת אימייל זו לא נמצא במערכת";
                break;
            case 'wrong_password':
                errorMessage = "הסיסמה שהוזנה שגויה";
                break;
            case 'login_failed':
            case 'invalid_credentials':
                errorMessage = "פרטי ההתחברות שגויים. אנא בדקו את האימייל והסיסמה";
                break;
            default:
                errorMessage = "אירעה שגיאה בעת ההתחברות. אנא נסו שוב";
        }

        showMessage(errorMessage, 'error');
        
        // ניקוי ה-URL
        const newUrl = window.location.pathname;
        const emailParam = params.get('email');
        if (emailParam) {
            window.history.replaceState({}, document.title, newUrl + '?email=' + encodeURIComponent(emailParam));
        } else {
            window.history.replaceState({}, document.title, newUrl);
        }
    }

    // פונקציה לוידוא הסתרת סיסמה
    function ensurePasswordHidden() {
        if (passwordField.type === 'password') {
            passwordField.style.webkitTextSecurity = 'disc';
            passwordField.style.textSecurity = 'disc';
        }
    }

    // הפעלה מיידית
    ensurePasswordHidden();

    // וידוא שהסיסמה מוסתרת גם אחרי focus/blur
    passwordField.addEventListener('focus', ensurePasswordHidden);
    passwordField.addEventListener('blur', ensurePasswordHidden);
});
</script>

</body>
</html>