<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WiseCart – שכחת סיסמה</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <!-- ====== CSS ====== -->
    <style>
        /* ===== Login Section ===== */
        .login-section {
            padding: 60px 0 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            display: flex;
        }

        .login-image {
            flex: 1;
            background: linear-gradient(
                135deg,
                var(--primary-color),
                var(--secondary-color)
            );
            color: #fff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-image h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .login-benefits {
            margin-top: 40px;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .benefit-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 15px;
        }

        .login-form-container {
            flex: 1;
            padding: 40px;
        }
        .login-form-container h2 {
            text-align: center;
            color: var(--dark-color);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .form-description {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #2980b9;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-login a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .back-to-login a:hover {
            color: #2980b9;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: 600;
        }
        .message.error {
            color: var(--accent-color);
        }
        .message.success {
            color: var(--secondary-color);
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            nav ul {
                gap: 15px;
            }
            .login-container {
                flex-direction: column;
            }
            .login-image,
            .login-form-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- ===== Forgot Password Section ===== -->
    <section class="login-section">
        <div class="container">
            <div class="login-container">
                <!-- ----- Left side (marketing) ----- -->
                <div class="login-image">
                    <h2>אל תדאגו, זה קורה לכולם!</h2>
                    <p>נשלח לכם קישור לאיפוס הסיסמה במייל, ותוכלו לחזור לחסוך בקניות.</p>

                    <div class="login-benefits">
                        <div class="benefit-item">
                            <div class="benefit-icon">🔒</div>
                            <div>
                                <h4>איפוס בטוח</h4>
                                <p>איפוס הסיסמה נעשה באמצעות קישור מאובטח שנשלח למייל</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon">⚡</div>
                            <div>
                                <h4>תהליך מהיר</h4>
                                <p>תוך דקות ספורות תוכלו לחזור להשתמש בחשבון שלכם</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon">🛡️</div>
                            <div>
                                <h4>אבטחה מקסימלית</h4>
                                <p>המידע שלכם מוגן ברמת האבטחה הגבוהה ביותר</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ----- Right side (form) ----- -->
                <div class="login-form-container">
                    <h2>איפוס סיסמה</h2>
                    
                    <div class="form-description">
                        הזינו את כתובת האימייל שלכם ונשלח לכם קישור לאיפוס הסיסמה
                    </div>

                    <form action="forgot-password-process.php" method="post">
                        <div class="form-group">
                            <label for="email">כתובת אימייל</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="form-control"
                                placeholder="הזינו את כתובת האימייל שלכם"
                                required
                            />
                        </div>

                        <button type="submit" class="login-btn">שלח קישור לאיפוס</button>
                    </form>

                    <!-- הודעות הצלחה / שגיאה -->
                    <div id="message" class="message"></div>

                    <div class="back-to-login">
                        <a href="EXloginPage.php">← חזרה לדף ההתחברות</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Footer ===== -->
   <?php include('includes/footer.php'); ?>

    <!-- ===== JS: הודעות בהתאם ל-URL ===== -->
    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            const box = document.getElementById("message");

            if (params.has("sent")) {
                box.textContent = "קישור לאיפוס הסיסמה נשלח למייל שלכם!";
                box.classList.add("success");
            } else if (params.get("error") === "email_not_found") {
                box.textContent = "כתובת האימייל לא נמצאה במערכת.";
                box.classList.add("error");
            } else if (params.get("error") === "send_failed") {
                box.textContent = "שגיאה בשליחת המייל. אנא נסו שוב.";
                box.classList.add("error");
            }
        })();
    </script>
</body>
</html>