<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM menu_items WHERE category_id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $item_count = $check_stmt->get_result()->fetch_assoc()['count'];

    if ($item_count > 0) {
        $error_message = "Cannot delete category. It has $item_count menu items.";
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "Category deleted.";
        } else {
            $error_message = "Error deleting category.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = sanitizeInput($_POST['category_name']);
    $description = sanitizeInput($_POST['description']);

    if (!empty($category_name)) {
        $check_stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $check_stmt->bind_param("s", $category_name);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            $error_message = "Category already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $category_name, $description);
            if ($stmt->execute()) {
                $success_message = "Category added.";
                $_POST = array();
            } else {
                $error_message = "Error adding category.";
            }
        }
    } else {
        $error_message = "Category name required.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $category_id = (int)$_POST['category_id'];
    $category_name = sanitizeInput($_POST['category_name']);
    $description = sanitizeInput($_POST['description']);

    if (!empty($category_name)) {
        $check_stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ? AND category_id != ?");
        $check_stmt->bind_param("si", $category_name, $category_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            $error_message = "Category already exists.";
        } else {
            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
            $stmt->bind_param("ssi", $category_name, $description, $category_id);
            if ($stmt->execute()) {
                $success_message = "Category updated.";
            } else {
                $error_message = "Error updating category.";
            }
        }
    } else {
        $error_message = "Category name required.";
    }
}

$categories_result = $conn->query("
    SELECT c.*, COUNT(mi.item_id) as item_count
    FROM categories c
    LEFT JOIN menu_items mi ON c.category_id = mi.category_id
    GROUP BY c.category_id
    ORDER BY c.category_name
");

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Categories</title>
    <link rel="stylesheet" href="css/style.css">
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
                <li><a href="admin_categories.php">Categories</a></li>
                <li><a href="logout.php?type=admin">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Manage Categories</h2>
            <p>Organize your menu by adding and editing categories.</p>
        </section>

        <?php if (!empty($success_message)): ?>
            <section>
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </section>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <section>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </section>
        <?php endif; ?>

        <section>
            <h3>Add New Category</h3>
            <form method="POST">
                <p>
                    <label for="category_name">Name *</label><br>
                    <input type="text" name="category_name" id="category_name" value="<?php echo htmlspecialchars($_POST['category_name'] ?? ''); ?>" required>
                </p>
                <p>
                    <label for="description">Description</label><br>
                    <textarea name="description" id="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </p>
                <p>
                    <button type="submit" name="add_category">Add Category</button>
                </p>
            </form>
        </section>

        <section>
            <h3>Existing Categories</h3>
            <?php if ($categories_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Items</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $cat['category_id']; ?></td>
                                <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($cat['description'] ?: 'No description'); ?></td>
                                <td><?php echo $cat['item_count']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($cat['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
                                        <input type="hidden" name="category_name" value="<?php echo htmlspecialchars($cat['category_name']); ?>">
                                        <input type="hidden" name="description" value="<?php echo htmlspecialchars($cat['description']); ?>">
                                        <button type="submit" name="edit_category">Edit</button>
                                    </form>
                                    <?php if ($cat['item_count'] == 0): ?>
                                        <a href="admin_categories.php?delete=<?php echo $cat['category_id']; ?>" onclick="return confirm('Delete this category?')">Delete</a>
                                    <?php else: ?>
                                        <span>Cannot delete</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No categories found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>
</body>
</html>
