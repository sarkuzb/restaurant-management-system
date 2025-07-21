<?php
require_once 'config/auth.php';
requireUser();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get user's orders
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - User Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><h1>Delicious <span>Restaurant</span></h1></div>
            <nav class="nav">
                <ul>
                    <li><a href="user_dashboard.php">Dashboard</a></li>
                    <li><a href="user_menu.php">Menu</a></li>
                    <li><a href="user_orders.php" class="active">My Orders</a></li>
                    <li><a href="user_profile.php">Profile</a></li>
                    <li><a href="logout.php?type=user">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="card">
            <h2>My Order History</h2>
            <p>Track your current and past orders</p>
        </div>

        <div class="card">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span style="background: 
                                            <?php 
                                            switch($order['status']) {
                                                case 'pending': echo '#ff9800'; break;
                                                case 'confirmed': echo '#2196F3'; break;
                                                case 'preparing': echo '#FF5722'; break;
                                                case 'ready': echo '#4CAF50'; break;
                                                case 'delivered': echo '#8BC34A'; break;
                                                case 'cancelled': echo '#f44336'; break;
                                                default: echo '#666';
                                            }
                                            ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="viewOrder(<?php echo $order['order_id']; ?>)">
                                            View Details
                                        </button>
                                        <?php if ($order['status'] == 'pending'): ?>
                                            <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                                                Cancel
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center" style="padding: 2rem;">
                    <h3 style="color: #666;">No Orders Yet</h3>
                    <p style="color: #888;">You haven't placed any orders. Start exploring our menu!</p>
                    <a href="user_menu.php" class="btn btn-success">Browse Menu</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Status Guide -->
        <div class="card">
            <h3>Order Status Guide</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; text-align: center;">
                <div><span style="background: #ff9800; color: white; padding: 0.25rem 0.5rem; border-radius: 3px;">Pending</span><br><small>Order received</small></div>
                <div><span style="background: #2196F3; color: white; padding: 0.25rem 0.5rem; border-radius: 3px;">Confirmed</span><br><small>Order confirmed</small></div>
                <div><span style="background: #FF5722; color: white; padding: 0.25rem 0.5rem; border-radius: 3px;">Preparing</span><br><small>Being prepared</small></div>
                <div><span style="background: #4CAF50; color: white; padding: 0.25rem 0.5rem; border-radius: 3px;">Ready</span><br><small>Ready for pickup</small></div>
                <div><span style="background: #8BC34A; color: white; padding: 0.25rem 0.5rem; border-radius: 3px;">Delivered</span><br><small>Order delivered</small></div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container"><p>&copy; 2025 Delicious Restaurant. All rights reserved.</p></div>
    </footer>

    <script>
        function viewOrder(orderId) {
            alert('Order details for #' + orderId + ' (Demo feature)');
            // In real implementation: window.location.href = 'order_details.php?id=' + orderId;
        }

        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                alert('Order #' + orderId + ' cancelled (Demo feature)');
                // In real implementation: window.location.href = 'cancel_order.php?id=' + orderId;
            }
        }
    </script>
</body>
</html>