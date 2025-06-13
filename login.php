<?php
session_start();

// טיפול בהתחברות אוטומטית
if (isset($_GET['auto']) && $_GET['auto'] === 'true' &&
    isset($_SESSION['auto_login_email']) && isset($_SESSION['auto_login_password'])) {
    $_POST['email'] = $_SESSION['auto_login_email'];
    $_POST['password'] = $_SESSION['auto_login_password'];
    unset($_SESSION['auto_login_email'], $_SESSION['auto_login_password']);
}

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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // בדיקה אם השדות ריקים
    if (empty($email) || empty($password)) {
        $redirect_url = "EXloginPage.php?error=empty_fields";
        if (!empty($email)) {
            $redirect_url .= "&email=" . urlencode($email);
        }
        header("Location: $redirect_url");
        exit();
    }

    // בדיקת תקינות אימייל
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: EXloginPage.php?error=invalid_email&email=" . urlencode($email));
        exit();
    }

    // חיפוש המשתמש במסד הנתונים
    $stmt = $conn->prepare("SELECT id, email, password, first_name, last_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // בדיקת הסיסמה
        if (password_verify($password, $user['password'])) {
            // התחברות מצליחה - יצירת session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['logged_in'] = true;
            $_SESSION['id'] = $user['id'];
            
            header("Location: ../index.html");
            exit();
        } else {
            // סיסמה שגויה
            header("Location: EXloginPage.php?error=wrong_password&email=" . urlencode($email));
            exit();
        }
    } else {
        // משתמש לא נמצא
        header("Location: EXloginPage.php?error=user_not_found&email=" . urlencode($email));
        exit();
    }
}

// אם לא הגיע POST request, הפנה לדף ההתחברות
header("Location: EXloginPage.php");
exit();
?>