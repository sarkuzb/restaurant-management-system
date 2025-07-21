<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $success_message = $stmt->execute() ? "Order status updated successfully!" : "Error updating order status.";
}

$stmt = $conn->prepare("
    SELECT o.*, u.username, u.full_name, u.email, u.phone as user_phone 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: admin_orders.php?error=Order not found");
    exit();
}

$stmt = $conn->prepare("
    SELECT oi.*, mi.item_name, mi.description, c.category_name 
    FROM order_items oi 
    LEFT JOIN menu_items mi ON oi.item_id = mi.item_id 
    LEFT JOIN categories c ON mi.category_id = c.category_id 
    WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> - Admin</title>
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
            <h2>Order #<?php echo $order_id; ?></h2>
            <p><a href="admin_orders.php">← Back to Orders</a></p>
        </section>

        <?php if ($success_message): ?>
            <section><p><?php echo htmlspecialchars($success_message); ?></p></section>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <section><p><?php echo htmlspecialchars($error_message); ?></p></section>
        <?php endif; ?>

        <section>
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($order_items->num_rows > 0): ?>
                        <?php $total_items = 0; ?>
                        <?php while($item = $order_items->fetch_assoc()): ?>
                            <?php $total_items += $item['quantity']; ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['item_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($item['description']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($item['category_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No items found for this order.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total Items: <?php echo $total_items ?? 0; ?></td>
                        <td colspan="3">Order Total:</td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <section>
            <h3>Order Info</h3>
            <p><strong>Order Date:</strong><br>
               <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></p>

            <p><strong>Current Status:</strong><br>
               <?php echo ucfirst($order['status']); ?></p>

            <p><strong>Total Amount:</strong><br>
               $<?php echo number_format($order['total_amount'], 2); ?></p>

            <form method="POST">
                <p><label for="status">Update Status:</label></p>
                <p>
                    <select name="status" id="status" required>
                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="preparing" <?php echo $order['status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                        <option value="ready" <?php echo $order['status'] === 'ready' ? 'selected' : ''; ?>>Ready</option>
                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </p>
                <p><button type="submit" name="update_status">Update Status</button></p>
            </form>
        </section>

        <section>
            <h3>Customer Info</h3>
            <p><strong>Full Name:</strong><br>
               <?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></p>

            <p><strong>Username:</strong><br>
               <?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></p>

            <p><strong>Email:</strong><br>
                <?php if ($order['email']): ?>
                    <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>">
                        <?php echo htmlspecialchars($order['email']); ?>
                    </a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </p>

            <p><strong>Phone:</strong><br>
                <?php if ($order['phone']): ?>
                    <a href="tel:<?php echo htmlspecialchars($order['phone']); ?>">
                        <?php echo htmlspecialchars($order['phone']); ?>
                    </a>
                <?php else: ?>
                    <?php echo htmlspecialchars($order['user_phone'] ?? 'N/A'); ?>
                <?php endif; ?>
            </p>

            <p><strong>Delivery Address:</strong><br>
               <?php echo nl2br(htmlspecialchars($order['delivery_address'] ?? 'Not provided')); ?></p>
        </section>

        <section>
            <h3>Actions</h3>
            <p>
                <button onclick="printOrder()">🖨️ Print Order</button>
                <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>?subject=Order Update #<?php echo $order_id; ?>">📧 Email</a>
                <a href="tel:<?php echo htmlspecialchars($order['phone'] ?: $order['user_phone']); ?>">📞 Call</a>
                <a href="admin_orders.php">← Back to All Orders</a>
            </p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>

    <script>
        function printOrder() {
            const body = `
                <h2>Order #<?php echo $order_id; ?></h2>
                <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></p>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?: $order['user_phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                <hr>
                <table>${document.querySelector('table tbody').innerHTML}</table>
                <p><strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong></p>
            `;
            const win = window.open('', '_blank');
            win.document.write(`<html><body>${body}</body></html>`);
            win.document.close();
            win.print();
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const current = '<?php echo $order['status']; ?>';
            const selected = this.status.value;
            if (current !== selected && !confirm(`Change status from "${current}" to "${selected}"?`)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
