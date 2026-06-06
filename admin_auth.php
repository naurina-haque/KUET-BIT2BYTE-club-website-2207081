<?php
session_start();
require_once 'db.php';

/**
 * Admin Authentication Helper for BIT2BYTE Club Website
 * Single admin with fixed credentials
 */

/**
 * Authenticate admin login
 * Fixed credentials: email: admin@bit2byte.com, password: admin123
 */
function adminLogin($email, $password) {
    global $conn;
    
    $email = $conn->real_escape_string(trim($email));
    
    $stmt = $conn->prepare("SELECT id, email, password, name FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    if (!password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_logged_in'] = true;
    
    return ['success' => true, 'message' => 'Login successful.'];
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication
 */
function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: login.html');
        exit();
    }
}

/**
 * Logout admin
 */
function adminLogout() {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

/**
 * Get current admin info
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['admin_id'],
        'email' => $_SESSION['admin_email'],
        'name' => $_SESSION['admin_name']
    ];
}
?>