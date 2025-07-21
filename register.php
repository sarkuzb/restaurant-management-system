<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Account - Delicious Restaurant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/admin.css" />
</head>
<body class="login-body">

<header class="main-header">
  <h1 class="site-title">Delicious <span>Restaurant</span></h1>
  <nav class="main-nav">
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="menu.php">Menu</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>
</header>

<main class="login-main">
  <section class="login-box">
    <h2>Create Account</h2>
    <p>Join us to start ordering delicious food!</p>

    <?php if (!empty($success_message)): ?>
      <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
      <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" id="registerForm" class="login-form">
      <fieldset>
        <legend>Registration Form</legend>

        <label for="username">Username *</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required />

        <label for="full_name">Full Name *</label>
        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required />

        <label for="email">Email Address *</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required />

        <label for="password">Password *</label>
        <input type="password" name="password" id="password" required />

        <label for="confirm_password">Confirm Password *</label>
        <input type="password" name="confirm_password" id="confirm_password" required />

        <label for="phone">Phone Number</label>
        <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" />

        <label for="address">Address</label>
        <textarea name="address" id="address" rows="2"><?php echo htmlspecialchars($address ?? ''); ?></textarea>

        <button type="submit" class="login-btn">Create Account</button>
      </fieldset>
    </form>

    <div class="login-links">
      <p>Already have an account? <a href="user_login.php">Login here</a></p>
      <p><a href="index.php">← Back to Home</a></p>
    </div>
  </section>
</main>

<footer class="main-footer">
  <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
  <p>Developed for COS107 - Web Programming and Application</p>
</footer>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    const password = form.querySelector('input[name="password"]');
    const confirm = form.querySelector('input[name="confirm_password"]');

    form.addEventListener('submit', function (e) {
      if (password.value.length < 6) {
        e.preventDefault();
        alert("Password must be at least 6 characters.");
        return;
      }
      if (password.value !== confirm.value) {
        e.preventDefault();
        alert("Passwords do not match.");
      }
    });

    password.focus();
  });
</script>

</body>
</html>
