<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restaurant Management</title>
    <link rel="stylesheet" href="css/logins.css">
</head>
<body class="login-body">

<header class="main-header">
    <h1 class="site-title">Delicious <span>Restaurant</span></h1>
    <nav class="main-nav">
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="login.php" class="active-link">Login</a></li>
        </ul>
    </nav>
</header>

<main class="login-main">
    <section class="login-intro">
        <h2>Choose Login Type</h2>
        <p>Select how you'd like to access the system</p>
    </section>

    <section class="login-options">
        <div class="login-card" onclick="selectLoginType('user')">
            <p class="login-icon">👤</p>
            <h3>Customer Login</h3>
            <p>Browse menu, place orders, and track your order status</p>
            <button class="login-btn">Login as Customer</button>
        </div>

        <div class="login-card" onclick="selectLoginType('admin')">
            <p class="login-icon">👨‍💼</p>
            <h3>Admin Login</h3>
            <p>Manage menu, orders, users, and restaurant operations</p>
            <button class="login-btn">Login as Admin</button>
        </div>
    </section>

    <section class="demo-credentials">
        <h4>Demo Credentials</h4>
        <div class="credential-boxes">
            <div class="credential-card">
                <h5>Customer Account</h5>
                <p><strong>Username:</strong> john_doe<br><strong>Password:</strong> user123</p>
            </div>
            <div class="credential-card">
                <h5>Admin Account</h5>
                <p><strong>Username:</strong> admin<br><strong>Password:</strong> admin123</p>
            </div>
        </div>
    </section>

    <section class="role-info">
        <h4>What You Can Do</h4>
        <div class="roles-container">
            <div class="role-card">
                <h5>As a Customer:</h5>
                <ul>
                    <li>Browse restaurant menu</li>
                    <li>Search for specific dishes</li>
                    <li>View order history</li>
                    <li>Update profile information</li>
                </ul>
            </div>
            <div class="role-card">
                <h5>As an Admin:</h5>
                <ul>
                    <li>Manage menu items (CRUD)</li>
                    <li>View and manage orders</li>
                    <li>Register new users</li>
                    <li>System administration</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="register-prompt">
        <p>Don't have an account? <a href="register.php">Register Here</a></p>
        <p><a href="index.php">← Back to Home</a></p>
    </section>
</main>

<footer class="main-footer">
    <p>&copy; 2025 Delicious Restaurant. All rights reserved.</p>
    <p>Developed for COS107 - Web Programming and Application</p>
</footer>

<script>
    function selectLoginType(type) {
        const clickedOption = event.currentTarget;
        clickedOption.style.transform = 'scale(0.95)';
        setTimeout(() => {
            window.location.href = type === 'user' ? 'user_login.php' : 'admin_login.php';
        }, 150);
        setTimeout(() => {
            clickedOption.style.transform = 'translateY(-5px)';
        }, 150);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === '1' || e.key.toLowerCase() === 'u') selectLoginType('user');
        else if (e.key === '2' || e.key.toLowerCase() === 'a') selectLoginType('admin');
    });

    function showAutoDemo() {
        const demoCredentials = document.querySelector('.demo-credentials');
        let isHighlighted = false;
        setInterval(() => {
            demoCredentials.style.backgroundColor = isHighlighted ? '' : '#fdf8e1';
            isHighlighted = !isHighlighted;
        }, 3000);
    }

    setTimeout(showAutoDemo, 2000);

    function quickLogin(type) {
        sessionStorage.setItem('demo_username', type === 'user' ? 'john_doe' : 'admin');
        sessionStorage.setItem('demo_password', type === 'user' ? 'user123' : 'admin123');
        window.location.href = type === 'user' ? 'user_login.php' : 'admin_login.php';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const demoSection = document.querySelector('.demo-credentials');
        const quickDemoDiv = document.createElement('div');
        quickDemoDiv.classList.add('quick-demo');
        quickDemoDiv.innerHTML = `
            <p>Quick Demo Access:</p>
            <button class="demo-button" onclick="quickLogin('user')">Demo as Customer</button>
            <button class="demo-button" onclick="quickLogin('admin')">Demo as Admin</button>
        `;
        demoSection.appendChild(quickDemoDiv);
    });
</script>

</body>
</html>
