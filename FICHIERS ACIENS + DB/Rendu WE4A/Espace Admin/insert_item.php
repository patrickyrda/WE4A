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

$sql = "INSERT INTO ues (title, code) VALUES ('$title', '$code')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => 'Item inserted successfully']);
} else {
    echo json_encode(['error' => 'Error: ' . $sql . '<br>' . $conn->error]);
}

$conn->close();