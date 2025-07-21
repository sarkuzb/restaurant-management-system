<?php
/**
 * Add to Cart / Create Order Functionality
 * Restaurant Management System
 */

require_once 'config/auth.php';
requireUser();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

// Handle adding item to cart/order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_order'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    
    // Get item details
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE item_id = ? AND availability = 'available'");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    
    if ($item && $quantity > 0) {
        // Calculate total
        $unit_price = $item['price'];
        $subtotal = $unit_price * $quantity;
        $total_amount = $subtotal; // In real app, add tax, delivery, etc.
        
        // Create new order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, delivery_address, phone) VALUES (?, ?, 'pending', ?, ?)");
        $delivery_address = $_SESSION['user_address'] ?? 'No address provided';
        $phone = $_SESSION['user_phone'] ?? 'No phone provided';
        $stmt->bind_param("idss", $user_id, $total_amount, $delivery_address, $phone);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            
            // Add item to order
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidd", $order_id, $item_id, $quantity, $unit_price, $subtotal);
            
            if ($stmt->execute()) {
                $success_message = "Order placed successfully! Order #$order_id";
                // Redirect to orders page after 2 seconds
                header("refresh:2;url=user_orders.php");
            } else {
                $error_message = "Error adding item to order.";
            }
        } else {
            $error_message = "Error creating order.";
        }
    } else {
        $error_message = "Invalid item or quantity.";
    }
}

// Get item details if item_id is provided
$item = null;
if (isset($_GET['item_id'])) {
    $item_id = (int)$_GET['item_id'];
    $stmt = $conn->prepare("SELECT mi.*, c.category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.category_id WHERE mi.item_id = ? AND mi.availability = 'available'");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
}

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Order - Restaurant Management</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><h1>Delicious <span>Restaurant</span></h1></div>
            <nav class="nav">
                <ul>
                    <li><a href="user_dashboard.php">Dashboard</a></li>
                    <li><a href="user_menu.php">Menu</a></li>
                    <li><a href="user_orders.php">My Orders</a></li>
                    <li><a href="user_profile.php">Profile</a></li>
                    <li><a href="logout.php?type=user">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="card">
            <h2>Add Item to Order</h2>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
                <br><small>Redirecting to your orders...</small>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($item): ?>
            <div class="card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Item Details -->
                    <div>
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p style="color: #888; margin-bottom: 0.5rem;">
                            <strong>Category:</strong> <?php echo htmlspecialchars($item['category_name']); ?>
                        </p>
                        <p style="color: #666; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </p>
                        <div class="price" style="font-size: 1.5rem; margin-bottom: 1rem;">
                            $<?php echo number_format($item['price'], 2); ?>
                        </div>
                        <p style="color: #4CAF50;">
                            <strong>Status:</strong> <?php echo ucfirst($item['availability']); ?>
                        </p>
                    </div>

                    <!-- Order Form -->
                    <div>
                        <h4>Place Your Order</h4>
                        <form method="POST">
                            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                            
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <select name="quantity" id="quantity" class="form-control" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Total Price</label>
                                <div id="total_price" style="font-size: 1.2rem; font-weight: bold; color: #4CAF50;">
                                    $<?php echo number_format($item['price'], 2); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Delivery Address</label>
                                <p style="color: #666; font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($_SESSION['user_address'] ?? 'No address on file'); ?>
                                    <br><a href="user_profile.php" style="color: #667eea;">Update address</a>
                                </p>
                            </div>

                            <button type="submit" name="add_to_order" class="btn btn-success" style="width: 100%;">
                                Place Order
                            </button>
                        </form>

                        <div style="margin-top: 1rem;">
                            <a href="user_menu.php" class="btn btn-warning" style="width: 100%;">
                                Back to Menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="text-center">
                    <h3 style="color: #666;">Item Not Found</h3>
                    <p>The requested item is not available.</p>
                    <a href="user_menu.php" class="btn btn-primary">Back to Menu</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Calculate total price based on quantity
        document.addEventListener('DOMContentLoaded', function() {
            const quantitySelect = document.getElementById('quantity');
            const totalPriceDiv = document.getElementById('total_price');
            const unitPrice = <?php echo $item['price'] ?? 0; ?>;

            quantitySelect.addEventListener('change', function() {
                const quantity = parseInt(this.value);
                const total = unitPrice * quantity;
                totalPriceDiv.textContent = '$' + total.toFixed(2);
            });
        });
    </script>
</body>
</html>