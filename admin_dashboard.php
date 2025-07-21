<?php
require_once 'config/auth.php';
requireAdmin();
$conn = getDBConnection();

$stats = [];
$stats['total_items'] = $conn->query("SELECT COUNT(*) as count FROM menu_items")->fetch_assoc()['count'];
$stats['total_categories'] = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$stats['total_users'] = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$stats['total_orders'] = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];

$recent_orders = $conn->query("
    SELECT o.order_id, u.full_name, o.total_amount, o.status, o.order_date 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");

$recent_items = $conn->query("
    SELECT item_id, item_name, price, availability 
    FROM menu_items 
    ORDER BY created_at DESC 
    LIMIT 5
");

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Restaurant Management</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <h1>Delicious <span>Restaurant</span></h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_menu.php">Menu Management</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
                <li><a href="admin_users.php">Users</a></li>
                <li><a href="admin_profile.php">Profile</a></li>
                <li><a href="logout.php?type=admin">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
            <p>Manage your restaurant operations from this dashboard.</p>
            <strong>Admin Panel</strong>
        </section>

        <section>
            <h2>Statistics</h2>
            <ul>
                <li>Menu Items: <?php echo $stats['total_items']; ?></li>
                <li>Registered Users: <?php echo $stats['total_users']; ?></li>
                <li>Total Orders: <?php echo $stats['total_orders']; ?></li>
                <li>Categories: <?php echo $stats['total_categories']; ?></li>
            </ul>
        </section>

        <section>
            <h2>Quick Actions</h2>
            <ul>
                <li><a href="admin_menu_add.php">+ Add Menu Item</a></li>
                <li><a href="admin_user_add.php">+ Register New User</a></li>
                <li><a href="admin_categories.php">Manage Categories</a></li>
                <li><a href="admin_reports.php">View Reports</a></li>
            </ul>
        </section>

        <section>
            <h2>Recent Orders</h2>
            <?php if ($recent_orders->num_rows > 0): ?>
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
                    <?php while($order = $recent_orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                        <td><a href="admin_order_view.php?id=<?php echo $order['order_id']; ?>">View</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p><a href="admin_orders.php">View All Orders</a></p>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Recently Added Menu Items</h2>
            <?php if ($recent_items->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $recent_items->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $item['item_id']; ?></td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo ucfirst($item['availability']); ?></td>
                        <td><a href="admin_menu_edit.php?id=<?php echo $item['item_id']; ?>">Edit</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p><a href="admin_menu.php">Manage All Items</a></p>
            <?php else: ?>
                <p>No menu items found.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>System Information</h2>
            <article>
                <h3>Login Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
                <p><strong>Login Time:</strong> <?php echo date('M j, Y g:i A'); ?></p>
            </article>
            <article>
                <h3>Quick Settings</h3>
                <p><a href="admin_profile.php">Update Profile</a></p>
                <p><a href="admin_password.php">Change Password</a></p>
                <p><a href="logout.php?type=admin">Logout</a></p>
            </article>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
        <p>Developed for COS107 - Web Programming and Application</p>
    </footer>
</body>
</html>
