<?php
// user/profile.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
if (!is_logged_in()) header("Location: ../login.php");
$uid = $_SESSION['user_id'];

$msg = ''; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name']; $phone = $_POST['phone']; $address = $_POST['address'];
        $photo = handle_file_upload('profile_photo', ['jpg','jpeg','png']);
        $sql = $mysqli->prepare("UPDATE users SET name=?, phone=?, address=?".($photo? ", profile_photo=?" : "")." WHERE id=?");
        if ($photo) $sql->bind_param('ssssi', $name,$phone,$address,$photo,$uid); else $sql->bind_param('sssi',$name,$phone,$address,$uid);
        if ($sql->execute()) $msg = "Profile updated.";
    }
    if (isset($_POST['change_pass'])) {
        $cur = $_POST['current_password']; $new = $_POST['new_password'];
        $r = $mysqli->query("SELECT password FROM users WHERE id = $uid")->fetch_assoc();
        if (!password_verify($cur, $r['password'])) $errors[] = "Current password incorrect.";
        elseif (strlen($new) < 6) $errors[] = "New password min 6 chars.";
        else {
            $h = password_hash($new, PASSWORD_DEFAULT);
            $mysqli->query("UPDATE users SET password = '".$mysqli->real_escape_string($h)."' WHERE id = $uid");
            $msg = "Password changed.";
        }
    }
}
$user = $mysqli->query("SELECT * FROM users WHERE id = $uid")->fetch_assoc();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/_user_nav.php' ?? ''; ?>
<section class="container py-4">
  <div class="row">
    <div class="col-md-3"><div class="sidebar">...</div></div>
    <div class="col-md-9">
      <div class="card p-3">
        <h5>Profile Settings</h5>
        <?php if($msg) echo '<div class="alert alert-success">'.esc($msg).'</div>'; ?>
        <?php if($errors) echo '<div class="alert alert-danger">'.esc(implode(', ',$errors)).'</div>'; ?>
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3"><label>Name</label><input name="name" value="<?=esc($user['name'])?>" class="form-control"></div>
          <div class="mb-3"><label>Phone</label><input name="phone" value="<?=esc($user['phone'])?>" class="form-control"></div>
          <div class="mb-3"><label>Address</label><textarea name="address" class="form-control"><?=esc($user['address'])?></textarea></div>
          <div class="mb-3"><label>Profile Photo</label><input type="file" name="profile_photo" class="form-control"></div>
          <button name="update_profile" class="btn btn-primary">Update Profile</button>
        </form>
        <hr>
        <h6>Change Password</h6>
        <form method="post">
          <div class="mb-3"><label>Current</label><input name="current_password" type="password" class="form-control"></div>
          <div class="mb-3"><label>New Password</label><input name="new_password" type="password" class="form-control"></div>
          <button name="change_pass" class="btn btn-warning">Change</button>
        </form>
      </div>
    </div>
  </div>
</section>
</body>
</html>
