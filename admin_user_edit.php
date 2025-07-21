<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

// Get user ID
$user_id = (int)($_GET['id'] ?? 0);
if ($user_id <= 0) {
    header("Location: admin_users.php?error=Invalid user ID");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $full_name = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    
    if (validateEmail($email)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, phone = ?, address = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $username, $email, $full_name, $phone, $address, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User updated successfully!";
        } else {
            $error_message = "Error updating user.";
        }
    } else {
        $error_message = "Invalid email address.";
    }
}

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: admin_users.php?error=User not found");
    exit();
}

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><h1>Delicious <span>Restaurant</span></h1></div>
            <nav class="nav">
                <ul>
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="admin_menu.php">Menu</a></li>
                    <li><a href="admin_orders.php">Orders</a></li>
                    <li><a href="admin_users.php">Users</a></li>
                    <li><a href="logout.php?type=admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="card">
            <div class="d-flex justify-between align-center">
                <h2>Edit User #<?php echo $user['user_id']; ?></h2>
                <a href="admin_users.php" class="btn btn-primary">← Back to Users</a>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <h3>User Information</h3>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Update User</button>
                    <a href="admin_users.php" class="btn btn-warning">Cancel</a>
                </div>
            </form>
        </div>

        <!-- User Stats -->
        <div class="card">
            <h3>User Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div class="text-center">
                    <strong>Member Since</strong><br>
                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                </div>
                <div class="text-center">
                    <strong>Total Orders</strong><br>
                    <?php 
                    $conn = getDBConnection();
                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
                    $stmt->bind_param("i", $user['user_id']);
                    $stmt->execute();
                    echo $stmt->get_result()->fetch_assoc()['count'];
                    closeDBConnection($conn);
                    ?>
                </div>
                <div class="text-center">
                    <strong>Account Status</strong><br>
                    <span style="color: #4CAF50;">Active</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container"><p>&copy; 2025 Delicious Restaurant. All rights reserved.</p></div>
    </footer>
</body>
</html>