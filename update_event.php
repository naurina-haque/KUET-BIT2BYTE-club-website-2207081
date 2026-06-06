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
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : null;

// Validate required fields
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID.']);
    exit;
}

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
    $oldImageResult = $conn->query("SELECT image FROM events WHERE id = $id");
    if ($oldImageResult && $row = $oldImageResult->fetch_assoc() && !empty($row['image']) && file_exists($row['image'])) {
        unlink($row['image']);
    }
    
    $newFileName = 'event_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $destFilePath = $uploadDir . $newFileName;
    
    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
        $imagePath = 'uploads/' . $newFileName;
    }
}

// Update event
if ($imagePath) {
    $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, date = ?, location = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $description, $date, $location, $imagePath, $id);
} else {
    $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, date = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $date, $location, $id);
}

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Event updated successfully.']);
} else {
    $error = $stmt->error;
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to update event: ' . $error]);
}
?>