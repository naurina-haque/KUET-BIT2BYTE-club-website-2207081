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
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : null;

// Validate required fields
if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Title is required.']);
    exit;
}

if (empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Date is required.']);
    exit;
}

// Convert datetime-local format (YYYY-MM-DDTHH:MM) to MySQL datetime format (YYYY-MM-DD HH:MM:SS)
$date = str_replace('T', ' ', $date);
// Append seconds if not present
if (strlen($date) === 16) {
    $date .= ':00';
}

// Sanitize input
$title = $conn->real_escape_string($title);
$description = $conn->real_escape_string($description);
$date = $conn->real_escape_string($date);
$location = $location ? $conn->real_escape_string($location) : null;

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
    $newFileName = 'event_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $destFilePath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
        $imagePath = 'uploads/' . $newFileName;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
        exit;
    }
}

// Insert event
$stmt = $conn->prepare("INSERT INTO events (title, description, date, location, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $title, $description, $date, $location, $imagePath);

if ($stmt->execute()) {
    $eventId = $stmt->insert_id;
    $stmt->close();
    echo json_encode([
        'success' => true,
        'message' => 'Event added successfully.',
        'event_id' => $eventId
    ]);
} else {
    $error = $stmt->error;
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to add event: ' . $error]);
}
?>