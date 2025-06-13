<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>מדיניות פרטיות - WiseCart</title>
  <link rel="stylesheet" href="CSS/styles.css" />
  <style>
    .info-section {
      padding: 80px 0;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .info-container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      border-radius: 10px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      line-height: 1.8;
      color: #333;
      font-size: 16px;
    }

    .info-container h1,
    .info-container h2 {
      color: var(--dark-color);
      margin-top: 0;
    }

    .info-container h2 {
      font-size: 20px;
      margin-top: 30px;
    }

    .info-container a {
      color: var(--primary-color);
      text-decoration: underline;
    }
    
     .back-button {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background-color: var(--primary-color);
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
    }

    .back-button:hover {
      background-color: var(--secondary-color);
    }

    @media (max-width: 768px) {
      .info-container {
        padding: 25px;
      }
    }
  </style>
</head>
<body>

<section class="info-section">
  <div class="container">
    <div class="info-container">
      <h1>מדיניות פרטיות</h1>
      <h2>1. איסוף מידע</h2>
       <p>אנו אוספים פרטים מזהים בעת ההרשמה והשימוש באתר, כגון שם, אימייל ומספר טלפון.</p>
      <h2>2. שימוש במידע</h2>
      <p>המידע משמש לצורך הפעלת השירות, שיפור חוויית המשתמש ותקשור עם המשתמש.</p>
      <h2>3. אבטחת מידע</h2>
        <p>האתר מיישם אמצעי אבטחה אבל איננו אחראים לפריצות בלתי צפויות.</p>
        <h2>4. מסירת מידע לצד שלישי</h2>
      <p>לא נמסור את פרטיך לצדדים שלישיים אלא באישורך או עפ"י חוק.</p>
      <h2>5. עוגיות (Cookies)</h2>
      <p>האתר עושה שימוש בעוגיות לזיהוי משתמשים ולשיפור השירות.</p>
    </div>
  </div>
</section>

<?php include('includes/footer.php'); ?>
</body>
</html>
