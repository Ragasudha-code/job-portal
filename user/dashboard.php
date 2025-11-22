<?php
// user/dashboard.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
if (!is_logged_in()) header("Location: ../login.php");

$uid = $_SESSION['user_id'];

// summary counts
$stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM applications WHERE user_id = ?");
$stmt->bind_param('i',$uid); $stmt->execute(); $total = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $mysqli->prepare("SELECT status, COUNT(*) AS cnt FROM applications WHERE user_id = ? GROUP BY status");
$stmt->bind_param('i',$uid); $stmt->execute(); $res = $stmt->get_result();
$status_counts = ['Pending'=>0,'Shortlisted'=>0,'Rejected'=>0,'Hired'=>0];
while($r = $res->fetch_assoc()) $status_counts[$r['status']] = $r['cnt'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">JobPortal</a>
    <div>
      <span class="me-3"><?=esc($_SESSION['user_name'])?></span>
      <a href="../logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
    </div>
  </div>
</nav>
<section class="container py-4">
  <div class="row">
    <div class="col-md-3">
      <div class="sidebar">
        <h6>User Dashboard</h6>
        <ul class="list-unstyled">
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="apply.php">Apply for Job</a></li>
          <li><a href="my_applications.php">My Applications</a></li>
          <li><a href="profile.php">Profile Settings</a></li>
          <li><a href="../logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <div class="row g-3">
        <div class="col-md-4"><div class="card p-3"><h5>Total Applications</h5><h2><?=esc($total)?></h2></div></div>
        <?php foreach($status_counts as $k=>$v): ?>
          <div class="col-md-2"><div class="card p-2"><small><?=esc($k)?></small><h4><?=esc($v)?></h4></div></div>
        <?php endforeach; ?>
      </div>

      <div class="card mt-4 p-3">
        <h5>Recent Applications</h5>
        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>ID</th><th>Job</th><th>Status</th><th>Submitted</th><th>Action</th></tr></thead>
            <tbody>
              <?php
              $stmt = $mysqli->prepare("SELECT a.id,a.status,a.created_at,j.title FROM applications a LEFT JOIN jobs j ON a.job_id=j.id WHERE a.user_id=? ORDER BY a.created_at DESC LIMIT 6");
              $stmt->bind_param('i',$uid); $stmt->execute(); $rows = $stmt->get_result();
              while($r = $rows->fetch_assoc()):
              ?>
                <tr>
                  <td><?=esc($r['id'])?></td>
                  <td><?=esc($r['title'] ?? 'N/A')?></td>
                  <td><?=esc($r['status'])?></td>
                  <td><?=esc($r['created_at'])?></td>
                  <td><a href="view_application.php?id=<?=esc($r['id'])?>" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</section>
</body>
</html>
