<?php
/**
 * הגדרות חיבור למסד נתונים לטופס צור קשר
 * contact_config.php
 */

// הגדרות מסד נתונים - עדכן את הפרטים שלך כאן
define('DB_HOST', 'localhost');
define('DB_NAME', 'isorye_product_prices');  // שם מסד הנתונים שלך
define('DB_USER', 'isorye_WISECART');          // החלף בשם משתמש שלך
define('DB_PASS', 'NOMbys230799!');          // החלף בסיסמה שלך

// הגדרות אימייל
define('ADMIN_EMAIL', 'noaba3@mta.ac.il');     // החלף באימייל שלך

/**
 * פונקציה ליצירת חיבור למסד נתונים
 */
function getDatabaseConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
            DB_USER, 
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        throw new Exception('שגיאת חיבור למסד נתונים');
    }
}
?>