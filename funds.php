<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fund_manager";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle different actions (add, edit, delete, get)
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'get':
        // Fetch all funds
        $sql = "SELECT * FROM funds";
        $result = $conn->query($sql);
        $funds = [];
        while ($row = $result->fetch_assoc()) {
            $funds[] = $row;
        }
        echo json_encode($funds);
        break;

    case 'add':
        // Add a new fund
        $name = $_POST['name'];
        $type = $_POST['type'];
        $amount = $_POST['amount'];
        $sql = "INSERT INTO funds (name, type, amount) VALUES ('$name', '$type', '$amount')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        break;

    case 'edit':
        // Edit an existing fund
        $id = $_POST['id'];
        $name = $_POST['name'];
        $type = $_POST['type'];
        $amount = $_POST['amount'];
        $sql = "UPDATE funds SET name='$name', type='$type', amount='$amount' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        break;

    case 'delete':
        // Delete a fund
        $id = $_POST['id'];
        $sql = "DELETE FROM funds WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>
