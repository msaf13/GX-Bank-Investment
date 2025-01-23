<?php
session_start();
include 'a.php'; // Database connection

// Check if the manager is logged in
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'manager') {
    header('Location: l.html'); // Redirect to login if not logged in
    exit();
}

// Get the investment ID from the URL
$investment_id = $_GET['id'];

// Get the manager's ID from the session
$manager_id = $_SESSION['id'];

// First, check if the investment exists and is linked to the logged-in manager
$sql = "SELECT id FROM investment_products WHERE id = ? AND id IN (SELECT investment_id FROM manager_investments WHERE manager_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $investment_id, $manager_id);
$stmt->execute();
$stmt->store_result();

// If the investment exists for the manager
if ($stmt->num_rows > 0) {
    // Delete from the manager_investments table to remove the relationship
    $delete_stmt = $conn->prepare("DELETE FROM manager_investments WHERE investment_id = ? AND manager_id = ?");
    $delete_stmt->bind_param("ii", $investment_id, $manager_id);
    $delete_stmt->execute();

    // Delete the investment from the investment_products table
    $delete_investment_stmt = $conn->prepare("DELETE FROM investment_products WHERE id = ?");
    $delete_investment_stmt->bind_param("i", $investment_id);
    $delete_investment_stmt->execute();

    // Redirect with success message
    header('Location: mdash.php');
    exit();
} else {
    // If investment is not found or not owned by the manager, redirect with error message
    header('Location: mdashboard.php?message=Investment not found or not owned by you');
    exit();
}

// Close database connection
$conn->close();
?>
