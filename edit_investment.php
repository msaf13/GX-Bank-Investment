<?php
session_start();
include 'a.php'; // Database connection

if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'manager') {
    header('Location: l.html');
    exit();
}

if (isset($_GET['id'])) {
    $investment_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM investment_products WHERE id = ?");
    $stmt->bind_param("i", $investment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $investment = $result->fetch_assoc();
    } else {
        die("Investment not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE investment_products SET name = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sdi", $name, $price, $investment_id);

    if ($stmt->execute()) {
        header("Location: mdash.php");
    } else {
        echo "Error updating investment.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GX Bank - Edit Investment</title>
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
            max-width: 600px;
            margin: 50px auto;
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            width: 90%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #d13fee;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        button:hover {
            background-color: #b832d4;
        }

        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
            color: #d13fee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>GX Bank - Edit Investment</h1>
        <a href="mdash.php">Back to Dashboard</a>
    </header>
    <div class="container">
        <h2>Edit Investment</h2>
        <form action="" method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($investment['name']); ?>" placeholder="Investment Name" required>
            <input type="number" name="price" value="<?php echo htmlspecialchars($investment['price']); ?>" placeholder="Price (RM)" step="0.01" required>
            <button type="submit">Save Changes</button>
        </form>
        <div class="back-button">
            <a href="mdash.php">Go Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
