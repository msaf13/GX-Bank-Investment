<?php
session_start();
include 'a.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: l.html");
    exit();
}

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request: No user ID provided.");
}

$id = intval($_GET['id']);

// Delete the user
$delete_query = "DELETE FROM users WHERE id = $id";

if ($conn->query($delete_query)) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "Error deleting user: " . $conn->error;
}
?>
