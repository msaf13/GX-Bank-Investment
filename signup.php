<?php
include("a.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nric = $_POST['nric'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $usertype = 'customer';

    if (empty($username) || empty($email) || empty($nric) || empty($phone) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }

    $currentYear = date("Y");
    $birthYear = substr($nric, 0, 2);
    $birthYear = ($birthYear > intval(date("y"))) ? '19' . $birthYear : '20' . $birthYear;
    $age = $currentYear - intval($birthYear);

    if ($age < 18) {
        echo "<script>alert('Sorry, you must be at least 18 years old to create an account. Please try again.'); window.history.back();</script>";
        exit;
    }

   

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR nric = ? OR phone = ?");
    $stmt->bind_param("ssss", $username, $email, $nric, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username, email, NRIC, or phone already exists!'); window.history.back();</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, nric, phone, password, usertype) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $nric, $phone, $hashed_password, $usertype);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location.href = '1.html';</script>";
    } else {
        echo "<script>alert('Error creating account! Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
