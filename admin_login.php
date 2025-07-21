<?php
require_once 'config/auth.php';

if (isAdminLoggedIn()) {
    header("Location: admin_dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } elseif (authenticateAdmin($username, $password)) {
        header("Location: admin_dashboard.php");
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
  <title>Admin Login - Restaurant Management</title>
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
      <li><a href="user_login.php">User Login</a></li>
    </ul>
  </nav>
</header>

<main class="login-main">
  <section class="login-box">
    <h2>Admin Login</h2>
    <p>Access the restaurant management system</p>

    <?php if (!empty($error_message)): ?>
      <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
      <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="admin_login.php" class="login-form" id="adminLoginForm">
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

        <button type="submit" class="login-btn">Login as Admin</button>
      </fieldset>
    </form>

    <div class="demo-info">
      <h3>Demo Credentials</h3>
      <p class="clickable-demo" style="cursor:pointer;">
        Username: <strong>admin</strong><br>
        Password: <strong>admin123</strong>
      </p>
    </div>

    <div class="login-links">
      <a href="user_login.php">Login as User</a> |
      <a href="index.php">← Back to Home</a>
    </div>
  </section>
</main>

<footer class="main-footer">
  <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
  <p>Developed for COS107 - Web Programming and Application</p>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('adminLoginForm');
  const usernameField = document.getElementById('username');
  const passwordField = document.getElementById('password');

  function validateField(field, minLength = 1) {
    const value = field.value.trim();
    const formGroup = field.closest('fieldset');
    let errorElement = field.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
      errorElement.remove();
    }

    if (value.length === 0) {
      field.classList.add('error');
      showFieldError(field, 'This field is required.');
      return false;
    } else if (value.length < minLength) {
      field.classList.add('error');
      showFieldError(field, `Must be at least ${minLength} characters.`);
      return false;
    } else {
      field.classList.remove('error');
      return true;
    }
  }

  function showFieldError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    field.insertAdjacentElement('afterend', errorDiv);
  }

  usernameField.addEventListener('blur', () => validateField(usernameField, 3));
  passwordField.addEventListener('blur', () => validateField(passwordField, 6));

  form.addEventListener('submit', function (e) {
    const validUsername = validateField(usernameField, 3);
    const validPassword = validateField(passwordField, 6);
    if (!validUsername || !validPassword) {
      e.preventDefault();
    }
  });

  [usernameField, passwordField].forEach(field => {
    field.addEventListener('input', function () {
      this.classList.remove('error');
      const next = this.nextElementSibling;
      if (next && next.classList.contains('error-message')) {
        next.remove();
      }
    });
  });

  // Autofill demo
  document.querySelector('.clickable-demo').addEventListener('click', function () {
    usernameField.value = 'admin';
    passwordField.value = 'admin123';
    usernameField.focus();
  });

  // Loading state
  form.addEventListener('submit', function () {
    const btn = this.querySelector('button[type="submit"]');
    btn.textContent = 'Logging in...';
    btn.disabled = true;
  });
});
</script>

</body>
</html>
