<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "td1";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'];
$code = $_POST['code'];
$id = $_POST['id'];

$sql = "UPDATE ues SET title = ?, code = ? WHERE ID = ?";

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['error' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param("ssi", $title, $code, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Item updated successfully']);
} else {
    echo json_encode(['error' => 'Error updating item: ' . $stmt->error]);
}

$stmt->close();
$conn->close();