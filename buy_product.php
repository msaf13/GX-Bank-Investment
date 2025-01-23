<?php
include 'a.php';
session_start();

// Check if the user is logged in as a customer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'customer') {
    header('Location: login.php'); // Redirect to login if not a customer
    exit();
}

// Get the customer ID from the session
$customer_id = $_SESSION['id'];

// Fetch all available products from the database
$products_result = $conn->query("SELECT id, name, price FROM investment_products");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product price
    $product_result = $conn->query("SELECT price FROM investment_products WHERE id = $product_id");
    $product = $product_result->fetch_assoc();
    $price = $product['price'];
    $total_price = $price * $quantity; // Total price for the selected quantity

    // Fetch the manager_id for the product
    $manager_stmt = $conn->prepare("SELECT manager_id FROM manager_investments WHERE investment_id = ?");
    $manager_stmt->bind_param("i", $product_id);
    $manager_stmt->execute();
    $manager_result = $manager_stmt->get_result();
    $manager = $manager_result->fetch_assoc();
    $manager_id = $manager['manager_id'];

    // Check wallet balance
    $wallet_stmt = $conn->prepare("SELECT balance FROM wallet WHERE customer_id = ?");
    $wallet_stmt->bind_param("i", $customer_id);
    $wallet_stmt->execute();
    $wallet = $wallet_stmt->get_result()->fetch_assoc();

    if ($wallet['balance'] >= $total_price) {
        // Deduct from wallet
        $new_balance = $wallet['balance'] - $total_price;
        $update_wallet_stmt = $conn->prepare("UPDATE wallet SET balance = ? WHERE customer_id = ?");
        $update_wallet_stmt->bind_param("di", $new_balance, $customer_id);
        $update_wallet_stmt->execute();

        // Insert transaction history (with manager_id)
        $insert_transaction_stmt = $conn->prepare("
            INSERT INTO transaction_history (customer_id, product_id, manager_id, amount, type) 
            VALUES (?, ?, ?, ?, 'purchase')
        ");
        $insert_transaction_stmt->bind_param("iiid", $customer_id, $product_id, $manager_id, $total_price);
        $insert_transaction_stmt->execute();

        echo "<script>alert('Product purchased successfully!'); window.location.href='cdashboard.php';</script>";
    } else {
        echo "<script>alert('Insufficient balance!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Product</title>
    <style>
        /* Add your custom styles here */
        .form-wrapper {
            margin: 50px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-wrapper label {
            font-weight: bold;
            margin-right: 10px;
        }

        .form-wrapper select, .form-wrapper input {
            padding: 10px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-wrapper button {
            background-color: #d13fee;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .form-wrapper button:hover {
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

    <!-- Buy Product Form -->
    <div class="form-wrapper">
        <h2>Buy Investment Product</h2>
        <form action="buy_product.php" method="POST">
            <label for="product_id">Select Product:</label>
            <select name="product_id" id="product_id" required>
                <?php 
                // Check if products are available
                if ($products_result->num_rows > 0) {
                    while ($product = $products_result->fetch_assoc()) {
                        echo "<option value='" . $product['id'] . "'>" . $product['name'] . " - RM " . number_format($product['price'], 2) . "</option>";
                    }
                } else {
                    echo "<option value=''>No products available</option>";
                }
                ?>
            </select>

            <!-- Quantity Input -->
            <label for="quantity">Enter Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" required>

            <button type="submit">Buy</button>
        </form>
    </div>
</body>
</html>
