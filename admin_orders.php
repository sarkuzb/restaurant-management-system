<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    header("Location: admin_orders.php?success=Order status updated");
    exit();
}

// Get all orders
$query = "SELECT o.*, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC";
$result = $conn->query($query);

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Management - Admin</title>
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
                <li><a href="logout.php?type=admin">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Orders Management</h2>
        </section>

        <?php if (isset($_GET['success'])): ?>
            <section>
                <p><?php echo htmlspecialchars($_GET['success']); ?></p>
            </section>
        <?php endif; ?>

        <section>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="pending"   <?php echo $order['status'] === 'pending'   ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="preparing" <?php echo $order['status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                            <option value="ready"     <?php echo $order['status'] === 'ready'     ? 'selected' : ''; ?>>Ready</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <a href="admin_order_view.php?id=<?php echo $order['order_id']; ?>">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>
</body>
</html>
