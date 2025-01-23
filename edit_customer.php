<?php
include 'a.php';
session_start();

// Check if the user is logged in and is a manager
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: l.html');
    exit();
}

$manager_id = $_SESSION['id'];

// Fetch customer details to edit
if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];
    
    // Fetch customer details
    $customer_stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $customer_stmt->bind_param("i", $customer_id);
    $customer_stmt->execute();
    $customer_result = $customer_stmt->get_result();
    $customer = $customer_result->fetch_assoc();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Update customer details
    $update_stmt = $conn->prepare("UPDATE customers SET name = ?, email = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $name, $email, $customer_id);
    $update_stmt->execute();

    echo "<script>alert('Customer details updated successfully!'); window.location.href='manager_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
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
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .form-container {
            width: 40%;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
        }

        .form-container label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            width: 100%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #4cae4c;
        }

    </style>
</head>
<body>

<header>
    <h1>GX BANK</h1>
    <nav>
        <a href="manager_dashboard.php">Dashboard</a>
        <a href="edit_customer.php">Edit Customer</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="form-container">
    <h2>Edit Customer</h2>
    <form method="POST" action="edit_customer.php">
        <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">

        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo $customer['name']; ?>" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?php echo $customer['email']; ?>" required>

        <button type="submit">Update Customer</button>
    </form>
</div>

</body>
</html>
