<?php
$conn = new mysqli('localhost', 'root', '', 'fund_manager');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT customers.id, customers.name, customers.email, funds.name AS fund_name
        FROM customers
        LEFT JOIN funds ON customers.fund_id = funds.id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['fund_name']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No customers found</td></tr>";
}

$conn->close();
?>
