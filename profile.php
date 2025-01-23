<?php
include 'a.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'customer') {
    header("Location: l.html");
    exit;
}

$customer_id = $_SESSION['id'];

// Fetch customer profile data
$stmt = $conn->prepare("SELECT username, email, nric, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Fetch wallet balance
$wallet_stmt = $conn->prepare("SELECT balance FROM wallet WHERE customer_id = ?");
$wallet_stmt->bind_param("i", $customer_id);
$wallet_stmt->execute();
$wallet_result = $wallet_stmt->get_result();
$wallet = $wallet_result->fetch_assoc();
$balance = $wallet ? $wallet['balance'] : '0.00';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #d6a4fc, #ffb6c1);
            margin: 0;
            padding: 0;
            color: #333;
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
            margin: 0;
        }

        header nav a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            margin-left: 15px;
        }

        header nav a:hover {
            color: #d13fee;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .profile-header {
            background: #d13fee;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .profile-header h2 {
            margin: 0;
        }

        .profile-content {
            padding: 20px;
        }

        .profile-card {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .profile-card p {
            margin: 0;
        }

        .wallet-section {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border: 2px solid #d13fee;
            border-radius: 10px;
        }

        .wallet-section h3 {
            margin: 0 0 10px 0;
            color: #d13fee;
        }

        .wallet-section p {
            font-size: 1.5em;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container a {
            text-decoration: none;
            background-color: #d13fee;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .button-container a:hover {
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
            <a href="topup.php">Top-up Wallet</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Profile Container -->
    <div class="container">
        <div class="profile-header">
            <h2>Your Profile</h2>
        </div>
        <div class="profile-content">
            <div class="profile-card">
                <p><strong>Username:</strong></p>
                <p><?php echo htmlspecialchars($profile['username']); ?></p>
            </div>
            <div class="profile-card">
                <p><strong>Email:</strong></p>
                <p><?php echo htmlspecialchars($profile['email']); ?></p>
            </div>
            <div class="profile-card">
                <p><strong>NRIC:</strong></p>
                <p><?php echo htmlspecialchars($profile['nric']); ?></p>
            </div>
            <div class="profile-card">
                <p><strong>Phone:</strong></p>
                <p><?php echo htmlspecialchars($profile['phone']); ?></p>
            </div>

            <!-- Wallet Section -->
            <div class="wallet-section">
                <h3>Wallet Balance</h3>
                <p>RM <?php echo number_format($balance, 2); ?></p>
            </div>

            <!-- Action Buttons -->
            <div class="button-container">
                <a href="topup.php">Top-up Wallet</a>
            </div>
        </div>
    </div>
</body>
</html>
