<?php
session_start();
session_destroy(); // Destroy the session

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page or another appropriate page
header("Location: l.html"); // Replace with your login page
exit();
?>
