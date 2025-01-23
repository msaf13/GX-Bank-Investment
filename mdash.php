<?php
session_start();
include 'a.php'; // Database connection

// Check if the manager is logged in
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'manager') {
    header('Location: l.html'); // Redirect to login if not logged in
    exit();
}

// Get the manager's ID from the session
$manager_id = $_SESSION['id'];

// Fetch investments for the manager
$sql = "SELECT investment_products.id, investment_products.name, investment_products.price 
        FROM investment_products 
        INNER JOIN manager_investments 
        ON investment_products.id = manager_investments.investment_id 
        WHERE manager_investments.manager_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$investments_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GX Bank - Manager Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #d6a4fc, #ffb6c1);
            margin: 0;
            padding: 0;
        }

        header {
            background-color: black;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: #d13fee;
        }

        header a {
            text-decoration: none;
            color: white;
            margin-left: 20px;
            font-weight: bold;
        }

        header a:hover {
            color: #d13fee;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            color: #d13fee;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #d13fee;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        button {
            background-color: #d13fee;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #b832d4;
        }

        a {
            text-decoration: none;
            color: #d13fee;
        }

        a:hover {
            text-decoration: underline;
        }

        .add-button {
            display: block;
            text-align: right;
            margin-top: 20px;
        }

    </style>
</head>
<body>
    <header>
        <h1>GX Bank - Manager Dashboard</h1>
        <a href="logout.php">Logout</a>
    </header>
    <div class="container">
        <h2>Your Investment Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Investment ID</th>
                    <th>Investment Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $investments_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>RM <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <a href="edit_investment.php?id=<?php echo $row['id']; ?>">
                                <button>Edit</button>
                            </a>
                            <a href="delete_fund.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this investment?');">
                                <button>Delete</button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="add-button">
            <a href="add_investment.php">
                <button>Add New Investment</button>
            </a>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
