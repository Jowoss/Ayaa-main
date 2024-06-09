<div class="sidebar">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    
      <link rel="stylesheet" href="./css/sidebar.css">

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <ul class="navbar-nav flex-column">
      <li class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="dashboard.php">Dashboard<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'category.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="category.php">Category<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'product.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="product.php">Product<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="admin.php">Admin<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'livesearch.php' OR $current_page == 'update.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="livesearch.php">Live Seach</a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'purchased.php' OR $current_page == 'update.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="purchased.php">Purchased</a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </nav>
</div>
