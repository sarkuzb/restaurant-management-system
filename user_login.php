<?php
require_once 'config/auth.php';

if (isUserLoggedIn()) {
    header("Location: user_dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } elseif (authenticateUser($username, $password)) {
        header("Location: user_dashboard.php");
        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Login - Restaurant Management</title>
  <link rel="stylesheet" href="css/admin.css" />
</head>
<body class="login-body">

<header class="main-header">
  <h1 class="site-title">Delicious <span>Restaurant</span></h1>
  <nav class="main-nav">
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="menu.php">Menu</a></li>
      <li><a href="about.php">About Us</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="admin_login.php">Admin Login</a></li>
    </ul>
  </nav>
</header>

<main class="login-main">
  <section class="login-box">
    <h2>Customer Login</h2>
    <p>Access your account to place orders</p>

    <?php if (!empty($error_message)): ?>
      <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
      <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="post" action="user_login.php" class="login-form">
      <fieldset>
        <legend>Login Credentials</legend>

        <label for="username">Username</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
          required 
        />

        <label for="password">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          required 
        />

        <button type="submit" class="login-btn">Login</button>
      </fieldset>
    </form>

    <div class="demo-info">
      <h3>Demo Credentials</h3>
      <p>
        Username: <strong>john_doe</strong><br>
        Password: <strong>user123</strong>
      </p>
    </div>

    <div class="login-links">
      <a href="register.php">Register Here</a> |
      <a href="admin_login.php">Admin Login</a> |
      <a href="index.php">← Back to Home</a>
    </div>
  </section>
</main>

<footer class="main-footer">
  <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
  <p>Developed for COS107 - Web Programming and Application</p>
</footer>

</body>
</html>
