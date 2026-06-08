<?php
session_start();
require_once 'db.php';

// Check for remember me cookie if session not set
if (!isset($_SESSION['admin_logged_in']) && isset($_COOKIE['remember_admin'])) {
    $cookie_data = base64_decode($_COOKIE['remember_admin']);
    $parts = explode(':', $cookie_data);
    if (count($parts) === 2) {
        $admin_id = $parts[0];
        $password_hash = $parts[1];
        
        $stmt = $conn->prepare("SELECT id, email, password, name FROM admin WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (hash('sha256', $admin['password']) === $password_hash) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_logged_in'] = true;
                // Redirect if coming from a GET request (cookie valid, user already logged in)
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    header('Location: admin.php');
                    exit;
                }
            }
        }
        $stmt->close();
    }
}

// Redirect to admin if already logged in via session
if (isset($_SESSION['admin_logged_in']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    } else {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
    }
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both email and password.']);
        exit;
    }
    
    $email = $conn->real_escape_string(trim($email));
    
    $stmt = $conn->prepare("SELECT id, email, password, name FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    if (!password_verify($password, $admin['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_logged_in'] = true;
    
    if (!empty($data['remember'])) {
        $cookie_value = base64_encode($admin['id'] . ':' . hash('sha256', $admin['password']));
        setcookie('remember_admin', $cookie_value, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Login successful. Redirecting...',
        'redirect' => 'admin.php'
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BIT2BYTE</title>
    <link rel="stylesheet" href="login.css">
    <script src="login.js"></script>
</head>
<body class="login-page">

  <div class="login-wrapper">
    <div class="modal">

      <div class="modal-logo"><span>BIT2BYTE</span></div>
      <h3>Login As Admin</h3>

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Enter your email" autocomplete="off" />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Enter your password" />
      </div>

      <div class="remember-me">
        <label class="checkbox-wrapper">
          <input type="checkbox" id="remember" />
          <span class="checkmark"></span>
          Remember Me
        </label>
      </div>

      <button class="modal-submit" onclick="handleLogin()">Login</button>

      <div class="success-msg" id="successMsg"></div>

      <div class="divider"><span>or</span></div>

      <div class="modal-footer-text">
        Go back to <a href="home.html">Home</a>
      </div>

    </div>
  </div>


</body>
</html>