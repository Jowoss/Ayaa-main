<?php
session_start();

if (isset($_SESSION['admin_id'])) {
  if (isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
    // logout logic here
    session_destroy();
    header('Location: login.php');
    exit;
  } else {
   ?>
    <script>
      if (confirm('Are you sure you want to leave?')) {
        window.location.href = 'logout.php?confirm=true';
      } else {
        window.location.href = 'dashboard.php'; // or any other page
      }
    </script>
    <?php
  }
} else {
  header('Location: login.php');
  exit;
}
?>