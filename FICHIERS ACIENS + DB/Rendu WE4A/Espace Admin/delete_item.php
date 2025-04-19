<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "td1";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$id = $_POST['id'];

$sql = "DELETE FROM ues WHERE ID = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => 'Item deleted successfully']);
} else {
    echo json_encode(['error' => 'Error: ' . $sql . '<br>' . $conn->error]);
}

$conn->close();
?>