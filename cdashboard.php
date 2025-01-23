<?php
include 'a.php';
session_start();

// Check if the user is logged in and is a customer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'customer') {
    header("Location: l.html");
    exit;
}

$customer_id = $_SESSION['id'];

// Get wallet balance
$wallet_stmt = $conn->prepare("SELECT balance FROM wallet WHERE customer_id = ?");
$wallet_stmt->bind_param("i", $customer_id);
$wallet_stmt->execute();
$wallet_result = $wallet_stmt->get_result();
$wallet = $wallet_result->fetch_assoc();
$balance = $wallet ? $wallet['balance'] : 0;

// Get available products
$products_stmt = $conn->prepare("SELECT * FROM investment_products");
$products_stmt->execute();
$products_result = $products_stmt->get_result();

// Check if the form is submitted to buy the product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    // Fetch product price
    $product_stmt = $conn->prepare("SELECT price FROM investment_products WHERE id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product = $product_result->fetch_assoc();
    $price = $product['price'];

    // Calculate total price
    $total_price = $price * $quantity;

    // Fetch the manager_id for the product
    $manager_stmt = $conn->prepare("SELECT manager_id FROM manager_investments WHERE investment_id = ?");
    $manager_stmt->bind_param("i", $product_id);
    $manager_stmt->execute();
    $manager_result = $manager_stmt->get_result();
    $manager = $manager_result->fetch_assoc();
    $manager_id = $manager['manager_id'];

    // Check wallet balance
    if ($balance >= $total_price) {
        // Deduct from wallet
        $new_balance = $balance - $total_price;
        $update_wallet_stmt = $conn->prepare("UPDATE wallet SET balance = ? WHERE customer_id = ?");
        $update_wallet_stmt->bind_param("di", $new_balance, $customer_id);
        $update_wallet_stmt->execute();

        // Insert transaction history (no quantity included)
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
    <title>Customer Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #d6a4fc, #ffb6c1);
            margin: 0;
            padding: 0;
        }

        header {
            background-color: black;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            color: #d13fee;
            font-size: 2rem;
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

        .container {
            display: flex;
            justify-content: space-around;
            padding: 30px 0;
            margin-top: 30px;
        }

        .box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 30%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .box h2 {
            font-size: 1.5rem;
            color: #333;
        }

        .box p {
            font-size: 1.25rem;
            color: #333;
        }

        form {
            margin-top: 15px;
        }

        form label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        button {
            background-color: #d13fee;
            color: white;
            padding: 12px;
            width: 100%;
            text-align: center;
            border-radius: 5px;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #b832d4;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <h1>GX BANK</h1>
        <nav>
           
            <a href="topup.php">Top-up Wallet</a>
            <a href="profile.php">Profile</a>
            <a href="history.php">Transaction History</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Main Dashboard Container -->
    <div class="container">
        <!-- Wallet Balance Box -->
        <div class="box">
            <h2>Wallet Balance</h2>
            <p><strong>$<?php echo number_format($balance, 2); ?></strong></p>
            <a href="topup.php" class="button">Top-up Wallet</a>
        </div>

        <!-- Available Products Box -->
        <div class="box">
            <h2>Available Products</h2>
            <form action="cdashboard.php" method="POST">
                <label for="product_id">Select a product to buy:</label>
                <select name="product_id" id="product_id" required>
                    <?php while ($product = $products_result->fetch_assoc()) { ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?> - $<?php echo $product['price']; ?></option>
                    <?php } ?>
                </select>
                <label for="quantity">Enter quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" value="1" required>
                <button type="submit">Buy Now</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 GX Bank. All rights reserved.</p>
    </footer>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
