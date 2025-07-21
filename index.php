<?php
/**
 * Restaurant Management System - Home Page
 */

require_once 'config/database.php';
$conn = getDBConnection();

// Fetch featured menu items
$featured_items_query = "SELECT * FROM menu_items WHERE availability = 'available' ORDER BY created_at DESC LIMIT 6";
$featured_items_result = $conn->query($featured_items_query);

// Get total counts for stats
$total_items = $conn->query("SELECT COUNT(*) as count FROM menu_items WHERE availability = 'available'")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Delicious Restaurant - Home</title>
  <link rel="stylesheet" href="css/home.css" />
</head>
<body>

  <!-- Header -->
  <header>
    <div>
      <div>
        <h1>Delicious <span>Restaurant</span></h1>
      </div>
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

  <!-- Main Content -->
  <main>

    <!-- Welcome -->
    <section>
      <div>
        <h2>Welcome to Delicious Restaurant</h2>
        <p>
          Experience the finest culinary delights with our expertly crafted dishes.
          From traditional favorites to modern innovations, we serve meals that satisfy every palate.
        </p>
      </div>
    </section>

    <!-- Stats -->
    <section>
      <article>
        <div><?php echo $total_items; ?></div>
        <div>Menu Items</div>
      </article>
      <article>
        <div><?php echo $total_categories; ?></div>
        <div>Categories</div>
      </article>
      <article>
        <div>15+</div>
        <div>Years Experience</div>
      </article>
      <article>
        <div>1000+</div>
        <div>Happy Customers</div>
      </article>
    </section>

    <!-- Featured Items -->
    <section>
      <h2>Featured Menu Items</h2>
      <div>
        <?php if ($featured_items_result->num_rows > 0): ?>
          <?php while($item = $featured_items_result->fetch_assoc()): ?>
            <article>
              <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
              <p><?php echo htmlspecialchars($item['description']); ?></p>
              <div>$<?php echo number_format($item['price'], 2); ?></div>
              <div>
                <span><?php echo ucfirst($item['availability']); ?></span>
              </div>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No menu items available at the moment.</p>
        <?php endif; ?>
      </div>
      <div>
        <a href="menu.php">View Full Menu</a>
      </div>
    </section>

    <!-- Quick Access -->
    <section>
      <h2>Quick Access</h2>
      <div>
        <article>
          <h3>For Customers</h3>
          <p>Browse our menu, place orders, and track your order status.</p>
          <a href="user_login.php">Customer Login</a>
        </article>
        <article>
          <h3>For Administrators</h3>
          <p>Manage menu items, orders, users, and restaurant operations.</p>
          <a href="admin_login.php">Admin Login</a>
        </article>
      </div>
    </section>

    <!-- Why Choose Us -->
    <section>
      <h2>Why Choose Delicious Restaurant?</h2>
      <div>
        <article>
          <h4>Fresh Ingredients</h4>
          <p>We use only the freshest, locally sourced ingredients to ensure the highest quality in every dish.</p>
        </article>
        <article>
          <h4>Expert Chefs</h4>
          <p>Our experienced chefs bring creativity and passion to every meal they prepare.</p>
        </article>
        <article>
          <h4>Fast Service</h4>
          <p>Quick and efficient service without compromising on quality or taste.</p>
        </article>
        <article>
          <h4>Great Ambiance</h4>
          <p>Enjoy your meal in a comfortable and welcoming environment perfect for any occasion.</p>
        </article>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer>
    <div>
      <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
      <p>Developed for COS107 - Web Programming and Application</p>
    </div>
  </footer>

  <script>
    // Smooth scroll + stat card animation
    document.addEventListener('DOMContentLoaded', function() {
      const links = document.querySelectorAll('a[href^="#"]');
      links.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const targetSection = document.querySelector(this.getAttribute('href'));
          if (targetSection) {
            targetSection.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

      document.querySelectorAll('article').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
      });
    });
  </script>

</body>
</html>
