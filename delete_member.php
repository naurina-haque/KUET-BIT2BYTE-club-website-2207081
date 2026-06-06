<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit;
}

// Get member ID
$memberId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($memberId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid member ID.']);
    exit;
}

// Delete member
$stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
$stmt->bind_param("i", $memberId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Member deleted successfully.']);
    } else {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Member not found.']);
    }
} else {
    $error = $stmt->error;
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to delete member: ' . $error]);
}
?>