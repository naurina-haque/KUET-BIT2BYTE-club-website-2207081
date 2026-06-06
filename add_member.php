<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$designation = isset($_POST['designation']) ? trim($_POST['designation']) : 'member';

// Validate required fields
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required.']);
    exit;
}

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit;
}

// Sanitize input
$name = $conn->real_escape_string($name);
$email = $conn->real_escape_string($email);
$phone = $phone ? $conn->real_escape_string($phone) : null;
$designation = $conn->real_escape_string($designation);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// Check if email already exists
$checkStmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    $checkStmt->close();
    echo json_encode(['success' => false, 'message' => 'Email already exists.']);
    exit;
}
$checkStmt->close();

// Handle image upload
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileType = $_FILES['image']['type'];
    
    // Get file extension
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
    // Validate file type
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)]);
        exit;
    }
    
    // Validate file size (max 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB.']);
        exit;
    }
    
    // Generate unique filename
    $newFileName = 'member_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $destFilePath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
        $imagePath = 'uploads/' . $newFileName;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
        exit;
    }
}

// Insert member
$stmt = $conn->prepare("INSERT INTO members (name, email, phone, designation, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $designation, $imagePath);

if ($stmt->execute()) {
    $memberId = $stmt->insert_id;
    $stmt->close();
    echo json_encode([
        'success' => true,
        'message' => 'Member added successfully.',
        'member_id' => $memberId
    ]);
} else {
    $error = $stmt->error;
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to add member: ' . $error]);
}
?>