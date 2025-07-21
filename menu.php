<?php
require_once 'config/database.php';

$conn = getDBConnection();

$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_query);

$menu_items_query = "SELECT mi.*, c.category_name 
    FROM menu_items mi 
    LEFT JOIN categories c ON mi.category_id = c.category_id 
    WHERE mi.availability = 'available' 
    ORDER BY c.category_name, mi.item_name";
$menu_items_result = $conn->query($menu_items_query);

$menu_by_category = [];
if ($menu_items_result->num_rows > 0) {
    while ($item = $menu_items_result->fetch_assoc()) {
        $category = $item['category_name'] ?? 'Uncategorized';
        $menu_by_category[$category][] = $item;
    }
}

$search_results = null;
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = sanitizeInput($_GET['search']);
    $search_sql = "SELECT mi.*, c.category_name 
        FROM menu_items mi 
        LEFT JOIN categories c ON mi.category_id = c.category_id 
        WHERE mi.availability = 'available' 
        AND (mi.item_name LIKE ? OR mi.description LIKE ? OR c.category_name LIKE ?)
        ORDER BY mi.item_name";
    $search_term = "%{$search_query}%";
    $stmt = $conn->prepare($search_sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $search_results = $stmt->get_result();
}

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Menu - Delicious Restaurant</title>
  <link rel="stylesheet" href="css/menu.css" />
</head>
<body>

<header>
  <div>
    <h1>Delicious <span>Restaurant</span></h1>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </nav>
  </div>
</header>
<main>

  <section>
    <h2>Our Delicious Menu</h2>
    <p>Discover our carefully crafted dishes made with the finest ingredients.</p>
  </section>

  <section>
    <h3>Search Our Menu</h3>
    <form method="GET" action="menu.php">
      <label for="search">Find your favorite dish</label>
      <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>" />
      <button type="submit">Search</button>
      <?php if (!empty($search_query)): ?>
        <a href="menu.php">Clear</a>
      <?php endif; ?>
    </form>
  </section>

  <?php if ($search_results !== null): ?>
    <section>
      <h3>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h3>

      <?php if ($search_results->num_rows > 0): ?>
        <?php while($item = $search_results->fetch_assoc()): ?>
          <article>
            <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
            <p><?php echo htmlspecialchars($item['category_name']); ?></p>
            <p><?php echo htmlspecialchars($item['description']); ?></p>
            <p>$<?php echo number_format($item['price'], 2); ?></p>
            <a href="add_to_cart.php?item_id=<?php echo $item['item_id']; ?>">Order Now</a>
          </article>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No items found matching your search.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>
  <?php if (!$search_results && count($menu_by_category) > 0): ?>
    <section>
      <h3>Browse by Category</h3>
      <nav>
        <ul>
          <?php foreach (array_keys($menu_by_category) as $category): ?>
            <li>
              <a href="#category-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                <?php echo htmlspecialchars($category); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </section>
  <?php endif; ?>

  <?php if (!$search_results): ?>
    <?php foreach ($menu_by_category as $category => $items): ?>
      <section id="category-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
        <h2><?php echo htmlspecialchars($category); ?></h2>

        <?php foreach ($items as $item): ?>
          <article>
            <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
            <p><?php echo htmlspecialchars($item['description']); ?></p>
            <p>$<?php echo number_format($item['price'], 2); ?></p>
            <a href="login.php">Order Now</a>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>
  <?php if (empty($menu_by_category) && !$search_results): ?>
    <section>
      <h3>Menu Coming Soon!</h3>
      <p>We're preparing something delicious for you. Please check back soon.</p>
      <a href="contact.php">Contact Us for Updates</a>
    </section>
  <?php endif; ?>

  <?php if (!empty($menu_by_category)): ?>
    <section>
      <h3>Ready to Order?</h3>
      <p>Create an account or login to place your order.</p>
      <a href="user_login.php">Login to Order</a>
      <a href="register.php">Create Account</a>
    </section>
  <?php endif; ?>

</main>

<footer>
  <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
  <p>Developed for COS107 - Web Programming and Application</p>
</footer>

</body>
</html>
