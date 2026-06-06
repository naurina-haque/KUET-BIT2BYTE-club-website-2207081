<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    // Try regular POST data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
} else {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
}

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter both email and password.']);
    exit;
}

// Sanitize email
$email = $conn->real_escape_string(trim($email));

// Check admin credentials
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

// Verify password
if (!password_verify($password, $admin['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    exit;
}

// Set session variables
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_name'] = $admin['name'];
$_SESSION['admin_logged_in'] = true;

// Return success
echo json_encode([
    'success' => true, 
    'message' => 'Login successful. Redirecting...',
    'redirect' => 'admin.html'
]);
?>