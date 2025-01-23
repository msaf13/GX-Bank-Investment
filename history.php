<?php
include 'a.php';
session_start();

// Check if the user is logged in as a customer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'customer') {
    header("Location: l.html");
    exit;
}

$customer_id = $_SESSION['id'];

// Fetch transaction history with LEFT JOIN to include unmatched products
$history_stmt = $conn->prepare("
    SELECT 
        transaction_history.transaction_date, 
        COALESCE(investment_products.name, 'Unknown Product') AS product_name, 
        transaction_history.amount, 
        transaction_history.type
    FROM 
        transaction_history
    LEFT JOIN 
        investment_products ON transaction_history.product_id = investment_products.id
    WHERE 
        transaction_history.customer_id = ?
    ORDER BY 
        transaction_history.transaction_date DESC
");
$history_stmt->bind_param("i", $customer_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
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
            font-weight: bold;
            margin-left: 10px;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #d13fee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
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
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>GX Bank</h1>
        <nav>
            <a href="cdashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Container -->
    <div class="container">
        <h2>Transaction History</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($history_result->num_rows > 0) {
                    while ($row = $history_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>RM <?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                        </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No transactions found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
