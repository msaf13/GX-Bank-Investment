<?php
session_start();
include 'a.php';

// Ensure the user is logged in and is a manager
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'manager') {
    header('Location: l.html');
    exit();
}

$manager_id = $_SESSION['id']; // Get the manager's ID from the session

// Fetch the investment products linked to the manager
$investments_sql = "
    SELECT ip.id, ip.name, ip.price 
    FROM investment_products ip
    JOIN manager_investments mi ON ip.id = mi.investment_id
    WHERE mi.manager_id = ?";
$stmt = $conn->prepare($investments_sql);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Investments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        .investment-table { width: 80%; margin: 50px auto; border-collapse: collapse; }
        .investment-table th, .investment-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .investment-table th { background-color: #3a3a3a; color: white; }
        .back-btn { width: 100%; padding: 10px; background-color: #888; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .back-btn:hover { background-color: #555; }
    </style>
</head>
<body>

<h2 style="text-align:center">Your Investments</h2>

<table class="investment-table">
    <thead>
        <tr>
            <th>Investment Name</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($investment = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $investment['name']; ?></td>
                <td><?php echo $investment['price']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<button class="back-btn" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>

</body>
</html>
