<?php
// Connexion à la base de données
$host = 'localhost'; // Adresse du serveur MySQL
$dbname = 'td1'; // Nom de la base de données
$username = 'root'; // Nom d'utilisateur MySQL (par défaut root)
$password = ''; // Mot de passe MySQL (vide par défaut sur XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
