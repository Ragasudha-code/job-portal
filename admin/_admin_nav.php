<?php
// _admin_nav.php
?>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
    <div>
      <span class="me-3"><?=esc($_SESSION['admin_name'])?></span>
      <a href="../logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
    </div>
  </div>
</nav>
