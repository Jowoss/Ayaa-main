<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
require_once('classes/database.php');
$con = new Database();

$admin_id = $_SESSION['admin_id'];
 $data = $con->viewdata($admin_id);

// Assuming the profile picture URL is stored in the session or fetched from the database
$profilePicture = $_SESSION['profile_picture'] ?? 'path/to/default/profile_picture.jpg';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Aya</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="<?php echo $data['profile_picture'];?>" width="30" height="30" class="rounded-circle mr-1" alt="Profile Picture"> 
          <?php echo $data['user'];?> <!-- assuming $data['username'] is set -->
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </li>
    </ul>
  </div>
</nav>