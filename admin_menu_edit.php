<?php
require_once 'config/auth.php';
requireAdmin();

$conn = getDBConnection();
$success_message = '';
$error_message = '';

$item_id = (int)($_GET['id'] ?? 0);
if ($item_id <= 0) {
    header("Location: admin_menu.php?error=Invalid item ID");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = sanitizeInput($_POST['item_name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $availability = $_POST['availability'];

    if (!empty($item_name) && $price > 0) {
        $stmt = $conn->prepare("UPDATE menu_items SET item_name = ?, description = ?, price = ?, category_id = ?, availability = ? WHERE item_id = ?");
        $stmt->bind_param("ssidsi", $item_name, $description, $price, $category_id, $availability, $item_id);

        $success_message = $stmt->execute() ? "Menu item updated successfully!" : "Error updating menu item.";
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

$stmt = $conn->prepare("SELECT * FROM menu_items WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    header("Location: admin_menu.php?error=Item not found");
    exit();
}

$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Item</title>
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
            <h2>Edit Menu Item #<?php echo $item['item_id']; ?></h2>
            <p><a href="admin_menu.php">← Back to Menu</a></p>
        </section>

        <?php if (!empty($success_message)): ?>
            <section><p><?php echo htmlspecialchars($success_message); ?></p></section>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <section><p><?php echo htmlspecialchars($error_message); ?></p></section>
        <?php endif; ?>

        <section>
            <h3>Menu Item Details</h3>
            <form method="POST">
                <p>
                    <label for="item_name">Item Name *</label><br>
                    <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
                </p>

                <p>
                    <label for="price">Price *</label><br>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $item['price']; ?>" required>
                </p>

                <p>
                    <label for="category_id">Category</label><br>
                    <select id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php while($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo $item['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </p>

                <p>
                    <label for="availability">Availability</label><br>
                    <select id="availability" name="availability">
                        <option value="available" <?php echo $item['availability'] === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="unavailable" <?php echo $item['availability'] === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                    </select>
                </p>

                <p>
                    <label for="description">Description</label><br>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
                </p>

                <p>
                    <button type="submit">Update Item</button>
                    <a href="admin_menu.php">Cancel</a>
                    <a href="admin_menu.php?delete=<?php echo $item['item_id']; ?>" onclick="return confirm('Delete this item permanently?')">Delete Item</a>
                </p>
            </form>
        </section>

        <section>
            <h3>Preview</h3>
            <article>
                <h4 id="preview-name"><?php echo htmlspecialchars($item['item_name']); ?></h4>
                <p id="preview-description"><?php echo htmlspecialchars($item['description']); ?></p>
                <p id="preview-price">$<?php echo number_format($item['price'], 2); ?></p>
                <p id="preview-status"><?php echo ucfirst($item['availability']); ?></p>
            </article>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('item_name');
            const descInput = document.getElementById('description');
            const priceInput = document.getElementById('price');
            const statusSelect = document.getElementById('availability');

            const previewName = document.getElementById('preview-name');
            const previewDesc = document.getElementById('preview-description');
            const previewPrice = document.getElementById('preview-price');
            const previewStatus = document.getElementById('preview-status');

            nameInput.addEventListener('input', () => {
                previewName.textContent = nameInput.value || 'Item Name';
            });

            descInput.addEventListener('input', () => {
                previewDesc.textContent = descInput.value || 'Item description...';
            });

            priceInput.addEventListener('input', () => {
                const val = parseFloat(priceInput.value) || 0;
                previewPrice.textContent = '$' + val.toFixed(2);
            });

            statusSelect.addEventListener('change', () => {
                const val = statusSelect.value;
                previewStatus.textContent = val.charAt(0).toUpperCase() + val.slice(1);
            });
        });
    </script>
</body>
</html>
