<?php
// Variables de connexion à la base de données
$servername = "localhost"; // Serveur (dans XAMPP c'est localhost)
$username = "root"; // Utilisateur par défaut dans XAMPP
$password = ""; // Mot de passe par défaut dans XAMPP est vide
$dbname = "td1"; // Nom de la base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
} 
?>
