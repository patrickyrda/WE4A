<?php
require 'config.php';

// Fonction pour récupérer les activités récentes avec UV
function feed($limit = 10, $offset = 0) {
    global $conn;
    $stmt = $conn->prepare("SELECT i.*, u.nom, u.prenom, uv.nom_uv, p.id_uv 
                            FROM feed i 
                            JOIN Utilisateur u ON i.id_utilisateur = u.id_utilisateur 
                            LEFT JOIN posts p ON i.id_posts = p.id_posts 
                            LEFT JOIN uv ON p.id_uv = uv.id_uv
                            WHERE i.type IN ('new_message', 'new_fichier') 
                            ORDER BY i.date_creation DESC 
                            LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// On récupère les 10 dernières activités
$feed = feed(10, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fil d'actualité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Fil d'actualité</h2>
        <div class="list-group" id="feed-container">
            <?php if (empty($feed)): ?>
                <p class="text-muted">Aucune activité récente</p>
            <?php else: ?>
                <?php foreach ($feed as $activite): ?>
                    <a href="uv.php?id=<?php echo $activite['id_uv']; ?>" class="list-group-item list-group-item-action">
                        <strong><?php echo htmlspecialchars($activite['prenom']) . ' ' . htmlspecialchars(strtoupper($activite['nom'])); ?></strong>
                        <?php if ($activite['type'] === 'new_message'): ?>
                            a posté un nouveau message dans
                        <?php elseif ($activite['type'] === 'new_fichier'): ?>
                            a posté un nouveau fichier dans
                        <?php endif; ?>
                        <span class="fw-bold"> <?php echo htmlspecialchars($activite['nom_uv']); ?></span>
                        <small class="text-muted d-block"> <?php echo date('d/m/Y H:i', strtotime($activite['date_creation'])); ?></small>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($feed)): ?>
            <button id="load-more" class="btn btn-primary mt-3">Charger plus</button>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            let offset = 10;
            $("#load-more").click(function() {
                $.ajax({
                    url: "charger_plus.php",
                    type: "GET",
                    data: { offset: offset },
                    success: function(data) {
                        if (data.trim() !== "") {
                            $("#feed-container").append(data);
                            offset += 10;
                        } else {
                            $("#load-more").hide();
                        }
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>