<?php
// Inclure le fichier de connexion
include('config.php');

// Requête SQL pour récupérer les étudiants
$sql_etudiants = "SELECT id, nom_complet, email, 'Etudiant' AS role FROM etudiants";
$result_etudiants = $conn->query($sql_etudiants);

// Vérifier si la requête a réussi
if ($result_etudiants === false) {
    die("Erreur de requête SQL : " . $conn->error);
}

// Requête SQL pour récupérer les professeurs
$sql_professeurs = "SELECT id, nom_complet, email, 'Professeur' AS role FROM professeurs";
$result_professeurs = $conn->query($sql_professeurs);

// Vérifier si la requête a réussi
if ($result_professeurs === false) {
    die("Erreur de requête SQL : " . $conn->error);
}

// Combiner les résultats dans un seul tableau
$combined_results = [];
if ($result_etudiants->num_rows > 0) {
    while ($row = $result_etudiants->fetch_assoc()) {
        $combined_results[] = $row;
    }
}
if ($result_professeurs->num_rows > 0) {
    while ($row = $result_professeurs->fetch_assoc()) {
        $combined_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Étudiants et Professeurs</title>
    <link rel="stylesheet" href="style3.css">  <!-- Lien vers le CSS -->
    <script>
        let sortOrder = 1; // 1 pour croissant, -1 pour décroissant
        let sortColumn = 'nom_complet'; // Colonne par défaut

        // Fonction pour trier la table
        function sortTable() {
    const table = document.getElementById("userTable");
    let rows = Array.from(table.rows).slice(1); // Exclure l'en-tête
    let sortedRows;

    // Tri des lignes selon la colonne sélectionnée
    sortedRows = rows.sort((a, b) => {
        let aText = a.cells[sortColumn].textContent.toLowerCase();
        let bText = b.cells[sortColumn].textContent.toLowerCase();
        if (sortColumn === 'email') {
            aText = a.cells[sortColumn].querySelector('a').href.toLowerCase();
            bText = b.cells[sortColumn].querySelector('a').href.toLowerCase();
        }
        return (aText.localeCompare(bText)) * sortOrder;
    });

    // Réaffichage des lignes triées
    sortedRows.forEach(row => table.appendChild(row));
}


        // Fonction pour mettre à jour le critère et l'ordre de tri en fonction de la sélection
        function updateSortCriteria() {
    const criterion = document.getElementById("sortCriterion").value;
    const order = document.getElementById("sortOrder").value;

    // Mappage des critères
    const criteriaMap = {
        'nom_complet': 0,  // Nom complet correspond à la colonne 0
        'role': 1,         // Rôle correspond à la colonne 1
        'email': 2         // Email correspond à la colonne 2
    };

    sortColumn = criteriaMap[criterion];  // Mise à jour de l'index de la colonne
    sortOrder = order === 'asc' ? 1 : -1; // Si 'asc', tri croissant, sinon décroissant
}


        // Fonction qui sera appelée lorsque l'utilisateur clique sur le bouton de validation
        function validateSort() {
            updateSortCriteria(); // Met à jour les critères de tri
            sortTable(); // Trie la table en fonction des critères
        }
    </script>
</head>
<body>
    <div class="bandeau"></div>
    <div class="rectangle">
        <div class="container">
            <h2>Liste des Étudiants et Professeurs de l'UE</h2>
            
            <!-- Sélection du critère de tri et de l'ordre -->
            <label for="sortCriterion">Trier par :</label>
            <select id="sortCriterion">
                <option value="nom_complet">Nom / Prénom</option>
                <option value="email">Email</option>
                <option value="role">Rôle</option>
            </select>

            <label for="sortOrder">Ordre :</label>
            <select id="sortOrder">
                <option value="asc">Croissant</option>
                <option value="desc">Décroissant</option>
            </select>

            <!-- Bouton de validation pour effectuer le tri -->
            <button onclick="validateSort()">Valider</button>

            <?php
            // Vérifier si des résultats sont trouvés
            if (count($combined_results) > 0) {
                // Début du tableau
                echo "<table id='userTable'>
                <thead>
                    <tr>
                        <th>Nom Complet</th>
                        <th>Rôle</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>";

                // Boucle pour afficher les données de chaque utilisateur (étudiant ou professeur)
                foreach ($combined_results as $row) {
                    echo "<tr><td>" . htmlspecialchars($row["nom_complet"]) . "</td>
                              <td>" . htmlspecialchars($row["role"]) . "</td>
                              <td><a href='mailto:" . htmlspecialchars($row["email"]) . "'>" . htmlspecialchars($row["email"]) . "</a></td></tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Aucun utilisateur trouvé.</p>";
            }
            ?>
        </div>

        <?php
        // Fermer la connexion à la base de données
        $conn->close();
        ?>
    </div>
</body>
</html>
