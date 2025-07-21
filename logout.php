<?php
/**
 * logout.php
 * Logout System - Restaurant Management System
 */

// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page (adjust the path if needed)
header("Location: login.php");
exit();
?>
