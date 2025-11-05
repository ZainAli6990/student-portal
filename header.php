<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Portal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="nav">
  <div class="container">
    <h1><a href="dashboard.php">Student Portal</a></h1>
    <nav>
      <?php if (!empty($_SESSION['user'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
  <?php
  // Flash messages
  if (!empty($_SESSION['flash'])) {
      echo '<div class="flash">' . htmlspecialchars($_SESSION['flash']) . '</div>';
      unset($_SESSION['flash']);
  }
  ?>
<main>
</body>
</html>