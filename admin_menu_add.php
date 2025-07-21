<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = sanitizeInput($_POST['item_name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $availability = $_POST['availability'];

    if (!empty($item_name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO menu_items (item_name, description, price, category_id, availability) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $item_name, $description, $price, $category_id, $availability);

        if ($stmt->execute()) {
            $success_message = "Menu item added successfully!";
        } else {
            $error_message = "Error adding menu item.";
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Menu Item</title>
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

    <main class="main-content">
        <div class="card">
            <h2>Add New Menu Item</h2>
            <p><a href="admin_menu.php">← Back to Menu</a></p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Price *</label>
                    <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Availability</label>
                    <select name="availability" class="form-control">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Add Item</button>
                <a href="admin_menu.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>
</body>
</html>
