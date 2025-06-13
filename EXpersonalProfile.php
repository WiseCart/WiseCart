<?php
session_start();

// חיבור למסד הנתונים
$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db = "isorye_product_prices";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("שגיאת חיבור למסד הנתונים: " . $e->getMessage());
}

// בדיקה אם המשתמש מחובר
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: EXloginPage.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_data = null;
$message = '';
$error = '';

// שליפת נתוני המשתמש
try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, city, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        throw new Exception("משתמש לא נמצא");
    }
} catch (Exception $e) {
    $error = "שגיאה בטעינת נתוני המשתמש: " . $e->getMessage();
}

// טיפול בעדכון נתונים
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    try {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $city = trim($_POST['city'] ?? '');
        
        // ולידציה בסיסית
        if (empty($first_name) || empty($last_name) || empty($email)) {
            throw new Exception("שם פרטי, שם משפחה ואימייל הם שדות חובה");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("כתובת האימייל לא תקינה");
        }
        
        // בדיקה אם האימייל כבר קיים אצל משתמש אחר
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            throw new Exception("כתובת האימייל כבר רשומה אצל משתמש אחר");
        }
        
        // עדכון הנתונים
        $stmt = $pdo->prepare("
            UPDATE users SET 
                first_name = ?, 
                last_name = ?, 
                email = ?, 
                phone = ?, 
                city = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $first_name, $last_name, $email, $phone, $city, $user_id
        ]);
        
        // עדכון נתוני הסשן
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        
        // רענון נתוני המשתמש
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, city, created_at FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $message = "הנתונים עודכנו בהצלחה!";
        
    } catch (Exception $e) {
        $error = "שגיאה בעדכון הנתונים: " . $e->getMessage();
    }
}

// פונקציה לקבלת ראשי תיבות עבור האווטר
function getInitials($firstName, $lastName) {
    $first = mb_substr($firstName, 0, 1, 'UTF-8');
    $last = mb_substr($lastName, 0, 1, 'UTF-8');
    return $first . $last;
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiseCart - אזור אישי</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --gray-light: #f8f9fa;
            --gray-medium: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gray-light);
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .main-content {
            padding-top: 120px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            margin-top: 80px;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .page-header p {
            text-align: center;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* User Profile Card */
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .profile-info h2 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .profile-info .contact {
            color: #666;
            font-size: 0.95rem;
        }
        
        .member-since {
            background: var(--gray-light);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #666;
            margin-right: auto;
        }
        
        /* Form Sections */
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .section-title {
            font-size: 1.4rem;
            margin-bottom: 20px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-group input,
        .form-group select {
            padding: 12px 15px;
            border: 2px solid var(--gray-medium);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        /* City Selector */
        .city-selector {
            position: relative;
        }
        
        .city-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid var(--gray-medium);
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            display: none;
        }
        
        .city-suggestion {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .city-suggestion:hover {
            background-color: var(--gray-light);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: var(--gray-medium);
            color: var(--dark-color);
        }
        
        .btn-secondary:hover {
            background: #c5c9d0;
        }
        
        /* Required field indicator */
        .required {
            color: var(--accent-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!--Header-->
    <?php include('includes/header.php'); ?>
    
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>האזור האישי שלי</h1>
            <p>נהל את הפרטים האישיים שלך</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($user_data): ?>
        <!-- User Profile -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= htmlspecialchars(getInitials($user_data['first_name'] ?? '', $user_data['last_name'] ?? '')) ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? '')) ?></h2>
                    <div class="contact">
                        <?= htmlspecialchars($user_data['email'] ?? '') ?>
                        <?php if ($user_data['phone']): ?>
                            • <?= htmlspecialchars($user_data['phone']) ?>
                        <?php endif; ?>
                        <?php if ($user_data['city']): ?>
                            • <?= htmlspecialchars($user_data['city']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="member-since">
                    חבר מאז: <?= $user_data['created_at'] ? date('d/m/Y', strtotime($user_data['created_at'])) : 'לא זמין' ?>
                </div>
            </div>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="action" value="update_profile">
            
            <!-- Personal Information -->
            <div class="form-section">
                <h3 class="section-title">פרטים אישיים</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">שם פרטי <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user_data['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">שם משפחה <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user_data['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">דוא"ל <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">טלפון</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" placeholder="054-123-4567">
                    </div>
                    <div class="form-group">
                        <label for="city">עיר מגורים</label>
                        <div class="city-selector">
                            <input type="text" id="city" name="city" class="city-search" placeholder="הקלד שם עיר..." 
                                   value="<?= htmlspecialchars($user_data['city'] ?? '') ?>" autocomplete="off">
                            <div class="city-suggestions" id="citySuggestions"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">💾 שמור הגדרות</button>
                <button type="reset" class="btn btn-secondary">🔄 איפוס</button>
            </div>
        </form>
        
        <?php else: ?>
            <div class="alert alert-error">
                לא ניתן לטעון את נתוני המשתמש. אנא נסה להתחבר מחדש.
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
    
    <script>
        // Israeli cities data
        const israeliCities = [
            'תל אביב-יפו', 'ירושלים', 'חיפה', 'ראשון לציון', 'אשדוד', 'נתניה', 'באר שבע',
            'בני ברק', 'חולון', 'רמת גן', 'אשקלון', 'רחובות', 'בת ים', 'כפר סבא', 'הרצליה',
            'חדרה', 'מודיעין', 'נצרת', 'לוד', 'רמלה', 'רעננה', 'גבעתיים', 'קריית אתא',
            'קריית גת', 'קריות', 'נוף הגליל', 'אור יהודה', 'יהוד-מונוסון', 'דימונה',
            'טמרה', 'סחנין', 'שפרעם', 'כרמיאל', 'מעלה אדומים', 'אילת', 'מגדל העמק',
            'בית שמש', 'עכו', 'ום אל-פחם', 'רמת השרון', 'קריית מוצקין', 'הוד השרון',
            'פתח תקווה', 'ראש העין', 'קריית ביאליק', 'קריית ים', 'מעלות-תרשיחא',
            'אלעד', 'טירת כרמל', 'יקנעם', 'שוהם'
        ];

        // City search functionality
        document.getElementById('city').addEventListener('input', function(e) {
            const input = e.target.value.toLowerCase();
            const suggestions = document.getElementById('citySuggestions');
            
            if (input.length < 2) {
                suggestions.style.display = 'none';
                return;
            }

            const filtered = israeliCities.filter(city => 
                city.toLowerCase().includes(input)
            ).slice(0, 10);

            if (filtered.length > 0) {
                suggestions.innerHTML = filtered.map(city => 
                    `<div class="city-suggestion" onclick="selectCity('${city}')">${city}</div>`
                ).join('');
                suggestions.style.display = 'block';
            } else {
                suggestions.style.display = 'none';
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.city-selector')) {
                document.getElementById('citySuggestions').style.display = 'none';
            }
        });

        function selectCity(city) {
            document.getElementById('city').value = city;
            document.getElementById('citySuggestions').style.display = 'none';
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!firstName || !lastName || !email) {
                e.preventDefault();
                alert('אנא מלא את כל השדות החובה (שם פרטי, שם משפחה ואימייל)');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('אנא הזן כתובת אימייל תקינה');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '⏳ שומר...';
            submitBtn.disabled = true;
            
            // Re-enable button after 3 seconds (fallback)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value;
            // Remove all non-digits
            value = value.replace(/\D/g, '');
            
            // Format Israeli phone numbers
            if (value.length >= 3) {
                if (value.startsWith('05')) {
                    // Mobile: 05X-XXX-XXXX
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
                } else if (value.startsWith('0')) {
                    // Landline: 0X-XXX-XXXX
                    value = value.replace(/(\d{2})(\d{3})(\d{4})/, '$1-$2-$3');
                }
            }
            
            e.target.value = value;
        });

        // Form dirty state tracking
        let formDirty = false;
        document.querySelectorAll('input').forEach(field => {
            field.addEventListener('input', () => {
                formDirty = true;
            });
        });

        // Warn user before leaving if form is dirty
        window.addEventListener('beforeunload', function(e) {
            if (formDirty) {
                e.preventDefault();
                e.returnValue = 'יש לך שינויים שלא נשמרו. האם אתה בטוח שברצונך לעזוב?';
            }
        });

        // Reset form dirty state on successful submit
        document.querySelector('form').addEventListener('submit', function() {
            formDirty = false;
        });
    </script>
    <script src="js/script.js"></script>
    <?php include 'includes/chat_bot_widget.php'; ?>
</body>
</html>