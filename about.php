<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - Delicious Restaurant</title>
  <link rel="stylesheet" href="css/home.css" />
</head>
<body>

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

  <main>

    <section>
      <div>
        <h2>About Delicious Restaurant</h2>
        <p>Where Culinary Excellence Meets Exceptional Service</p>
      </div>
    </section>

    <section>
      <h2>Our Story</h2>
      <div>
        <article>
          <p>Founded in 2010, Delicious Restaurant began as a small family-owned establishment with a simple vision: 
          to create memorable dining experiences through exceptional food and warm hospitality.</p>
          <p>Our journey began when Chef Maria Gonzalez and her husband Antonio decided to share their passion for traditional cooking 
          with modern innovation.</p>
          <p>Today, we're proud to continue serving our community with the same dedication to excellence that has defined us from the beginning.</p>
        </article>
      </div>
    </section>

    <section>
      <h2>Our Mission & Values</h2>
      <div>
        <article>
          <h3>🍽️ Quality First</h3>
          <p>We source only the finest, freshest ingredients from local suppliers and trusted partners.</p>
        </article>
        <article>
          <h3>🌱 Sustainability</h3>
          <p>We believe in responsible dining. Our restaurant implements eco-friendly practices...</p>
        </article>
        <article>
          <h3>👨‍👩‍👧‍👦 Community</h3>
          <p>We're more than just a restaurant – we're a part of the community...</p>
        </article>
      </div>
    </section>

    <section>
      <h2>Meet Our Team</h2>
      <div>
        <article>
          <div>MG</div>
          <h4>Chef Maria Gonzalez</h4>
          <p>Head Chef & Co-Founder</p>
          <p>With over 20 years of culinary experience...</p>
        </article>
        <article>
          <div>AG</div>
          <h4>Antonio Gonzalez</h4>
          <p>General Manager & Co-Founder</p>
          <p>Antonio oversees daily operations and ensures every guest receives exceptional service.</p>
        </article>
        <article>
          <div>JD</div>
          <h4>James Rodriguez</h4>
          <p>Sous Chef</p>
          <p>James brings international flavors and modern techniques to our kitchen.</p>
        </article>
      </div>
    </section>

    <section>
      <h2>What Makes Us Special</h2>
      <div>
        <article>
          <div>🍴</div>
          <div>
            <h4>Farm-to-Table Freshness</h4>
            <p>We partner with local farms to bring you the freshest ingredients daily...</p>
          </div>
        </article>
        <article>
          <div>⏰</div>
          <div>
            <h4>Fast & Efficient Service</h4>
            <p>Our streamlined kitchen operations and experienced staff ensure quick service...</p>
          </div>
        </article>
        <article>
          <div>🏆</div>
          <div>
            <h4>Award-Winning Cuisine</h4>
            <p>Our culinary excellence has been recognized with multiple local awards...</p>
          </div>
        </article>
        <article>
          <div>💝</div>
          <div>
            <h4>Special Occasions</h4>
            <p>We specialize in making your celebrations memorable with custom menus...</p>
          </div>
        </article>
      </div>
    </section>

    <section>
      <h2>Awards & Recognition</h2>
      <div>
        <figure>
          <div>🏅</div>
          <figcaption>
            <h4>Best Local Restaurant 2023</h4>
            <p>City Food & Wine Magazine</p>
          </figcaption>
        </figure>
        <figure>
          <div>⭐</div>
          <figcaption>
            <h4>Excellence in Service 2022</h4>
            <p>Restaurant Association Award</p>
          </figcaption>
        </figure>
        <figure>
          <div>👨‍🍳</div>
          <figcaption>
            <h4>Chef's Choice Award 2021</h4>
            <p>Culinary Institute Recognition</p>
          </figcaption>
        </figure>
      </div>
    </section>

    <section>
      <div>
        <h3>Experience the Difference</h3>
        <p>Ready to taste what makes us special? Join us for an unforgettable dining experience.</p>
        <div>
          <a href="menu.php">View Our Menu</a>
          <a href="contact.php">Make a Reservation</a>
        </div>
      </div>
    </section>

  </main>

  <footer>
    <div>
      <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
      <p>Developed for COS107 - Web Programming and Application</p>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      document.querySelectorAll('section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        observer.observe(section);
      });
    });
  </script>

</body>
</html>
