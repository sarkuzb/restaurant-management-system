<?php
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        $success_message = 'Thank you for your message! We will get back to you within 24 hours.';
        $name = $email = $phone = $subject = $message = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - Delicious Restaurant</title>
  <link rel="stylesheet" href="css/contact.css" />
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
    <h2>Contact Us</h2>
    <p>We'd love to hear from you! Get in touch for reservations, questions, or feedback.</p>
  </section>
  <section>
    <article>
      <h3>Get in Touch</h3>
      <address>
        <p><strong>Address:</strong><br>123 Culinary Street, Food District, City 12345, United States</p>
        <p><strong>Phone:</strong> <a href="tel:+1234567890">+1 (234) 567-8900</a></p>
        <p><strong>Email:</strong> <a href="mailto:info@deliciousrestaurant.com">info@deliciousrestaurant.com</a></p>
        <p><strong>Website:</strong> <a href="http://www.deliciousrestaurant.com">www.deliciousrestaurant.com</a></p>
      </address>
    </article>

    <article>
      <h3>Send us a Message</h3>

      <?php if (!empty($success_message)): ?>
        <p><?php echo htmlspecialchars($success_message); ?></p>
      <?php endif; ?>

      <?php if (!empty($error_message)): ?>
        <p><?php echo htmlspecialchars($error_message); ?></p>
      <?php endif; ?>

      <form method="POST" action="contact.php">
        <label for="name">Full Name *</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required />

        <label for="email">Email Address *</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required />

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" />

        <label for="subject">Subject *</label>
        <select id="subject" name="subject" required>
          <option value="">Select a subject</option>
          <option value="reservation" <?php echo ($subject ?? '') === 'reservation' ? 'selected' : ''; ?>>Make a Reservation</option>
          <option value="catering" <?php echo ($subject ?? '') === 'catering' ? 'selected' : ''; ?>>Catering Services</option>
          <option value="feedback" <?php echo ($subject ?? '') === 'feedback' ? 'selected' : ''; ?>>Feedback & Reviews</option>
          <option value="complaint" <?php echo ($subject ?? '') === 'complaint' ? 'selected' : ''; ?>>Complaint</option>
          <option value="general" <?php echo ($subject ?? '') === 'general' ? 'selected' : ''; ?>>General Inquiry</option>
          <option value="other" <?php echo ($subject ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="message">Message *</label>
        <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>

        <button type="submit">Send Message</button>
      </form>
    </article>
  </section>
  <section>
    <h2>Operating Hours</h2>
    <ul>
      <li><strong>Monday - Thursday:</strong> 11:00 AM - 10:00 PM</li>
      <li><strong>Friday - Saturday:</strong> 11:00 AM - 11:00 PM</li>
      <li><strong>Sunday:</strong> 12:00 PM - 9:00 PM</li>
      <li><strong>Holidays:</strong> Special Hours</li>
    </ul>
  </section>

  <section>
    <h2>Find Us</h2>
    <p>Map would go here (Google Maps or other service)</p>
    <p><a href="https://maps.google.com?q=123+Culinary+Street" target="_blank">View on Google Maps</a></p>
  </section>

  <section>
    <h2>Frequently Asked Questions</h2>
    <details>
      <summary>🍽️ Do you take reservations?</summary>
      <p>Yes, we accept reservations for parties of 2 or more...</p>
    </details>
    <details>
      <summary>🥗 Do you offer vegetarian/vegan options?</summary>
      <p>Yes, we offer many vegetarian and vegan options...</p>
    </details>
    <details>
      <summary>🚗 Is parking available?</summary>
      <p>Yes, we offer complimentary parking behind the restaurant.</p>
    </details>
    <details>
      <summary>🎉 Do you host private events?</summary>
      <p>Yes, we offer private dining for events and occasions.</p>
    </details>
  </section>
</main>

<footer>
  <div>
    <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    <p>Developed for COS107 - Web Programming and Application</p>
  </div>
</footer>

</body>
</html>
