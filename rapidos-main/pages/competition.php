<?php
require('../includes/header.php');
require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

// Connexion à la base de données
$connexion = dbconnect();

// Requête SQL pour récupérer les compétitions avec une date supérieure à aujourd'hui
$sql = "SELECT name, date, heure, distance, sexe, lieu, age, nbplace 
        FROM competition 
        WHERE date > CURDATE() 
        ORDER BY date ASC";
$stmt = $connexion->prepare($sql);
$stmt->execute();

// Récupérer les résultats sous forme de tableau associatif
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétitions</title>
    <link rel="stylesheet" href="/rapidos/css/style.css">
</head>
<body>

<h2>Liste des Compétitions</h2>

<!-- Champ de recherche -->
<input type="text" class="searchinput" id="searchInput" placeholder="Rechercher une compétition..." onkeyup="searchTable()">

<table id="competitionsTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Distance</th>
            <th>Sexe</th>
            <th>Lieu</th>
            <th>Âge</th>
            <th>Nombre de places</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Vérifier si des résultats existent
        if ($competitions) {
            // Parcourir les résultats et afficher chaque compétition
            foreach ($competitions as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['heure']) . "</td>";
                echo "<td>" . htmlspecialchars($row['distance']) . " km</td>";
                echo "<td>" . htmlspecialchars($row['sexe']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lieu']) . "</td>";
                echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nbplace']) . "</td>";
                echo "</tr>";
            }
        } else {
            // Afficher un message si aucune compétition n'existe
            echo "<tr><td colspan='8'>Aucune compétition trouvée.</td></tr>";
        }
        ?>
    </tbody>
</table>

<script>
// Fonction de recherche dans le tableau
function searchTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById('searchInput');
    filter = input.value.toUpperCase();
    table = document.getElementById('competitionsTable');
    tr = table.getElementsByTagName('tr');

    // Parcourir toutes les lignes du tableau, excepté l'en-tête
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = 'none'; // Cacher la ligne par défaut
        td = tr[i].getElementsByTagName('td');
        
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ''; // Afficher la ligne si la recherche correspond
                    break;
                }
            }
        }
    }
}
</script>

</body>
<br>
<br>
</html>

<?php require('../includes/footer.php'); ?>
