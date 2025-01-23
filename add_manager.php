<?php
session_start();
include 'a.php';

if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: l.html');
    exit();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nric = $_POST['nric'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $usertype = 'manager';

    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ? OR nric = ? OR phone = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ssss", $username, $email, $nric, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "Username, email, NRIC, or phone number already exists!";
    } else {
        $insert_sql = "INSERT INTO users (username, email, nric, phone, password, usertype) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssss", $username, $email, $nric, $phone, $password, $usertype);

        if ($stmt->execute()) {
            echo "<script>alert('Manager added successfully!'); window.location.href='admin_dashboard.php';</script>";
            exit;
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        form { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        button { width: 100%; padding: 10px; background-color: #d13fee; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #b832d4; }
        .back-btn { width: 100%; padding: 10px; background-color: #888; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .back-btn:hover { background-color: #555; }
    </style>
</head>
<body>

<h2 style="text-align:center">Add Manager</h2>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="nric" placeholder="NRIC" required><br>
    <input type="text" name="phone" placeholder="Phone" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Add Manager</button>
</form>

<button class="back-btn" onclick="window.location.href='admin_dashboard.php';">Back to Dashboard</button>

<?php
if (!empty($errorMessage)) {
    echo "<script>alert('$errorMessage');</script>";
}
?>

</body>
</html>
