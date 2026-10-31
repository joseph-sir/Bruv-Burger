<?php
// src/admin_auth.php
require_once 'config.php';

// Check if the admin is NOT logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}
?>