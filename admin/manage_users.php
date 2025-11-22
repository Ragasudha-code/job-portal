<?php
// admin/manage_users.php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../functions.php';
if (!is_admin_logged()) header("Location: login.php");

// toggle block
if (isset($_GET['block']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $b = ($_GET['block'] == '1') ? 1 : 0;
    $mysqli->query("UPDATE users SET is_blocked = $b WHERE id = $id");
}
$users = $mysqli->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/_admin_nav.php' ?? ''; ?>
<section class="container py-4">
  <h5>Registered Users</h5>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Blocked</th><th>Action</th></tr></thead>
    <tbody>
      <?php while($u = $users->fetch_assoc()): ?>
        <tr>
          <td><?=esc($u['id'])?></td>
          <td><?=esc($u['name'])?></td>
          <td><?=esc($u['email'])?></td>
          <td><?=esc($u['phone'])?></td>
          <td><?= $u['is_blocked'] ? 'Yes' : 'No' ?></td>
          <td>
            <a class="btn btn-sm btn-outline-<?= $u['is_blocked'] ? 'success' : 'danger' ?>" href="?id=<?=esc($u['id'])?>&block=<?= $u['is_blocked'] ? '0' : '1' ?>"><?= $u['is_blocked'] ? 'Unblock' : 'Block' ?></a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</section>
</body>
</html>
