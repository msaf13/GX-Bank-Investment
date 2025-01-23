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

// Delete related records from transaction_history first
$conn->begin_transaction();

try {
    $conn->query("DELETE FROM transaction_history WHERE customer_id = $id");
    $conn->query("DELETE FROM users WHERE id = $id");

    $conn->commit();
    header("Location: admin_dashboard.php");
    exit();
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    echo "Error deleting user and related records: " . $e->getMessage();
}
?>
