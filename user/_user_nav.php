<?php
// _user_nav.php
?>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="../index.php">JobPortal</a>
    <div>
      <span class="me-3"><?=esc($_SESSION['user_name'])?></span>
      <a href="../logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
    </div>
  </div>
</nav>
