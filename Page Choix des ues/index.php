<?php
// Inclure le fichier de connexion
include 'config.php';

// Récupérer les données depuis la base de données
$query = $pdo->query("SELECT * FROM ue");
$ues = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Unités d'Enseignement</title>
    <link rel="stylesheet" href="style2.css"> 
</head>
<body>
    <div class="bandeau">
    </div>
    <div class="rectangle">
        <p1>Mes cours</p1>
        <p2>Vue d’ensemble des cours</p2>
        <hr class="separator">
        <div class="container">
    <?php foreach ($ues as $ue): ?>
        <div class="ue">
            <!-- Partie Image -->
            <div class="image-container">
                <img src="<?= htmlspecialchars($ue['image']) ?>" alt="<?= htmlspecialchars($ue['intitule']) ?>">
            </div>
            
            <!-- Partie Textuelle (code et intitulé) -->
            <div class="text-container">
                <h3><?= htmlspecialchars($ue['code']) ?></h3>
                <p><?= htmlspecialchars($ue['intitule']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>


        
    </div>


    

</body>
</html>
