<?php
session_start();
include 'a.php'; // Database connection

if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'manager') {
    header('Location: l.html'); // Redirect to login if not logged in
    exit();
}

$message = ''; // To store success or error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $manager_id = $_SESSION['id'];

    // Check if the investment name already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM investment_products WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();  // Execute query to check if the name exists
    $stmt->bind_result($count);  // Bind the result of the count query
    $stmt->fetch();  // Fetch the result
    $stmt->close();  // Close the statement after fetching the result

    if ($count > 0) {
        // Investment name already exists
        $message = "Error: Investment with this name already exists.";
    } else {
        // Insert into investment_products if name is unique
        $stmt = $conn->prepare("INSERT INTO investment_products (name, price) VALUES (?, ?)");
        $stmt->bind_param("sd", $name, $price);
        if ($stmt->execute()) {
            $investment_id = $conn->insert_id;

            // Link the manager to the investment
            $stmt2 = $conn->prepare("INSERT INTO manager_investments (manager_id, investment_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $manager_id, $investment_id);
            $stmt2->execute();
            $stmt2->close(); // Close the second statement after execution

            // Success message and redirect
            echo "<script>
                    alert('Investment added successfully!');
                    window.location.href = 'mdash.php'; // Redirect to dashboard after popup
                  </script>";
            exit(); // Stop further script execution after the redirect
        } else {
            $message = "Error adding investment.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GX Bank - Add Investment</title>
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

.error-message {
    color: red;
    text-align: center;
    margin-bottom: 15px;
    font-weight: bold;
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
        <h1>GX Bank - Add Investment</h1>
        <a href="mdash.php">Back to Dashboard</a>
    </header>
    <div class="container">
        <h2>Add New Investment</h2>
        
        <!-- Display Error Message -->
        <?php if ($message): ?>
            <div class="error-message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="add_investment.php" method="POST">
            <input type="text" name="name" placeholder="Investment Name" required>
            <input type="number" name="price" placeholder="Price (RM)" step="0.01" required>
            <button type="submit">Add Investment</button>
        </form>
        <div class="back-button">
            <a href="mdash.php">Go Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
