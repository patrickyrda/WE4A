<?php
require 'config.php';

// Valider et recuperer id_uv depuis l'URL
if (!isset($_GET['id_uv']) || empty($_GET['id_uv'])) {
    die("Erreur : L'ID de l'UV est manquant ou invalide.");
}
$id_uv = intval($_GET['id_uv']); // Recuperer l'ID de l'UV depuis l'URL

// Verifier si l'UV existe
$stmt = $conn->prepare("SELECT id_uv FROM uv WHERE id_uv = ?");
$stmt->execute([$id_uv]);
if ($stmt->rowCount() === 0) {
    die("Erreur : L'UV specifiee n'existe pas.");
}

$id_posts = isset($_GET['id']) ? intval($_GET['id']) : null; // Verifier si on modifie un post
$post_data = null;

// Si modification, recuperer les donnees du post existant
if ($id_posts) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id_posts = ?");
    $stmt->execute([$id_posts]);
    $post_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post_data) {
        die("Erreur : Le post demande n'existe pas.");
    }
}

// Gerer la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type_post = $_POST['type_post']; // Recuperer le type de post (message ou fichier)
    $id_utilisateur = 1; // Remplacer par l'ID reel de l'utilisateur connecte
    $corps = $type_post === 'message' ? $_POST['corps'] : null;
    $nom_fichier = null;

    // Verifier si un fichier a ete uploade
    if ($type_post === 'fichier' && !empty($_FILES['fichier']['name'])) {
        $nom_fichier = basename($_FILES['fichier']['name']);
        $chemin_fichier = "uploads/" . $nom_fichier;
        move_uploaded_file($_FILES['fichier']['tmp_name'], $chemin_fichier);
    }

    if ($id_posts) {
        // Supprimer les anciens messages du feed lies a ce post
        $stmt = $conn->prepare("DELETE FROM feed WHERE id_posts = ?");
        $stmt->execute([$id_posts]);

        // Mettre a jour le post existant
        $stmt = $conn->prepare("UPDATE posts SET corps = ?, nom_fichier = ? WHERE id_posts = ?");
        $stmt->execute([$corps, $nom_fichier, $id_posts]);

        // Ajouter un nouveau message dans le feed
        $stmt = $conn->prepare("INSERT INTO feed (id_utilisateur, type, id_posts) VALUES (?, ?, ?)");
        $stmt->execute([$id_utilisateur, $type_post === 'message' ? 'new_message' : 'new_fichier', $id_posts]);
    } else {
        // Ajouter un nouveau post
        $stmt = $conn->prepare("INSERT INTO posts (id_utilisateur, id_uv, corps, nom_fichier, date_creation) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$id_utilisateur, $id_uv, $corps, $nom_fichier]);

        $new_post_id = $conn->lastInsertId();

        // Ajouter au feed
        $stmt = $conn->prepare("INSERT INTO feed (id_utilisateur, type, id_posts) VALUES (?, ?, ?)");
        $stmt->execute([$id_utilisateur, $type_post === 'message' ? 'new_message' : 'new_fichier', $new_post_id]);
    }

    // Rediriger vers la page UV
    header("Location: uv.php?id=" . $id_uv);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $id_posts ? "Modifier le post" : "Ajouter un nouveau post"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messageForm = document.getElementById('message-form');
            const fileForm = document.getElementById('file-form');
            const messageButton = document.getElementById('message-button');
            const fileButton = document.getElementById('file-button');

            // Afficher le formulaire de message et cacher celui de fichier
            messageButton.addEventListener('click', function () {
                messageForm.style.display = 'block';
                fileForm.style.display = 'none';
            });

            // Afficher le formulaire de fichier et cacher celui de message
            fileButton.addEventListener('click', function () {
                fileForm.style.display = 'block';
                messageForm.style.display = 'none';
            });
        });
    </script>
</head>
<body>
    <div class="container mt-4">
        <h1><?php echo $id_posts ? "Modifier le post" : "Ajouter un nouveau post"; ?></h1>
        <div class="d-flex justify-content-center mb-4">
            <button id="message-button" class="btn btn-primary me-2">Message Texte</button>
            <button id="file-button" class="btn btn-secondary">Partage Fichiers</button>
        </div>

        <!-- Formulaire pour le message texte -->
        <form id="message-form" method="POST" style="display: none;">
            <input type="hidden" name="type_post" value="message">
            <div class="mb-3">
                <label for="corps" class="form-label">Contenu :</label>
                <textarea class="form-control" id="corps" name="corps" rows="4"><?php echo htmlspecialchars($post_data['corps'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $id_posts ? "Modifier" : "Ajouter"; ?></button>
            <a href="uv.php?id=<?php echo $id_uv; ?>" class="btn btn-secondary">Annuler</a>
        </form>

        <!-- Formulaire pour le partage de fichiers -->
        <form id="file-form" method="POST" enctype="multipart/form-data" style="display: none;">
            <input type="hidden" name="type_post" value="fichier">
            <div class="mb-3">
                <label for="fichier" class="form-label">Ajouter un fichier :</label>
                <input type="file" class="form-control" id="fichier" name="fichier">
                <?php if (!empty($post_data['nom_fichier'])): ?>
                    <p class="mt-2">Fichier actuel : <a href="uploads/<?php echo htmlspecialchars($post_data['nom_fichier']); ?>" download><?php echo htmlspecialchars($post_data['nom_fichier']); ?></a></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $id_posts ? "Modifier" : "Ajouter"; ?></button>
            <a href="uv.php?id=<?php echo $id_uv; ?>" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
