<!-- filepath: /c:/wamp64/www/rapidos/pages/resultats.php -->
<?php
require('../includes/header.php');
require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

// Connexion à la base de données
$connexion = dbconnect();

// Requête SQL pour récupérer les compétitions où la date est inférieure à aujourd'hui avec les résultats
$sql = "SELECT c.id, c.date, c.heure, c.distance, c.sexe, c.lieu, c.age, c.nbplace, r.fichier AS resultats 
        FROM COMPETITION c 
        LEFT JOIN RESULTATS r ON c.id = r.id_competition 
        WHERE c.date < CURDATE() 
        ORDER BY c.date ASC";
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

<!-- Tableau des compétitions -->
<table id="competitionsTable">
    <thead>
        <tr>
            <th>Date</th>
            <th>Heure</th>
            <th>Distance</th>
            <th>Sexe</th>
            <th>Lieu</th>
            <th>Âge</th>
            <th>Nombre de places</th>
            <th>Résultats</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($competitions as $competition) { ?>
            <tr>
                <td><?php echo htmlspecialchars($competition['date']); ?></td>
                <td><?php echo htmlspecialchars($competition['heure']); ?></td>
                <td><?php echo htmlspecialchars($competition['distance']); ?></td>
                <td><?php echo htmlspecialchars($competition['sexe']); ?></td>
                <td><?php echo htmlspecialchars($competition['lieu']); ?></td>
                <td><?php echo htmlspecialchars($competition['age']); ?></td>
                <td><?php echo $competition['nbplace'] == 0 ? 'illimité' : htmlspecialchars($competition['nbplace']); ?></td>
                <td>
                    <?php if ($competition['resultats']) { ?>
                        <a href="../uploads/<?php echo htmlspecialchars($competition['resultats']); ?>" download>Télécharger</a>
                    <?php } else { ?>
                        Pas de résultats
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
function searchTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("competitionsTable");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}
</script>

<?php
require('../includes/footer.php');
?>  