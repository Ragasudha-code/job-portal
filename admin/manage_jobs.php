<?php
// admin/manage_jobs.php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../functions.php';
if (!is_admin_logged()) header("Location: login.php");

// create job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_job'])) {
    $t = $_POST['title']; $d = $_POST['description']; $skills = $_POST['skills']; $type = $_POST['job_type']; $deadline = $_POST['deadline'] ?: null;
    $stmt = $mysqli->prepare("INSERT INTO jobs (title,description,skills_required,job_type,deadline) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sssss',$t,$d,$skills,$type,$deadline);
    $stmt->execute();
}
$jobs = $mysqli->query("SELECT * FROM jobs ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Jobs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/_admin_nav.php' ?? ''; ?>
<section class="container py-4">
  <div class="row">
    <div class="col-md-6">
      <h5>Create Job</h5>
      <form method="post">
        <div class="mb-3"><label>Title</label><input name="title" class="form-control"></div>
        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="mb-3"><label>Skills (comma)</label><input name="skills" class="form-control"></div>
        <div class="mb-3"><label>Job Type</label>
          <select name="job_type" class="form-select">
            <option>Full-time</option><option>Part-time</option><option>Internship</option><option>Contract</option><option>Remote</option>
          </select>
        </div>
        <div class="mb-3"><label>Deadline</label><input name="deadline" type="date" class="form-control"></div>
        <button name="create_job" class="btn btn-primary">Create Job</button>
      </form>
    </div>
    <div class="col-md-6">
      <h5>Existing Jobs</h5>
      <table class="table">
        <thead><tr><th>ID</th><th>Title</th><th>Deadline</th></tr></thead>
        <tbody><?php while($j=$jobs->fetch_assoc()): ?>
          <tr><td><?=esc($j['id'])?></td><td><?=esc($j['title'])?></td><td><?=esc($j['deadline'])?></td></tr>
        <?php endwhile; ?></tbody>
      </table>
    </div>
  </div>
</section>
</body>
</html>
