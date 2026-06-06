<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit;
}

// Get event ID
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($eventId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID.']);
    exit;
}

// First, delete associated image if exists
$image_result = $conn->query("SELECT image FROM events WHERE id = $eventId");
if ($image_result && $row = $image_result->fetch_assoc()) {
    if (!empty($row['image']) && file_exists($row['image'])) {
        unlink($row['image']);
    }
}

// Delete event
$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Event deleted successfully.']);
    } else {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Event not found.']);
    }
} else {
    $error = $stmt->error;
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to delete event: ' . $error]);
}
?>