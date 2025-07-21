<?php
require_once 'config/auth.php';
requireUser();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $user_id = $_SESSION['user_id'];

    if (validateEmail($email)) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $phone;
            $_SESSION['user_address'] = $address;
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile.";
        }
    } else {
        $error_message = "Invalid email address.";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile</title>
  <link rel="stylesheet" href="css/dashboard.css" />
</head>
<body>

<header>
  <h1>Delicious Restaurant</h1>
  <nav>
    <ul>
      <li><a href="user_dashboard.php">Dashboard</a></li>
      <li><a href="user_menu.php">Menu</a></li>
      <li><a href="user_orders.php">My Orders</a></li>
      <li><a href="user_profile.php">Profile</a></li>
      <li><a href="logout.php?type=user">Logout</a></li>
    </ul>
  </nav>
</header>

<main>

  <section>
    <h2>My Profile</h2>
    <p>Update your account information below.</p>
  </section>

  <?php if ($success_message): ?>
    <section><p><?php echo htmlspecialchars($success_message); ?></p></section>
  <?php elseif ($error_message): ?>
    <section><p><?php echo htmlspecialchars($error_message); ?></p></section>
  <?php endif; ?>

  <section>
    <h3>Update Information</h3>
    <form method="post">
      <p>
        <label>Username<br>
          <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
        </label>
      </p>
      <p>
        <label>Full Name<br>
          <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </label>
      </p>
      <p>
        <label>Email Address<br>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </label>
      </p>
      <p>
        <label>Phone Number<br>
          <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </label>
      </p>
      <p>
        <label>Address<br>
          <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
        </label>
      </p>
      <p>
        <button type="submit" name="update_profile">Update Profile</button>
      </p>
    </form>
  </section>

  <section>
    <h3>Account Summary</h3>
    <p><strong>Member Since:</strong><br><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
    <p><strong>Status:</strong><br>Active</p>
    <p><strong>Total Orders:</strong><br>
      <?php
      $conn = getDBConnection();
      $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
      $stmt->bind_param("i", $user['user_id']);
      $stmt->execute();
      echo $stmt->get_result()->fetch_assoc()['count'];
      closeDBConnection($conn);
      ?>
    </p>
  </section>

  <section>
    <h3>Quick Links</h3>
    <ul>
      <li><a href="user_menu.php">Browse Menu</a></li>
      <li><a href="user_orders.php">View My Orders</a></li>
      <li><a href="contact.php">Contact Support</a></li>
      <li><a href="logout.php?type=user">Logout</a></li>
    </ul>
  </section>

  <section>
    <h3>Preferences (Demo)</h3>
    <ul>
      <li><strong>Notifications:</strong> Order updates, Promotions (enabled)</li>
      <li><strong>Dietary:</strong> Vegetarian, Vegan, Gluten-free (disabled)</li>
      <li><strong>Delivery:</strong> SMS notifications enabled</li>
    </ul>
    <p><em>Preferences are static and not editable in this version.</em></p>
  </section>

</main>

<footer>
  <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
</footer>

</body>
</html>
