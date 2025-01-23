<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];

    $conn = new mysqli('localhost', 'root', '', 'fund_manager');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO funds (name, type, amount) VALUES ('$name', '$type', '$amount')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Fund</title>
</head>
<body>
    <h1>Add New Fund</h1>
    <form method="POST">
        <label>Fund Name:</label>
        <input type="text" name="name" required><br>
        <label>Type:</label>
        <input type="text" name="type" required><br>
        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" required><br>
        <button type="submit">Add Fund</button>
    </form>
</body>
</html>
