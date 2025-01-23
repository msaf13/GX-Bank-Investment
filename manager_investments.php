<?php
session_start();
include 'a.php';

// Ensure the user is logged in and is a manager
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'manager') {
    header('Location: l.html');
    exit();
}

$manager_id = $_SESSION['id']; // Get the manager's ID from the session
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Insert the new investment product into the database
    $insert_sql = "INSERT INTO investment_products (name, price) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sd", $name, $price);

    if ($stmt->execute()) {
        // Link the investment to the manager
        $investment_id = $stmt->insert_id;
        $link_sql = "INSERT INTO manager_investments (manager_id, investment_id) VALUES (?, ?)";
        $link_stmt = $conn->prepare($link_sql);
        $link_stmt->bind_param("ii", $manager_id, $investment_id);

        if ($link_stmt->execute()) {
            echo "<script>alert('Investment Product added successfully!'); window.location.href='manager_investments.php';</script>";
            exit;
        } else {
            $errorMessage = "Error linking investment to manager.";
        }
    } else {
        $errorMessage = "Error adding investment product: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Investments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        form { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        input[type="text"], input[type="number"], input[type="submit"] { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        button { width: 100%; padding: 10px; background-color: #d13fee; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #b832d4; }
        .back-btn { width: 100%; padding: 10px; background-color: #888; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .back-btn:hover { background-color: #555; }
    </style>
</head>
<body>

<h2 style="text-align:center">Manage Investments</h2>

<form method="POST">
    <input type="text" name="name" placeholder="Investment Product Name" required><br>
    <input type="number" step="0.01" name="price" placeholder="Investment Price" required><br>
    <input type="submit" value="Add Investment Product">
</form>

<button class="back-btn" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>

<?php
if (!empty($errorMessage)) {
    echo "<script>alert('$errorMessage');</script>";
}
?>

</body>
</html>
