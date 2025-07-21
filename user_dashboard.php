<?php
require_once 'config/auth.php';
requireUser();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Total orders
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_orders = $stmt->get_result()->fetch_assoc()['count'];

// Total spent
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND status != 'cancelled'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_spent = $stmt->get_result()->fetch_assoc()['total'];

// Recent orders
$stmt = $conn->prepare("SELECT order_id, total_amount, status, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result();

// Featured menu items
$menu_items = $conn->query("SELECT mi.item_id, mi.item_name, mi.description, mi.price, c.category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.category_id WHERE mi.availability = 'available' ORDER BY c.category_name, mi.item_name LIMIT 8");

// Search functionality
$search_results = null;
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = sanitizeInput($_GET['search']);
    $search_sql = "SELECT mi.item_id, mi.item_name, mi.description, mi.price, c.category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.category_id WHERE mi.availability = 'available' AND (mi.item_name LIKE ? OR mi.description LIKE ? OR c.category_name LIKE ?) ORDER BY mi.item_name";
    $search_term = "%{$search_query}%";
    $stmt = $conn->prepare($search_sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $search_results = $stmt->get_result();
}

// Active orders
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status IN ('pending','confirmed','preparing')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_orders = $stmt->get_result()->fetch_assoc()['count'];

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Delicious Restaurant</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<header>
<h1>Delicious Restaurant</h1>
    <nav>
        <ul>
            <li><a href="user_dashboard.php" aria-current="page">Dashboard</a></li>
            <li><a href="user_menu.php">Browse Menu</a></li>
            <li><a href="user_orders.php">My Orders</a></li>
            <li><a href="user_profile.php">Profile</a></li>
            <li><a href="logout.php?type=user">Logout</a></li>
        </ul>
    </nav>
    
</header>

<main>
    <section>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
        <p>Explore our menu and enjoy your meals.</p>
    </section>

    <section>
        <h2>Your Stats</h2>
        <ul>
            <li>Total Orders: <?php echo $total_orders; ?></li>
            <li>Total Spent: $<?php echo number_format($total_spent, 2); ?></li>
            <li>Active Orders: <?php echo $active_orders; ?></li>
            <li>Status: VIP</li>
        </ul>
    </section>

    <section>
        <h2>Search Menu</h2>
        <form method="get" action="user_dashboard.php">
            <label for="search">Find items</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="e.g. pizza, dessert">
            <button type="submit">Search</button>
            <?php if (!empty($search_query)): ?>
                <a href="user_dashboard.php">Clear</a>
            <?php endif; ?>
        </form>

        <?php if ($search_results !== null): ?>
            <section>
                <h3>Results for "<?php echo htmlspecialchars($search_query); ?>"</h3>
                <?php if ($search_results->num_rows > 0): ?>
                    <ul>
                        <?php while($item = $search_results->fetch_assoc()): ?>
                            <li>
                                <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <p>Category: <?php echo htmlspecialchars($item['category_name']); ?></p>
                                <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                                <a href="add_to_cart.php?item_id=<?php echo $item['item_id']; ?>">Add to Order</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No matching items found.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </section>

    <section>
        <h2>Quick Links</h2>
        <nav>
            <ul>
                <li><a href="user_menu.php">Browse Full Menu</a></li>
                <li><a href="user_orders.php">View My Orders</a></li>
                <li><a href="user_profile.php">Update Profile</a></li>
                <li><a href="contact.php">Contact Restaurant</a></li>
            </ul>
        </nav>
    </section>

    <section>
        <h2>Recent Orders</h2>
        <?php if ($recent_orders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo ucfirst($order['status']); ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                            <td><a href="user_order_view.php?id=<?php echo $order['order_id']; ?>">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p><a href="user_orders.php">View All Orders</a></p>
        <?php else: ?>
            <p>No orders yet. <a href="user_menu.php">Order now</a></p>
        <?php endif; ?>
    </section>

    <section>
        <h2>Featured Menu</h2>
        <?php if ($menu_items->num_rows > 0): ?>
            <ul>
                <?php while($item = $menu_items->fetch_assoc()): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <p>Category: <?php echo htmlspecialchars($item['category_name']); ?></p>
                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <a href="add_to_cart.php?item_id=<?php echo $item['item_id']; ?>">Add to Order</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No items available currently.</p>
        <?php endif; ?>
        <p><a href="user_menu.php">View Full Menu</a></p>
    </section>
</main>

<footer>
    <p>&copy; 2025 Delicious Restaurant</p>
    <p>Developed for COS107 - Web Programming and Application</p>
</footer>

</body>
</html>
