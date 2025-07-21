<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $admin_id = $_SESSION['admin_id'];

    if (validateEmail($email)) {
        $stmt = $conn->prepare("UPDATE admins SET full_name = ?, email = ? WHERE admin_id = ?");
        $stmt->bind_param("ssi", $full_name, $email, $admin_id);
        if ($stmt->execute()) {
            $_SESSION['admin_name'] = $full_name;
            $_SESSION['admin_email'] = $email;
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile.";
        }
    } else {
        $error_message = "Invalid email address.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_id = $_SESSION['admin_id'];

    if ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        if (changeAdminPassword($admin_id, $old_password, $new_password)) {
            $success_message = "Password changed successfully!";
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}

// Fetch admin info
$stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <h1>Delicious <span>Restaurant</span></h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_menu.php">Menu</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
                <li><a href="admin_users.php">Users</a></li>
                <li><a href="admin_profile.php">Profile</a></li>
                <li><a href="logout.php?type=admin">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Admin Profile</h2>
        </section>

        <?php if (!empty($success_message)): ?>
            <section><p><?php echo htmlspecialchars($success_message); ?></p></section>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <section><p><?php echo htmlspecialchars($error_message); ?></p></section>
        <?php endif; ?>

        <section>
            <h3>Update Profile Information</h3>
            <form method="POST">
                <p>
                    <label>Username</label><br>
                    <input type="text" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                </p>
                <p><small>Username cannot be changed</small></p>
                <p>
                    <label>Full Name</label><br>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                </p>
                <p>
                    <label>Email Address</label><br>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </p>
                <p>
                    <button type="submit" name="update_profile">Update Profile</button>
                </p>
            </form>
        </section>

        <section>
            <h3>Change Password</h3>
            <form method="POST">
                <p>
                    <label>Current Password</label><br>
                    <input type="password" name="old_password" required>
                </p>
                <p>
                    <label>New Password</label><br>
                    <input type="password" name="new_password" required>
                </p>
                <p><small>Must be at least 6 characters</small></p>
                <p>
                    <label>Confirm New Password</label><br>
                    <input type="password" name="confirm_password" required>
                </p>
                <p>
                    <button type="submit" name="change_password">Change Password</button>
                </p>
            </form>
        </section>

        <section>
            <h3>Account Information</h3>
            <ul>
                <li><strong>Account Created:</strong> <?php echo date('F j, Y g:i A', strtotime($admin['created_at'])); ?></li>
                <li><strong>Last Login:</strong> <?php echo date('F j, Y g:i A'); ?> (Current Session)</li>
                <li><strong>Account Type:</strong> Administrator</li>
                <li><strong>Status:</strong> Active</li>
            </ul>
        </section>

        <section>
            <h3>Quick Actions</h3>
            <p>
                <a href="admin_dashboard.php">Go to Dashboard</a>
                <a href="admin_menu.php">Manage Menu</a>
                <a href="admin_orders.php">View Orders</a>
                <a href="logout.php?type=admin">Logout</a>
            </p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelectorAll('form')[1];
            form.addEventListener('submit', function (e) {
                const newPassword = this.querySelector('input[name="new_password"]').value;
                const confirmPassword = this.querySelector('input[name="confirm_password"]').value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert("Passwords do not match.");
                }

                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert("Password must be at least 6 characters.");
                }
            });
        });
    </script>
</body>
</html>
