<?php
$conn = new mysqli('localhost', 'root', '', 'fund_manager');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM funds";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['type']}</td>
            <td>{$row['amount']}</td>
            <td>
                <a href='update_fund.php?id={$row['id']}'>Edit</a> |
                <a href='delete_fund.php?id={$row['id']}'>Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No funds found</td></tr>";
}

$conn->close();
?>
