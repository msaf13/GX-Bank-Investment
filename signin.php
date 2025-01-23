<?php
session_start();
include('a.php'); // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the input (basic validation)
    if (empty($username) || empty($password)) {
        echo "<script>alert('Both fields are required!'); window.history.back();</script>";
        exit;
    }

    // Prepare the SQL query to check if the user exists
    $stmt = $conn->prepare("SELECT id, username, password, usertype FROM users WHERE username = ?");
    if (!$stmt) {
        // If the statement couldn't be prepared, print an error
        die("Error preparing query: " . $conn->error);
    }

    // Bind the parameters to the query
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch the user record
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];
        $usertype = $user['usertype'];

        // Check if the user is admin
        if ($usertype == 'admin') {
            // Admin uses plain text password
            if ($password == $stored_password) {
                // Set session variables for admin
                $_SESSION['username'] = $username;
                $_SESSION['usertype'] = $usertype;
                header("Location: admin_dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid username or password!'); window.history.back();</script>";
            }
        } else {
            // For other users (manager, customer), use password verification
            if (password_verify($password, $stored_password)) {
                // Set session variables
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['usertype'] = $usertype;

                // Redirect based on usertype
                if ($usertype == 'manager') {
                    header("Location: mdash.php");
                } elseif ($usertype == 'customer') {
                    header("Location: cdashboard.php");
                }
                exit;
            } else {
                echo "<script>alert('Invalid username or password!'); window.history.back();</script>";
            }
        }
    } else {
        // No user found
        echo "<script>alert('Invalid username or password!'); window.history.back();</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
