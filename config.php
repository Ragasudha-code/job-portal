<?php
// config.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database config - update to your credentials
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS',''); // set your password
define('DB_NAME','portal');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}

// file upload dir
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', '/uploads/'); // adjust if installed beneath subfolder
?>
