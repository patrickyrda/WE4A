<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "td1";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$surname = $_POST['surname'];
$email = $_POST['email'];

$sql = "INSERT INTO users (name, surname, email) VALUES ('$name', '$surname', '$email')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => 'Student inserted successfully']);
} else {
    echo json_encode(['error' => 'Error: ' . $sql . '<br>' . $conn->error]);
}

$conn->close();