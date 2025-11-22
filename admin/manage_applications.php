<?php
// admin/manage_applications.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
if (!is_admin_logged()) header("Location: login.php");

// handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $app_id = intval($_POST['app_id']); $status = $_POST['status'];
    $stmt = $mysqli->prepare("UPDATE applications SET status=?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $status, $app_id); $stmt->execute();
}

// search/filter
$search = $_GET['search'] ?? '';
$q = "SELECT a.*, u.name as user_name, j.title as job_title FROM applications a LEFT JOIN users u ON a.user_id=u.id LEFT JOIN jobs j ON a.job_id=j.id WHERE 1=1 ";
$params = [];
if ($search) {
    $s = "%".$mysqli->real_escape_string($search)."%";
    $q .= " AND (a.name LIKE '$s' OR a.email LIKE '$s' OR j.title LIKE '$s' OR u.name LIKE '$s')";
}
$q .= " ORDER BY a.created_at DESC LIMIT 200";
$res = $mysqli->query($q);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Applications</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/_admin_nav.php' ?? ''; ?>
<section class="container py-4">
  <h5>Manage Applications</h5>
  <form class="mb-3"><div class="input-group"><input name="search" class="form-control" placeholder="Search by name,email,job" value="<?=esc($search)?>"><button class="btn btn-primary">Search</button></div></form>

  <div class="table-responsive">
    <table class="table">
      <thead><tr><th>ID</th><th>Name</th><th>Job</th><th>Status</th><th>Submitted</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($r = $res->fetch_assoc()): ?>
          <tr>
            <td><?=esc($r['id'])?></td>
            <td><?=esc($r['name'])?> <div class="small-muted"><?=esc($r['user_name'])?></div></td>
            <td><?=esc($r['job_title'])?></td>
            <td><?=esc($r['status'])?></td>
            <td><?=esc($r['created_at'])?></td>
            <td>
              <a href="view_application.php?id=<?=esc($r['id'])?>" class="btn btn-sm btn-outline-primary">View</a>
              <form method="post" style="display:inline-block;">
                <input type="hidden" name="app_id" value="<?=esc($r['id'])?>">
                <select name="status" class="form-select form-select-sm d-inline-block" style="width:140px;display:inline-block;">
                  <option <?= $r['status']=='Pending'?'selected':'' ?>>Pending</option>
                  <option <?= $r['status']=='Shortlisted'?'selected':'' ?>>Shortlisted</option>
                  <option <?= $r['status']=='Rejected'?'selected':'' ?>>Rejected</option>
                  <option <?= $r['status']=='Hired'?'selected':'' ?>>Hired</option>
                </select>
                <button name="update_status" class="btn btn-sm btn-success">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</section>
</body>
</html>
