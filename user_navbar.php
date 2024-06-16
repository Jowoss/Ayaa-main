<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
require_once('classes/database.php');
$con = new Database();

// Check if admin_id is set in session
$admin_id = $_SESSION['admin_id'] ?? null;

// Fetch user data if admin_id is valid
$data = [];
if ($admin_id) {
    $data = $con->viewdata($admin_id);
}

// Default profile picture if not set in session
$profilePicture = $_SESSION['profile_picture'] ?? 'path/to/default/profile_picture.jpg';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Aya</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?php echo htmlspecialchars($data['profile_picture'] ?? $profilePicture); ?>" width="30" height="30" class="rounded-circle mr-1" alt="Profile Picture"> 
                    <?php echo htmlspecialchars($data['user'] ?? 'User'); ?> <!-- Sanitize and provide default -->
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>