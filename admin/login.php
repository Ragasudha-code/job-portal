<?php
// admin/login.php
require_once __DIR__ . '/../config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; $pwd = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT id,name,password FROM admins WHERE email = ?");
    $stmt->bind_param('s',$email); $stmt->execute(); $a = $stmt->get_result()->fetch_assoc();
    if ($a && password_verify($pwd, $a['password'])) {
        $_SESSION['admin_id'] = $a['id'];
        $_SESSION['admin_name'] = $a['name'];
        header("Location: dashboard.php");
        exit;
    } else $err = "Invalid admin credentials.";
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-5">
  <div class="card p-4">
    <h4>Admin Login</h4>
    <?php if($err) echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; ?>
    <form method="post">
      <div class="mb-3"><label>Email</label><input name="email" class="form-control" required></div>
      <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required></div>
      <button class="btn btn-primary">Login</button>
    </form>
  </div>
</div></div></div>
</body>
</html>
