<?php
// user/my_applications.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
if (!is_logged_in()) header("Location: ../login.php");
$uid = $_SESSION['user_id'];

$q = $mysqli->prepare("SELECT a.*, j.title FROM applications a LEFT JOIN jobs j ON a.job_id=j.id WHERE a.user_id = ? ORDER BY a.created_at DESC");
$q->bind_param('i', $uid); $q->execute(); $res = $q->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>My Applications</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/_user_nav.php' ?? ''; ?>
<section class="container py-4">
  <div class="row">
    <div class="col-md-3">
      <div class="sidebar"> ... <!-- same sidebar as others --> </div>
    </div>
    <div class="col-md-9">
      <div class="card p-3">
        <h5>My Applications</h5>
        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>ID</th><th>Job</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
            <tbody>
              <?php while($a = $res->fetch_assoc()): ?>
                <tr>
                  <td><?=esc($a['id'])?></td>
                  <td><?=esc($a['title'] ?? 'N/A')?></td>
                  <td><?=esc($a['status'])?> <?= $a['is_draft'] ? '<span class="badge bg-secondary">Draft</span>' : '' ?></td>
                  <td><?=esc($a['created_at'])?></td>
                  <td>
                    <a href="view_application.php?id=<?=esc($a['id'])?>" class="btn btn-sm btn-outline-primary">View</a>
                    <?php if($a['is_draft']): ?>
                      <a href="apply.php?id=<?=esc($a['id'])?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <?php endif; ?>
                    <a href="download.php?id=<?=esc($a['id'])?>" class="btn btn-sm btn-outline-success">Download</a>
                    <a href="delete_app.php?id=<?=esc($a['id'])?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete application?')">Delete</a>
                  </td>
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
