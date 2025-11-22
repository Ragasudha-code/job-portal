<?php
// login.php
require_once 'config.php';
require_once 'functions.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pwd = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT id,name,password,is_blocked FROM users WHERE email = ?");
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && password_verify($pwd, $res['password'])) {
        if ($res['is_blocked']) {
            $err = "Your account is blocked. Contact admin.";
        } else {
            $_SESSION['user_id'] = $res['id'];
            $_SESSION['user_name'] = $res['name'];
            header("Location: user/dashboard.php");
            exit;
        }
    } else $err = "Invalid credentials.";
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>User Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-md-5">
    <div class="card p-4">
      <h4>User Login</h4>
      <?php if($err) echo '<div class="alert alert-danger">'.esc($err).'</div>'; ?>
      <form method="post">
        <div class="mb-3"><label>Email</label><input name="email" type="email" required class="form-control"></div>
        <div class="mb-3"><label>Password</label><input name="password" type="password" required class="form-control"></div>
        <div class="d-flex justify-content-between">
          <a href="register.php">Register</a>
          <button class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>
  </div></div>
</div>
</body>
</html>
