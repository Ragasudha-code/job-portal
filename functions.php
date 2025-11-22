<?php
// functions.php
require_once __DIR__ . '/config.php';

function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin_logged() {
    return isset($_SESSION['admin_id']);
}

// redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// simple file upload for resume/profile
function handle_file_upload($file_field, $allowed = ['pdf','doc','docx','jpg','jpeg','png']) {
    if (!isset($_FILES[$file_field]) || $_FILES[$file_field]['error'] !== UPLOAD_ERR_OK) return null;
    $f = $_FILES[$file_field];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    $newName = uniqid() . '.' . $ext;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $dst = UPLOAD_DIR . $newName;
    if (move_uploaded_file($f['tmp_name'], $dst)) {
        return $newName;
    }
    return null;
}
?>
