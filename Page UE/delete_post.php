<?php
require 'config.php';

header('Content-Type: application/json'); // Assurer une reponse JSON

// Activer l'affichage des erreurs pour le debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_posts'])) {
    $id_posts = intval($_POST['id_posts']);

    try {
        // Verifier si le post existe
        $stmt = $conn->prepare("SELECT id_posts FROM posts WHERE id_posts = ?");
        $stmt->execute([$id_posts]);
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Le post n\'existe pas.']);
            exit;
        }

        // Supprimer les lignes associees dans la table feed
        $stmt = $conn->prepare("DELETE FROM feed WHERE id_posts = ?");
        $stmt->execute([$id_posts]);

        // Supprimer le post de la base de donnees
        $stmt = $conn->prepare("DELETE FROM posts WHERE id_posts = ?");
        $stmt->execute([$id_posts]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Post supprime avec succes.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Echec de la suppression du post.']);
        }
    } catch (Exception $e) {
        error_log("Erreur lors de la suppression du post: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Une erreur est survenue.']);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Requete invalide.']);
    exit;
}
?>
