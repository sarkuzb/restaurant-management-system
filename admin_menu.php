<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();

// Handle delete action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE item_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_menu.php?success=Item deleted successfully");
    exit();
}

$query = "SELECT mi.*, c.category_name FROM menu_items mi 
          LEFT JOIN categories c ON mi.category_id = c.category_id 
          ORDER BY c.category_name, mi.item_name";
$result = $conn->query($query);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Management - Admin</title>
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
            <h2>Menu Management</h2>
            <p><a href="admin_menu_add.php">+ Add New Item</a></p>
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
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $item['item_id']; ?></td>
                                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name'] ?? 'Uncategorized'); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo ucfirst($item['availability']); ?></td>
                                <td>
                                    <a href="admin_menu_edit.php?id=<?php echo $item['item_id']; ?>">Edit</a>
                                    <a href="admin_menu.php?delete=<?php echo $item['item_id']; ?>" onclick="return confirm('Delete this item?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No menu items found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>
</body>
</html>
