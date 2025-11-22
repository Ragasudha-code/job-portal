<?php
// index.php
require_once 'config.php';
if (isset($_SESSION['user_id'])) header("Location: user/dashboard.php");
if (isset($_SESSION['admin_id'])) header("Location: admin/dashboard.php");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Job Portal - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">JobPortal</a>
    <div>
      <a href="register.php" class="btn btn-outline-primary me-2">Register</a>
      <a href="login.php" class="btn btn-primary">User Login</a>
      <a href="admin/login.php" class="btn btn-secondary ms-2">Admin Login</a>
    </div>
  </div>
</nav>

<section class="container py-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h1>Apply to jobs easily</h1>
      <p class="small-muted">Register as a candidate, submit applications, track statuses and receive notifications.</p>
      <a href="register.php" class="btn btn-primary">Get started</a>
    </div>
    <div class="col-md-6 text-center">
      <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg" style="width:150px;opacity:.2">
    </div>
  </div>
</section>
</body>
</html>
