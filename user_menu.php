<?php
require_once 'config/auth.php';
requireUser();

$conn = getDBConnection();

$query = "SELECT mi.*, c.category_name 
          FROM menu_items mi 
          LEFT JOIN categories c ON mi.category_id = c.category_id 
          WHERE mi.availability = 'available' 
          ORDER BY c.category_name, mi.item_name";

$result = $conn->query($query);

$menu_by_category = [];
while ($item = $result->fetch_assoc()) {
    $category = $item['category_name'] ?? 'Other';
    $menu_by_category[$category][] = $item;
}

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu - User Dashboard</title>
  <link rel="stylesheet" href="css/user.css" />
</head>
<body>

<header>
  <h1>Delicious Restaurant</h1>
  <nav>
    <ul>
      <li><a href="user_dashboard.php">Dashboard</a></li>
      <li><a href="user_menu.php">Menu</a></li>
      <li><a href="user_orders.php">My Orders</a></li>
      <li><a href="user_profile.php">Profile</a></li>
      <li><a href="logout.php?type=user">Logout</a></li>
    </ul>
  </nav>
</header>

<main>
  <section>
    <h2>Restaurant Menu</h2>
    <p>Browse our delicious offerings and place your order!</p>
  </section>

  <?php foreach ($menu_by_category as $category => $items): ?>
    <section>
      <h3><?php echo htmlspecialchars($category); ?></h3>

      <ul>
        <?php foreach ($items as $item): ?>
          <li>
            <article>
              <header>
                <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
              </header>
              <p><?php echo htmlspecialchars($item['description']); ?></p>
              <p><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
              <footer>
                <a href="add_to_cart.php?item_id=<?php echo $item['item_id']; ?>">Add to Order</a>
              </footer>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endforeach; ?>
</main>

<footer>
  <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
</footer>

</body>
</html>
