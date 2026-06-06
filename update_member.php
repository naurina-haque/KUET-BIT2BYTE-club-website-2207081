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
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$designation = isset($_POST['designation']) ? trim($_POST['designation']) : 'member';

// Validate required fields
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid member ID.']);
    exit;
}

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

// Check if email already exists for another member
$checkStmt = $conn->prepare("SELECT id FROM members WHERE email = ? AND id != ?");
$checkStmt->bind_param("si", $email, $id);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    $checkStmt->close();
    echo json_encode(['success' => false, 'message' => 'Email already exists for another member.']);
    exit;
}
$checkStmt->close();

// Handle image upload (if new image is provided)
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)]);
        exit;
    }
    
    if ($fileSize > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB.']);
        exit;
    }
    
    // Delete old image if exists
    $oldImageResult = $conn->query("SELECT image FROM members WHERE id = $id");
    if ($oldImageResult && $row = $oldImageResult->fetch_assoc() && !empty($row['image']) && file_exists($row['image'])) {
        unlink($row['image']);
    }
    
    $newFileName = 'member_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $destFilePath = $uploadDir . $newFileName;
    
    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
        $imagePath = 'uploads/' . $newFileName;
    }
}

// Update member
if ($imagePath) {
    $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, designation = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $designation, $imagePath, $id);
} else {
    $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, designation = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $designation, $id);
}

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Member updated successfully.']);
} else {
    $error = $stmt->error;
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to update member: ' . $error]);
}
?>