<?php
session_start();
include 'a.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: l.html");
    exit();
}

// Check if the user ID is set in the URL
if (!isset($_GET['id'])) {
    die("Invalid request: No user ID provided.");
}

$id = intval($_GET['id']);

// Fetch user details
$result = $conn->query("SELECT * FROM users WHERE id = $id");
if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $nric = $conn->real_escape_string($_POST['nric']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $usertype = $conn->real_escape_string($_POST['usertype']);

    $update_query = "UPDATE users SET 
        username = '$username',
        email = '$email',
        nric = '$nric',
        phone = '$phone',
        usertype = '$usertype'
        WHERE id = $id";

    if ($conn->query($update_query)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        label {
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        button {
            background-color: #d13fee;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #b832d4;
        }

        .cancel-btn {
            background-color: #ccc;
            color: black;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>

    <h1>Edit User</h1>
    <form method="POST">
        <label for="username">Name:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="nric">NRIC:</label>
        <input type="text" name="nric" id="nric" value="<?php echo htmlspecialchars($user['nric']); ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

        <label for="usertype">Usertype:</label>
        <select name="usertype" id="usertype" required>
            <option value="customer" <?php echo $user['usertype'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
            <option value="manager" <?php echo $user['usertype'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
        </select>

        <button type="submit">Update User</button>
        <a href="admin_dashboard.php" class="cancel-btn">Cancel</a>
    </form>

</body>
</html>
