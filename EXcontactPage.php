<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>צור קשר - WiseCart</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
     /* Main Content */
        .main-content {
            padding-top: 100px;
            padding-bottom: 60px;
        }
        
        .page-title {
            margin-bottom: 30px;
        }
        
        .page-title h1 {
            font-size: 28px;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .page-title p {
            color: #666;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 140px 0 70px;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 36px;
            margin-bottom: 15px;
        }
        
        .page-header p {
            font-size: 18px;
            max-width: 700px;
            margin: 0 auto;
        }
         /* Header Section */
        .contact-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }
        
        .contact-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .contact-header p {
            text-align: center;
            opacity: 0.9;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
         /* Card Layout */
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            color: var(--dark-color);
            font-size: 20px;
        }
        
        .card-header .view-all {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        
        /* Contact Info */
        .contact-info h2 {
            font-size: 28px;
            color: var(--dark-color);
            margin-bottom: 25px;
        }
        
        .contact-methods {
            margin-bottom: 40px;
        }
        
        .contact-method {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .contact-icon {
            background-color: var(--primary-color);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 15px;
            color: white;
            flex-shrink: 0;
        }
        
        .contact-details h3 {
            margin-bottom: 8px;
            color: var(--dark-color);
            font-size: 18px;
        }
        
        .contact-details p {
            color: #666;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .social-icon {
            background-color: #456789;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s;
            color: white;
        }
        
        .social-icon:hover {
            background-color: var(--primary-color);
        }
        
        /* Contact Form */
        .contact-form h2 {
            font-size: 28px;
            color: var(--dark-color);
            margin-bottom: 25px;
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
            outline: none;
            border-color: var(--primary-color);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .form-submit:hover {
            background-color: #2980b9;
        }
        
       .error-message {
        color: #e74c3c;
        font-size: 14px;
        margin-top: 5px;
        display: block;
        }
        
        .form-control.error {
            border-color: #e74c3c;
        }
        
        .form-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
         /* FAQ Section */
        .faq-item {
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .faq-question {
            background: var(--gray-light);
            padding: 15px 20px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question:hover {
            background: #e9ecef;
        }

        .faq-answer {
            padding: 20px;
            display: none;
            border-top: 1px solid var(--gray-medium);
        }

        .faq-answer.active {
            display: block;
        }
        
        @media (max-width: 992px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .faq-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            nav ul {
                gap: 15px;
            }
            
            .page-header {
                padding: 130px 0 60px;
            }
            
            .page-header h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
     <?php include('includes/header.php'); ?>
<main>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>צור קשר</h1>
            <p>יש לכם שאלות או הצעות? אנחנו כאן בשבילכם. צרו איתנו קשר ונחזור אליכם בהקדם.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>פרטי התקשרות</h2>
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h3>טלפון</h3>
                                <p>072-123-4567</p>
                                <p>שעות פעילות: א'-ה' 09:00-18:00</p>
                            </div>
                        </div>
                        <div class="contact-method">
                            <div class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h3>דוא"ל</h3>
                                <p>info@wisecart.co.il</p>
                            </div>
                        </div>
                        <div class="contact-method">
                            <div class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h3>כתובת</h3>
                                <p>רח' הרצל 123, תל אביב</p>
                                <p>מיקוד: 6701234</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                <h2>שלחו לנו הודעה</h2>
                <form id="contact-form" novalidate>
                        <div class="form-group">
                            <label for="name">שם מלא</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">דוא"ל</label>
                            <input type="email" id="email" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">טלפון</label>
                            <input type="tel" id="phone" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="subject">נושא</label>
                            <select id="subject" name="name" class="form-control" required>
                                <option value="">בחרו נושא</option>
                                <option value="support">תמיכה טכנית</option>
                                <option value="feedback">משוב ותגובות</option>
                                <option value="partnership">הצעות שיתוף פעולה</option>
                                <option value="other">אחר</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">הודעה</label>
                            <textarea id="message" name="name" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="form-submit">שליחה</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
    <script src="js/script.js"></script>
    <script>
        // FAQ Toggle functionality
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('span:last-child');
            
            if (answer.classList.contains('active')) {
                answer.classList.remove('active');
                icon.textContent = '+';
            } else {
                // Close all other FAQ items
                document.querySelectorAll('.faq-answer.active').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelectorAll('.faq-question span:last-child').forEach(icon => {
                    icon.textContent = '+';
                });
                
                // Open clicked item
                answer.classList.add('active');
                icon.textContent = '-';
            }
        }
    </script>
    <script src="JScontactPage.js"></script>
    <?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>