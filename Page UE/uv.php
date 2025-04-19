<?php
require 'config.php';
include("./header.php");

function get_ue_id($id_posts) {
    global $conn;
    
    // Recuperer les details d'un post specifique
    $stmt = $conn->prepare("SELECT p.*, u.nom, u.prenom FROM posts p JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur WHERE p.id_posts = ?");
    $stmt->execute([$id_posts]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_posts = $_POST['id_posts'];
    $nouveau_contenu = $_POST['nouveau_contenu'];
    $id_utilisateur = 1; // Remplacer par l'ID reel de l'utilisateur connecte
    
    // Verifier si un fichier a ete uploade
    if (!empty($_FILES['fichier']['name'])) {
        $nom_fichier = basename($_FILES['fichier']['name']);
        $chemin_fichier = "uploads/" . $nom_fichier;
        move_uploaded_file($_FILES['fichier']['tmp_name'], $chemin_fichier);

        // Mettre a jour le post avec un fichier
        $stmt = $conn->prepare("UPDATE posts SET nom_fichier = ? WHERE id_posts = ?");
        $stmt->execute([$nom_fichier, $id_posts]);

        // Ajouter une entree dans le feed
        $stmt = $conn->prepare("INSERT INTO feed (id_utilisateur, type, id_posts) VALUES (?, 'new_fichier', ?)");
        $stmt->execute([$id_utilisateur, $id_posts]);
    } else {
        // Mettre a jour le contenu du post
        $stmt = $conn->prepare("UPDATE posts SET corps = ? WHERE id_posts = ?");
        $stmt->execute([$nouveau_contenu, $id_posts]);

        // Ajouter une entree dans le feed
        $stmt = $conn->prepare("INSERT INTO feed (id_utilisateur, type, id_posts) VALUES (?, 'new_message', ?)");
        $stmt->execute([$id_utilisateur, $id_posts]);
    }
}

$id_uv = $_GET['id']; // Recuperer l'ID de l'UV depuis l'URL

// Recuperer les details de l'UV
$stmt = $conn->prepare("SELECT * FROM uv WHERE id_uv = ?");
$stmt->execute([$id_uv]);
$uv_details = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$uv_details) {
    die("Une erreur est survenue.");
}

// Recuperer tous les posts pour l'UV specifiee, tries par date de creation (du plus recent au plus ancien)
$stmt = $conn->prepare("SELECT p.*, u.nom, u.prenom 
                        FROM posts p 
                        JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur 
                        WHERE p.id_uv = ? 
                        ORDER BY p.date_creation DESC, p.id_posts DESC");
$stmt->execute([$id_uv]);
$all_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($uv_details['nom_uv']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="feed.php" class="btn btn-secondary">← Retour</a>
            <a href="add_post.php?id_uv=<?php echo $id_uv; ?>" class="btn btn-success">Ajouter un nouveau post</a>
        </div>

        <h1><?php echo htmlspecialchars($uv_details['nom_uv']); ?></h1>
        <p><?php echo htmlspecialchars($uv_details['description']); ?></p>

        <div class="row">
            <?php foreach ($all_posts as $post): ?>
                <div class="col-12 mb-3" id="post-<?php echo $post['id_posts']; ?>">
                    <div class="card p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2><?php echo htmlspecialchars($post['corps']); ?></h2>
                            <div>
                                <a href="add_post.php?id=<?php echo $post['id_posts']; ?>&id_uv=<?php echo $id_uv; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <button class="btn btn-danger btn-sm delete-post-btn" data-id="<?php echo $post['id_posts']; ?>">Supprimer</button>
                            </div>
                        </div>
                        <?php if (!empty($post['nom_fichier'])): ?>
                            <p><a href="uploads/<?php echo htmlspecialchars($post['nom_fichier']); ?>" download><?php echo htmlspecialchars($post['nom_fichier']); ?></a></p>
                        <?php endif; ?>
                        <p class="text-muted">Par <?php echo htmlspecialchars($post['prenom']) . ' ' . htmlspecialchars(strtoupper($post['nom'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-post-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const postId = this.getAttribute('data-id');
                    if (confirm('Etes-vous sur de vouloir supprimer ce post ?')) {
                        fetch('delete_post.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({ id_posts: postId })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur reseau');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload(); // Recharger la page
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue.');
                        });
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
