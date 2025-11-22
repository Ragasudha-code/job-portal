<?php
// register.php
require_once 'config.php';
require_once 'functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = $_POST['phone'] ?? '';

    if (!$name) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 chars.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows>0) {
            $errors[] = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare("INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)");
            $ins->bind_param('ssss',$name,$email,$hash,$phone);
            if ($ins->execute()) {
                $_SESSION['user_id'] = $ins->insert_id;
                $_SESSION['user_name'] = $name;
                header("Location: user/dashboard.php");
                exit;
            } else {
                $errors[] = "Registration failed.";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4">
        <h4>Create account</h4>
        <?php if(!empty($errors)): ?>
          <div class="alert alert-danger">
            <?php foreach($errors as $e) echo "<div>".esc($e)."</div>"; ?>
          </div>
        <?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
            <div class="small-muted">min 6 characters</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control">
          </div>
          <div class="d-flex justify-content-between">
            <a href="login.php">Already have an account?</a>
            <button class="btn btn-primary">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
