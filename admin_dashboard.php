<?php
session_start();

// Prevent caching of the page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: l.html"); // Redirect to login page if not logged in
    exit();
}

// Database connection (ensure `a.php` contains the database connection)
include 'a.php';

// Fetch all users except the admin
$users_result = $conn->query("SELECT * FROM users WHERE usertype != 'admin'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
        }

        header {
            background-color: #3a3a3a;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            color: #d13fee;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        nav a:hover {
            color: #d13fee;
        }

        .sidebar {
            width: 250px;
            background-color: #2b2b2b;
            color: white;
            padding: 15px;
            height: calc(100vh - 60px);
            position: fixed;
            top: 60px;
            left: 0;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #d13fee;
            color: white;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .admin-table th, .admin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .admin-table th {
            background-color: #3a3a3a;
            color: white;
        }

        a.action-link {
            text-decoration: none;
            color: #d13fee;
            font-weight: bold;
        }

        a.action-link:hover {
            color: #b832d4;
        }

        button {
            background-color: #d13fee;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        button:hover {
            background-color: #b832d4;
        }

        .highlight {
            background-color: #ffe6e6; /* Highlight non-deletable users (e.g., managers) */
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="add_customer.php">Add Customer</a>
        <a href="add_manager.php">Add Manager</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>All Users</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>NRIC</th>
                    <th>Phone Number</th>
                    <th>Usertype</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()) { ?>
                    <tr class="<?php echo ($user['usertype'] == 'manager') ? 'highlight' : ''; ?>">
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['nric']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['usertype']); ?></td>
                        <td>
                            <a class="action-link" href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                            <?php if ($user['usertype'] != 'manager') { ?>
                                | <a class="action-link" href="delete_user.php?id=<?php echo $user['id']; ?>"
                                     onclick="return confirm('Are you sure you want to delete this user?');">
                                     Delete
                                </a>
                            <?php } else { ?>
                              
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
