<?php
// admin/dashboard.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
if (!is_admin_logged()) header("Location: login.php");

// summary counts
$total_apps = $mysqli->query("SELECT COUNT(*) AS c FROM applications")->fetch_assoc()['c'];
$status_res = $mysqli->query("SELECT status, COUNT(*) AS c FROM applications GROUP BY status");
$status_counts = ['Pending'=>0,'Shortlisted'=>0,'Rejected'=>0,'Hired'=>0];
while($r = $status_res->fetch_assoc()) $status_counts[$r['status']] = $r['c'];
$total_users = $mysqli->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm"><div class="container">
  <a class="navbar-brand" href="#">Admin Panel</a>
  <div><span class="me-3"><?=esc($_SESSION['admin_name'])?></span><a href="../logout.php" class="btn btn-outline-secondary btn-sm">Logout</a></div>
</div></nav>
<section class="container py-4">
  <div class="row">
    <div class="col-md-3">
      <div class="sidebar">
        <ul class="list-unstyled">
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="manage_applications.php">Manage Applications</a></li>
          <li><a href="manage_users.php">User Management</a></li>
          <li><a href="manage_jobs.php">Manage Jobs</a></li>
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <div class="row g-3">
        <div class="col-md-4"><div class="card p-3"><h6>Total Applications</h6><h3><?=esc($total_apps)?></h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Registered Users</h6><h3><?=esc($total_users)?></h3></div></div>
      </div>
      <div class="card mt-3 p-3">
        <h6>Applications by Status</h6>
        <div class="row">
          <?php foreach($status_counts as $k=>$v): ?>
            <div class="col-md-3"><div class="p-2"><strong><?=esc($k)?></strong><div><?=esc($v)?></div></div></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>
