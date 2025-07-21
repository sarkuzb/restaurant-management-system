<?php
/**
 * Authentication Functions
 * Restaurant Management System
 */

require_once 'database.php';

// Function to authenticate admin
function authenticateAdmin($username, $password) {
    $conn = getDBConnection();
    
    // Sanitize input
    $username = sanitizeInput($username);
    $password = md5($password); // Using MD5 for simplicity (in production, use password_hash)
    
    // Prepare and execute query
    $stmt = $conn->prepare("SELECT admin_id, username, email, full_name FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_email'] = $admin['email'];
        
        $stmt->close();
        closeDBConnection($conn);
        return true;
    }
    
    $stmt->close();
    closeDBConnection($conn);
    return false;
}

// Function to authenticate user
function authenticateUser($username, $password) {
    $conn = getDBConnection();
    
    // Sanitize input
    $username = sanitizeInput($username);
    $password = md5($password); // Using MD5 for simplicity
    
    // Prepare and execute query
    $stmt = $conn->prepare("SELECT user_id, username, email, full_name, phone, address FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_address'] = $user['address'];
        
        $stmt->close();
        closeDBConnection($conn);
        return true;
    }
    
    $stmt->close();
    closeDBConnection($conn);
    return false;
}

// Function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Function to logout admin
function logoutAdmin() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
}

// Function to logout user
function logoutUser() {
    unset($_SESSION['user_logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_username']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_phone']);
    unset($_SESSION['user_address']);
}

// Function to redirect if not admin
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header("Location: admin_login.php");
        exit();
    }
}

// Function to redirect if not user
function requireUser() {
    if (!isUserLoggedIn()) {
        header("Location: user_login.php");
        exit();
    }
}

// Function to register new user (for admin)
function registerUser($username, $password, $email, $full_name, $phone, $address) {
    $conn = getDBConnection();
    
    // Sanitize inputs
    $username = sanitizeInput($username);
    $email = sanitizeInput($email);
    $full_name = sanitizeInput($full_name);
    $phone = sanitizeInput($phone);
    $address = sanitizeInput($address);
    $password = md5($password);
    
    // Check if username already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        closeDBConnection($conn);
        return false; // User already exists
    }
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $password, $email, $full_name, $phone, $address);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $check_stmt->close();
    closeDBConnection($conn);
    
    return $result;
}

// Function to change admin password
function changeAdminPassword($admin_id, $old_password, $new_password) {
    $conn = getDBConnection();
    
    $old_password = md5($old_password);
    $new_password = md5($new_password);
    
    // Verify old password
    $verify_stmt = $conn->prepare("SELECT admin_id FROM admins WHERE admin_id = ? AND password = ?");
    $verify_stmt->bind_param("is", $admin_id, $old_password);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows == 0) {
        $verify_stmt->close();
        closeDBConnection($conn);
        return false; // Old password is incorrect
    }
    
    // Update password
    $update_stmt = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
    $update_stmt->bind_param("si", $new_password, $admin_id);
    $result = $update_stmt->execute();
    
    $verify_stmt->close();
    $update_stmt->close();
    closeDBConnection($conn);
    
    return $result;
}
?>