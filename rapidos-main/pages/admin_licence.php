<?php
require('../includes/header.php');
require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
    echo "Accès refusé.";
    exit();
}

// Connexion à la base de données
$connexion = dbconnect();

// Mettre à jour la colonne valide si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['licence_id'])) {
    $licence_id = $_POST['licence_id'];
    $valide = $_POST['valide'];

    try {
        $stmt = $connexion->prepare("UPDATE LICENCE SET valide = :valide WHERE id = :id");
        $stmt->bindParam(':valide', $valide);
        $stmt->bindParam(':id', $licence_id);
        $stmt->execute();
        echo "Statut de la licence mis à jour avec succès.";
    } catch (PDOException $e) {
        echo "Erreur de mise à jour : " . $e->getMessage();
    }
}

// Récupérer les demandes de licence avec les informations de la table MEMBRES
try {
    $stmt = $connexion->prepare("
        SELECT LICENCE.*, MEMBRES.nom, MEMBRES.prenom, MEMBRES.mail
        FROM LICENCE
        JOIN MEMBRES ON LICENCE.username = MEMBRES.username
    ");
    $stmt->execute();
    $licences = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de requête : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Licences</title>
    <link rel="stylesheet" href="/rapidos/css/style.css">
    <script>
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('licenceTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td');
            let found = false;
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    if (td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            if (found) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
    </script>
</head>
<body>
    <h2>Liste des demandes de licence</h2>
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Rechercher...">
    <table id="licenceTable" border="1">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Sexe</th>
                <th>Date de naissance</th>
                <th>Ville</th>
                <th>Téléphone</th>
                <th>Nationalité</th>
                <th>Nom du représentant légal</th>
                <th>Profession du père</th>
                <th>Numéro de téléphone du père</th>
                <th>Profession de la mère</th>
                <th>Numéro de téléphone de la mère</th>
                <th>Adresse mail parentale</th>
                <th>Carte d'identité</th>
                <th>Certificat médical</th>
                <th>Photo</th>
                <th>Total de la cotisation</th>
                <th>Moyen de paiement</th>
                <th>Valide</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($licences)) : ?>
                <?php foreach ($licences as $licence) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($licence['nom']); ?></td>
                        <td><?php echo htmlspecialchars($licence['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($licence['mail']); ?></td>
                        <td><?php echo $licence['sexe'] == 0 ? 'H' : 'F'; ?></td>
                        <td><?php echo htmlspecialchars($licence['date_naiss']); ?></td>
                        <td><?php echo htmlspecialchars($licence['ville']); ?></td>
                        <td><?php echo htmlspecialchars($licence['telephone']); ?></td>
                        <td><?php echo isset($licence['nationnalite']) ? htmlspecialchars($licence['nationnalite']) : 'Non spécifiée'; ?></td>                        <td><?php echo htmlspecialchars($licence['nom_p']); ?></td>
                        <td><?php echo htmlspecialchars($licence['prof_p']); ?></td>
                        <td><?php echo htmlspecialchars($licence['tel_p']); ?></td>
                        <td><?php echo htmlspecialchars($licence['prof_m']); ?></td>
                        <td><?php echo htmlspecialchars($licence['tel_m']); ?></td>
                        <td><?php echo htmlspecialchars($licence['mail_p']); ?></td>
                        <td><a href="../uploads/<?php echo htmlspecialchars($licence['carte_id']); ?>" download>Télécharger</a></td>
                        <td><a href="../uploads/<?php echo htmlspecialchars($licence['certif_med']); ?>" download>Télécharger</a></td>
                        <td><a href="../uploads/<?php echo htmlspecialchars($licence['photo_id']); ?>" download>Télécharger</a></td>
                        <td><?php echo htmlspecialchars($licence['cotisation_som']); ?></td>
                        <td><?php echo htmlspecialchars($licence['cotisation_m']); ?></td>
                        <td><?php echo htmlspecialchars($licence['valide']); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="licence_id" value="<?php echo htmlspecialchars($licence['id']); ?>">
                                <select name="valide">
                                    <option value="0" <?php echo $licence['valide'] == 0 ? 'selected' : ''; ?>>Non</option>
                                    <option value="1" <?php echo $licence['valide'] == 1 ? 'selected' : ''; ?>>Oui</option>
                                </select>
                                <button type="submit">Mettre à jour</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="23">Aucune demande de licence trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
<?php require('../includes/footer.php'); ?>