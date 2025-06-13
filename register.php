<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// הגדרות חיבור למסד נתונים
$host = "localhost";
$user = "isorye_WISECART";
$pass = "NOMbys230799!";
$db = "isorye_product_prices";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("שגיאת חיבור: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// בדיקה אם הטופס נשלח
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // קבלת נתונים מהטופס
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // בדיקות תקינות
    $errors = [];

    // בדיקת שדות ריקים
    if (empty($first_name)) $errors[] = "שם פרטי הוא שדה חובה";
    if (empty($last_name)) $errors[] = "שם משפחה הוא שדה חובה";
    if (empty($email)) $errors[] = "כתובת אימייל היא שדה חובה";
    if (empty($phone)) $errors[] = "מספר טלפון הוא שדה חובה";
    if (empty($city)) $errors[] = "עיר מגורים היא שדה חובה";
    if (empty($password)) $errors[] = "סיסמה היא שדה חובה";

    // בדיקת תקינות אימייל
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "כתובת האימייל אינה תקינה";
    }

    // בדיקת אורך סיסמה
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "הסיסמה חייבת להכיל לפחות 6 תווים";
    }

    // בדיקת התאמת סיסמאות
    if ($password !== $confirm_password) {
        $errors[] = "הסיסמאות אינן זהות";
    }

    // בדיקת תקינות טלפון (ספרות ותווים מותרים)
    if (!empty($phone) && !preg_match('/^[0-9\-\+\s\(\)]+$/', $phone)) {
        $errors[] = "מספר הטלפון אינו תקין";
    }

    // בדיקת אורך שמות
    if (!empty($first_name) && strlen($first_name) < 2) {
        $errors[] = "השם הפרטי חייב להכיל לפחות 2 תווים";
    }
    if (!empty($last_name) && strlen($last_name) < 2) {
        $errors[] = "שם המשפחה חייב להכיל לפחות 2 תווים";
    }

    // בדיקה אם האימייל כבר קיים במערכת
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "כתובת האימייל כבר רשומה במערכת";
            }
            $stmt->close();
        }
    }

    // אם יש שגיאות, חזור לדף ההרשמה עם השגיאות
    if (!empty($errors)) {
        $error_string = implode("|", $errors);
        $redirect_url = "EXsigninPage.php?error=validation&messages=" . urlencode($error_string);
        
        // שמירת הנתונים למילוי חוזר (ללא סיסמאות)
        $redirect_url .= "&first_name=" . urlencode($first_name);
        $redirect_url .= "&last_name=" . urlencode($last_name);
        $redirect_url .= "&email=" . urlencode($email);
        $redirect_url .= "&phone=" . urlencode($phone);
        $redirect_url .= "&city=" . urlencode($city);
        
        header("Location: $redirect_url");
        exit();
    }

    // הצפנת הסיסמה
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // תאריכי תקופת ניסיון
    $trial_start = date('Y-m-d H:i:s');
    $trial_end = date('Y-m-d H:i:s', strtotime('+1 month'));

    // ניסיון הכנסה למסד הנתונים עם אפשרויות שונות
    $success = false;
    $user_id = null;

    // אפשרות 1 - עם כל השדות כולל subscription
    $stmt = $conn->prepare("
        INSERT INTO users 
        (email, password, first_name, last_name, phone, city, 
         subscription_type, subscription_status, trial_start_date, trial_end_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'trial', 'active', ?, ?, NOW())
    ");

    if ($stmt) {
        $stmt->bind_param("ssssssss", $email, $hashed_password, $first_name, $last_name, $phone, $city, $trial_start, $trial_end);
        if ($stmt->execute()) {
            $success = true;
            $user_id = $conn->insert_id;
        }
        $stmt->close();
    }

    // אפשרות 2 - ללא subscription fields
    if (!$success) {
        $stmt = $conn->prepare("
            INSERT INTO users 
            (email, password, first_name, last_name, phone, city, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        if ($stmt) {
            $stmt->bind_param("ssssss", $email, $hashed_password, $first_name, $last_name, $phone, $city);
            if ($stmt->execute()) {
                $success = true;
                $user_id = $conn->insert_id;
            }
            $stmt->close();
        }
    }

    // אפשרות 3 - ללא created_at
    if (!$success) {
        $stmt = $conn->prepare("
            INSERT INTO users 
            (email, password, first_name, last_name, phone, city) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt) {
            $stmt->bind_param("ssssss", $email, $hashed_password, $first_name, $last_name, $phone, $city);
            if ($stmt->execute()) {
                $success = true;
                $user_id = $conn->insert_id;
            }
            $stmt->close();
        }
    }

    // אפשרות 4 - שדות בסיסיים בלבד
    if (!$success) {
        $stmt = $conn->prepare("
            INSERT INTO users 
            (email, password, first_name, last_name) 
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt) {
            $stmt->bind_param("ssss", $email, $hashed_password, $first_name, $last_name);
            if ($stmt->execute()) {
                $success = true;
                $user_id = $conn->insert_id;
            }
            $stmt->close();
        }
    }

    if ($success && $user_id) {
        // הרשמה הצליחה - התחברות אוטומטית
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        $_SESSION['logged_in'] = true;
        $_SESSION['id'] = $user_id;

        // הפניה לדף הבית עם הודעת הצלחה
        header("Location: ../index.html?registered=success&welcome=" . urlencode($first_name));
        exit();
        
    } else {
        // שגיאה בהכנסה למסד הנתונים
        error_log("Database error during registration: " . $conn->error);
        header("Location: EXsigninPage.php?error=database&message=" . urlencode("שגיאה בשמירת הנתונים. אנא נסו שוב מאוחר יותר."));
        exit();
    }

} else {
    // אם לא הגיע POST request, הפנה לדף ההרשמה
    header("Location: EXsigninPage.php");
    exit();
}

$conn->close();
?>