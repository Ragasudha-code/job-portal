<?php
// user/apply.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
if (!is_logged_in()) header("Location: ../login.php");

$uid = $_SESSION['user_id'];

// fetch jobs for dropdown
$jobs = $mysqli->query("SELECT id,title FROM jobs ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'] ?: null;
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Other';
    $skills = trim($_POST['skills'] ?? '');
    $cover = trim($_POST['cover_letter'] ?? '');
    $is_draft = isset($_POST['is_draft']) ? 1 : 0;

    // education & experience arrays (expecting repeated fields)
    $education = [];
    if (!empty($_POST['edu_degree'])) {
        for($i=0;$i<count($_POST['edu_degree']);$i++){
            if (trim($_POST['edu_degree'][$i])=='') continue;
            $education[] = [
                'degree' => $_POST['edu_degree'][$i],
                'institution' => $_POST['edu_institution'][$i],
                'year' => $_POST['edu_year'][$i]
            ];
        }
    }

    $experience = [];
    if (!empty($_POST['exp_company'])) {
        for($i=0;$i<count($_POST['exp_company']);$i++){
            if (trim($_POST['exp_company'][$i])=='') continue;
            $experience[] = [
                'company' => $_POST['exp_company'][$i],
                'duration' => $_POST['exp_duration'][$i],
                'role' => $_POST['exp_role'][$i],
                'description' => $_POST['exp_desc'][$i]
            ];
        }
    }

    // resume upload
    $resumeFile = handle_file_upload('resume', ['pdf','doc','docx']);
    // basic validations
    if (!$name) $errors[] = "Name required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO applications (user_id, job_id, name, email, phone, address, dob, gender, education, skills, experience, resume, cover_letter, is_draft) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $edu_json = json_encode($education);
        $exp_json = json_encode($experience);
        $stmt->bind_param('iisssssssssss', $uid, $job_id, $name, $email, $phone, $address, $dob, $gender, $edu_json, $skills, $exp_json, $resumeFile, $cover, $is_draft);
        // bind_param mismatch fix: convert to correct types
        // simpler: use prepared with types: i i s s s s s s s s s s i
        $stmt = $mysqli->prepare("INSERT INTO applications (user_id, job_id, name, email, phone, address, dob, gender, education, skills, experience, resume, cover_letter, is_draft) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('iissssssssssi', $uid, $job_id, $name, $email, $phone, $address, $dob, $gender, $edu_json, $skills, $exp_json, $resumeFile, $cover, $is_draft);
        if ($stmt->execute()) {
            header("Location: my_applications.php");
            exit;
        } else {
            $errors[] = "Failed to save application.";
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Apply</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/app.js" defer></script>
</head>
<body>
<?php include __DIR__ . '/_user_nav.php' ?? ''; ?>
<section class="container py-4">
  <div class="row">
    <div class="col-md-3">
      <div class="sidebar">
        <h6>User Dashboard</h6>
        <ul class="list-unstyled">
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="apply.php">Apply for Job</a></li>
          <li><a href="my_applications.php">My Applications</a></li>
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card p-3">
        <h5>Apply for Job</h5>
        <?php if(!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo "<div>".esc($e)."</div>"; ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label>Job (optional)</label>
            <select name="job_id" class="form-select">
              <option value="">-- Select --</option>
              <?php foreach($jobs as $j): ?>
                <option value="<?=esc($j['id'])?>"><?=esc($j['title'])?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label form-required">Name</label><input name="name" class="form-control" required></div>
            <div class="col-md-6 mb-3"><label class="form-label form-required">Email</label><input name="email" type="email" class="form-control" required></div>
          </div>

          <div class="mb-3"><label>Phone</label><input name="phone" class="form-control"></div>
          <div class="mb-3"><label>Address</label><textarea name="address" class="form-control"></textarea></div>

          <h6>Education</h6>
          <div id="education_list">
            <div class="row g-2 mb-2">
              <div class="col-md-4"><input name="edu_degree[]" class="form-control" placeholder="Degree"></div>
              <div class="col-md-4"><input name="edu_institution[]" class="form-control" placeholder="Institution"></div>
              <div class="col-md-2"><input name="edu_year[]" class="form-control" placeholder="Year"></div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addEducation()">+ Add Education</button>

          <h6>Experience</h6>
          <div id="exp_list">
            <div class="row g-2 mb-2">
              <div class="col-md-3"><input name="exp_company[]" class="form-control" placeholder="Company"></div>
              <div class="col-md-2"><input name="exp_duration[]" class="form-control" placeholder="Duration"></div>
              <div class="col-md-3"><input name="exp_role[]" class="form-control" placeholder="Role"></div>
              <div class="col-md-4"><input name="exp_desc[]" class="form-control" placeholder="Brief description"></div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addExperience()">+ Add Experience</button>

          <div class="mb-3"><label>Skills (comma separated)</label><input name="skills" class="form-control"></div>
          <div class="mb-3"><label>Resume (pdf/doc)</label><input name="resume" type="file" class="form-control"></div>
          <div class="mb-3"><label>Cover letter</label><textarea name="cover_letter" class="form-control"></textarea></div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_draft" id="save_draft">
            <label class="form-check-label" for="save_draft">Save as draft</label>
          </div>

          <button id="submit_btn" class="btn btn-primary">Submit Application</button>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
function addEducation(){
  const html = `<div class="row g-2 mb-2">
    <div class="col-md-4"><input name="edu_degree[]" class="form-control" placeholder="Degree"></div>
    <div class="col-md-4"><input name="edu_institution[]" class="form-control" placeholder="Institution"></div>
    <div class="col-md-2"><input name="edu_year[]" class="form-control" placeholder="Year"></div>
    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">Remove</button></div>
  </div>`;
  document.getElementById('education_list').insertAdjacentHTML('beforeend', html);
}
function addExperience(){
  const html = `<div class="row g-2 mb-2">
    <div class="col-md-3"><input name="exp_company[]" class="form-control" placeholder="Company"></div>
    <div class="col-md-2"><input name="exp_duration[]" class="form-control" placeholder="Duration"></div>
    <div class="col-md-3"><input name="exp_role[]" class="form-control" placeholder="Role"></div>
    <div class="col-md-3"><input name="exp_desc[]" class="form-control" placeholder="Brief description"></div>
    <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">X</button></div>
  </div>`;
  document.getElementById('exp_list').insertAdjacentHTML('beforeend', html);
}
</script>
</body>
</html>
