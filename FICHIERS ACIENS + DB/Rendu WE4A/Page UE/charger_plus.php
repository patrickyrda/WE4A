<?php
require 'config.php';

function get_post_title($id_posts) {
    global $conn;
    $stmt = $conn->prepare("SELECT uv.nom_uv FROM posts p JOIN uv ON p.id_uv = uv.id_uv WHERE p.id_posts = ?");
    $stmt->execute([$id_posts]);
    $post = $stmt->fetch();
    
    return $post ? $post['nom_uv'] : '[Post]';
}

if (isset($_GET['offset'])) {
    $offset = intval($_GET['offset']);
    
    $stmt = $conn->prepare("SELECT i.*, u.nom, u.prenom, p.id_uv 
                            FROM feed i 
                            JOIN Utilisateur u ON i.id_utilisateur = u.id_utilisateur 
                            LEFT JOIN posts p ON i.id_posts = p.id_posts 
                            WHERE i.type IN ('new_message', 'new_fichier') 
                            ORDER BY i.date_creation DESC 
                            LIMIT 10 OFFSET :offset");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $feed = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($feed as $activity) {
        echo '<a href="uv.php?id=' . $activity['id_uv'] . '" class="list-group-item list-group-item-action">';
        echo '<strong>' . htmlspecialchars($activity['prenom']) . ' ' . htmlspecialchars(strtoupper($activity['nom'])) . '</strong> ';
        if ($activity['type'] === 'new_message') {
            echo 'a posté un nouveau message dans ';
        } elseif ($activity['type'] === 'new_fichier') {
            echo 'a posté un nouveau fichier dans ';
        }
        echo '<span class="fw-bold">' . htmlspecialchars(get_post_title($activity['id_posts'])) . '</span>';
        echo '<small class="text-muted d-block">' . date('d/m/Y H:i', strtotime($activity['date_creation'])) . '</small>';
        echo '</a>';
    }
}
