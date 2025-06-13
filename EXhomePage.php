<?php
session_start();
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiseCart - השוואת מחירי קניות בסופרים</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        /* CSS Variables - מותאם לפלטת הצבעים של האזור האישי */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --gray-light: #f8f9fa;
            --gray-medium: #dee2e6;
            --text-color: #2c3e50;
            --border-radius: 15px;
            --box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gray-light);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 40px;
        }
        
        /* Hero Section - מעוצב בסגנון הכרטיסים */
        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 40px 50px;
            text-align: center;
            border-radius: var(--border-radius);
            margin: 80px auto 30px;
            max-width: 800px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
        }
        
        .hero h1 {
            font-size: 2.4rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 25px;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        .search-box {
            background-color: white;
            border-radius: 12px;
            padding: 8px;
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .search-box input {
            flex: 1;
            padding: 18px 20px;
            border: none;
            outline: none;
            font-size: 16px;
            color: var(--dark-color);
            border-radius: 8px 0 0 8px;
        }
        
        .search-box button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .search-box button:hover {
            background-color: #27ae60;
            transform: translateY(-1px);
        }
        
        /* Sections - מעוצבים כמו הכרטיסים באזור האישי */
        .section {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px 35px;
            margin: 0 auto 30px;
            max-width: 800px;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark-color);
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }
        
        .section-title p {
            color: #666;
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .feature-card {
            background-color: var(--gray-light);
            border-radius: 12px;
            padding: 20px 10px;
            text-align: center;
            transition: var(--transition);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: var(--transition);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(52, 152, 219, 0.2);
            border-color: var(--primary-color);
            background: white;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .feature-card h3 {
            margin-bottom: 10px;
            color: var(--dark-color);
            font-size: 1rem;
            font-weight: 600;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.4;
            font-size: 0.85rem;
        }
        
        /* How It Works Steps */
        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 50px;
        }
        
        .step {
            text-align: center;
            padding: 25px 15px;
            background: var(--gray-light);
            border-radius: 12px;
            transition: var(--transition);
            position: relative;
            border: 2px solid transparent;
        }
        
        .step:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: white;
            border-color: var(--primary-color);
        }
        
        .step-number {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 25px;
            font-weight: bold;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .step h3 {
            margin-bottom: 12px;
            color: var(--dark-color);
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .step p {
            color: #666;
            line-height: 1.5;
            font-size: 0.9rem;
        }
        
        /* Stores Grid */
        .stores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .store-card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: var(--transition);
            border: 2px solid transparent;
        }
        
        .store-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-color: var(--primary-color);
        }
        
        .store-logo {
            max-width: 85%;
            max-height: 70px;
            filter: grayscale(0.3);
            transition: var(--transition);
        }
        
        .store-card:hover .store-logo {
            filter: grayscale(0);
        }
        
        /* Testimonials */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 12px;
            padding: 25px 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: var(--transition);
            border: 2px solid transparent;
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -10px;
            right: 20px;
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.3;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
            border-color: var(--primary-color);
        }
        
        .testimonial-text {
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .author-image {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            margin-left: 15px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .author-info h4 {
            margin-bottom: 5px;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .author-info p {
            color: #777;
            font-size: 14px;
        }
        
        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, var(--dark-color), #34495e);
            color: white;
            text-align: center;
            border-radius: var(--border-radius);
            margin: 0 auto 30px;
            max-width: 800px;
            position: relative;
            overflow: hidden;
            padding: 50px 40px;
        }
        
        .cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .cta h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 35px;
            font-size: 1.15rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        .app-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .app-button {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: rgba(0,0,0,0.8);
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            text-decoration: none;
            transition: var(--transition);
            border: 2px solid transparent;
        }
        
        .app-button:hover {
            background-color: rgba(0,0,0,0.9);
            transform: translateY(-3px);
            border-color: var(--primary-color);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        
        .app-button .app-icon {
            font-size: 28px;
        }
        
        .app-text small {
            display: block;
            font-size: 12px;
            opacity: 0.8;
        }
        
        .app-text strong {
            font-size: 16px;
            font-weight: 600;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .hero {
                padding: 70px 0 50px;
                margin-top: 60px;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .section {
                padding: 30px 20px;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .steps,
            .testimonials-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .search-box {
                flex-direction: column;
                gap: 8px;
            }
            
            .search-box input,
            .search-box button {
                border-radius: 8px;
            }
            
            .app-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
        
        /* Animation for scroll effects */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .section,
        .feature-card,
        .step,
        .testimonial-card {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
</head>
<body>
    <!--Header-->    
    <?php include('includes/header.php'); ?>

    <!-- Hero Section -->
            <section class="hero">
        <div class="container">
            <h1>השוו מחירים וחסכו בקניות שלכם</h1>
            <p>WiseCart מאפשר לכם להשוות מחירי מוצרים בין סופרים שונים, לחסוך זמן וכסף, ולעשות את הקניות החכמות ביותר</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section features">
        <div class="container">
            <div class="section-title">
                <h2>למה לבחור ב-WiseCart?</h2>
                <p>אנו מציעים לכם את הדרך הפשוטה והיעילה ביותר להשוות מחירים ולחסוך בקניות</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">₪</div>
                    <h3>חיסכון של מאות שקלים</h3>
                    <p>השוואת מחירים חכמה יכולה לחסוך לכם מאות שקלים בחודש על קניות שלכם</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3>בוט השוואה חכם</h3>
                  <p>שלחו רשימת קניות ותקבלו תוצאות מיידיות עם המלצה על הסופר הזול ביותר</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>השווה עגלות מלאות</h3>
                    <p>הכניסו את כל המוצרים שאתם רוצים לקנות ומצאו את הסופר הזול ביותר</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>זמין מכל מכשיר</h3>
                    <p>גישה קלה מהמחשב או מהטלפון הנייד בכל מקום ובכל זמן</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>איך זה עובד?</h2>
                <p>מצאו את הסופר הזול ביותר בשלושה צעדים פשוטים</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>הוסיפו מוצרים לרשימה</h3>
                    <p>חפשו והוסיפו את המוצרים שאתם רוצים לקנות לרשימת הקניות שלכם</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>השוו מחירים</h3>
                    <p>המערכת תציג לכם השוואת מחירים בין כל הסופרים הזמינים באזורכם</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>חסכו בקניות</h3>
                    <p>בחרו את האופציה המשתלמת ביותר וחסכו בקניות השבועיות שלכם</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section testimonials">
        <div class="container">
            <div class="section-title">
                <h2>מה הלקוחות שלנו אומרים</h2>
                <p>אלפי ישראלים כבר חוסכים בקניות עם WiseCart</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">"חוסכת בין 200-300 שקלים בחודש על קניות בסופר מאז שהתחלתי להשתמש ב-WiseCart. אפליקציה מעולה!"</p>
                    <div class="testimonial-author">
                        <div class="author-image">מכ</div>
                        <div class="author-info">
                            <h4>מיכל כהן</h4>
                            <p>תל אביב</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"ממליץ בחום! הפלטפורמה פשוטה לשימוש ועוזרת לי לחסוך זמן וכסף. אני יכול לראות בקלות איפה משתלם לקנות."</p>
                    <div class="testimonial-author">
                        <div class="author-image">דל</div>
                        <div class="author-info">
                            <h4>דני לוי</h4>
                            <p>חיפה</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"כמשפחה גדולה, החיסכון משתלם במיוחד. מצאנו שאנחנו חוסכים מעל 500 ש״ח בחודש על קניות מתוכננות בחוכמה."</p>
                    <div class="testimonial-author">
                        <div class="author-image">רא</div>
                        <div class="author-info">
                            <h4>רונית אברהם</h4>
                            <p>באר שבע</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
    
    <script src="js/script.js"></script>
    <?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>