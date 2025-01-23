<?php
include 'a.php'; // Include your database connection file
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'customer') {
    header("Location: l.html");
    exit;
}

$customer_id = $_SESSION['id'];

// Handle top-up request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = $_POST['amount'];

    // Ensure the amount is a positive number
    if ($amount <= 0) {
        echo "<script>alert('Please enter a valid amount.');</script>";
    } else {
        // Update wallet balance
        $wallet_stmt = $conn->prepare("SELECT balance FROM wallet WHERE customer_id = ?");
        $wallet_stmt->bind_param("i", $customer_id);
        $wallet_stmt->execute();
        $wallet = $wallet_stmt->get_result()->fetch_assoc();
        $new_balance = $wallet ? $wallet['balance'] + $amount : $amount;

        if ($wallet) {
            // Update the wallet balance
            $update_wallet_stmt = $conn->prepare("UPDATE wallet SET balance = ? WHERE customer_id = ?");
            $update_wallet_stmt->bind_param("di", $new_balance, $customer_id);
            $update_wallet_stmt->execute();
        } else {
            // Insert new wallet record if none exists
            $insert_wallet_stmt = $conn->prepare("INSERT INTO wallet (customer_id, balance) VALUES (?, ?)");
            $insert_wallet_stmt->bind_param("id", $customer_id, $amount);
            $insert_wallet_stmt->execute();
        }

        echo "<script>alert('Wallet topped up successfully!'); window.location.href='cdashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top-up Wallet</title>
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

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            cursor: pointer;
        }

        nav a:hover {
            color: #d13fee;
        }

        .form-wrapper {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 100px auto;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #d13fee;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #b832d4;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>GX BANK</h1>
        <nav>
            <a href="cdashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Top-up Wallet Form -->
    <div class="form-wrapper">
        <h2>Top-up Wallet</h2>
        <form action="topup.php" method="POST">
            <input type="number" name="amount" placeholder="Enter Amount" required>
            <button type="submit">Top-up</button>
        </form>
    </div>
</body>
</html>
