<?php
require('../includes/header.php');

$apiUrl = "https://geo.api.gouv.fr/communes?fields=nom&format=json&geometry=centre";
$response = file_get_contents($apiUrl);
$villes = json_decode($response, true);

$nomVilles = [];

// Récupérer uniquement les noms des villes
foreach ($villes as $ville) {
    if (isset($ville['nom'])) {
        $nomVilles[] = $ville['nom'];
    }
}

// Trier la liste des noms de villes par ordre alphabétique
sort($nomVilles);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Licence</title>
    <script>
        function checkAge() {
            const birthDate = new Date(document.getElementById('date_naiss').value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDifference = today.getMonth() - birthDate.getMonth();
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            const minorSection = document.getElementById('minorSection');
            if (age < 18) {
                minorSection.style.display = 'block';
            } else {
                minorSection.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h2>Inscription</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="sexe">Sexe :</label>
        <select id="sexe" name="sexe" required>
            <option value="0">Homme</option>
            <option value="1">Femme</option>
        </select><br><br>

        <label for="date_naiss">Date de naissance :</label>
        <input type="date" id="date_naiss" name="date_naiss" required onchange="checkAge()"><br><br>

        <label for="ville">Ville :</label>
        <input type="text" id="ville" name="ville" list="villes" required><br><br>
        <datalist id="villes">
            <?php foreach ($nomVilles as $nomVille) : ?>
                <option value="<?php echo htmlspecialchars($nomVille); ?>">
            <?php endforeach; ?>
        </datalist>

        <label for="telephone">Numéro de téléphone:</label>
        <input type="text" id="telephone" name="telephone" required><br><br>
        
        <label for="nationnalite">Nationalité:</label>
<input type="text" id="nationnalite" name="nationnalite" required><br><br>


        <!-- Section Si mineur -->
        <div id="minorSection" style="display: none;">
            <label for="nom_p">Nom du représentant légal :</label>
            <input type="text" id="nom_p" name="nom_p" ><br><br>

            <label for="prof_p">Profession du père :</label>
            <input type="text" id="prof_p" name="prof_p" ><br><br>

            <label for="tel_p">Numéro de téléphone du père :</label>
            <input type="text" id="tel_p" name="tel_p" ><br><br>

            <label for="prof_m">Profession de la mère :</label>
            <input type="text" id="prof_m" name="prof_m" ><br><br>

            <label for="tel_m">Numéro de téléphone de la mère :</label>
            <input type="text" id="tel_m" name="tel_m" ><br><br>

            <label for="mail_p">Adresse mail parentale :</label>
            <input type="email" id="mail_p" name="mail_p" ><br><br>
        </div>

        <!-- ajout de pièces jointes -->
        <label for="carte_id">Carte d'identité :</label>
        <input type="file" id="carte_id" name="carte_id" accept=".jpg,.jpeg,.png,.pdf" required><br><br>

        <label for="certif_med">Certificat médical :</label>
        <input type="file" id="certif_med" name="certif_med" accept=".jpg,.jpeg,.png,.pdf" required><br><br>

        <label for="photo_id">Photo :</label>
        <input type="file" id="photo_id" name="photo_id" accept=".jpg,.jpeg,.png" required><br><br>

        <label for="cotisation_som">Total de la cotisation :</label>
        <input type="text" id="cotisation_som" name="cotisation_som" required><br><br>

        <label for="cotisation_m">Moyen de paiement :</label>
        <select id="cotisation_m" name="cotisation_m" required>
            <option value="cheque">Chèque</option>
            <option value="especes">Espèces</option>
            <option value="virement">Virement</option>
            <option value="bonCAF">Bon CAF</option>
            <option value="passport">Pass'port</option>
        </select><br><br>
        
        <input type="submit" value="S'inscrire">
    </form>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Vérifiez que les fichiers ont été téléchargés
        if (isset($_FILES['carte_id']) && isset($_FILES['certif_med']) && isset($_FILES['photo_id'])) {
            $sexe = $_POST['sexe'];
            $date_naiss = $_POST['date_naiss'];
            $ville = $_POST['ville'];
            $telephone = $_POST['telephone'];
            $nationality = $_POST['nationnalite'];
            $nom_p = $_POST['nom_p'];
            $prof_p = $_POST['prof_p'];
            $tel_p = $_POST['tel_p'];
            $prof_m = $_POST['prof_m'];
            $tel_m = $_POST['tel_m'];
            $mail_p = $_POST['mail_p'];
            $carte_id = $_FILES['carte_id']['name'];
            $certif_med = $_FILES['certif_med']['name'];
            $photo_id = $_FILES['photo_id']['name'];
            $cotisation_som = $_POST['cotisation_som'];
            $cotisation_m = $_POST['cotisation_m'];
            $valide = 0; // Mettre valide à 0 automatiquement

            // Déplacer les fichiers téléchargés vers le répertoire de destination
            // Récupérer le username de l'utilisateur connecté
            $username = $_SESSION['user']['username'];
            $target_dir = __DIR__ . '/../uploads/licence/' . $username . '/';

            // Vérifiez si le répertoire de destination existe, sinon créez-le
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $carte_id_path = $target_dir . basename($_FILES['carte_id']['name']);
            $certif_med_path = $target_dir . basename($_FILES['certif_med']['name']);
            $photo_id_path = $target_dir . basename($_FILES['photo_id']['name']);

            if (move_uploaded_file($_FILES['carte_id']['tmp_name'], $carte_id_path) &&
                move_uploaded_file($_FILES['certif_med']['tmp_name'], $certif_med_path) &&
                move_uploaded_file($_FILES['photo_id']['tmp_name'], $photo_id_path)) {
                
                // Connexion à la base de données
                $connexion = dbconnect();

                // Récupérer le username de l'utilisateur connecté
                $username = $_SESSION['user']['username'];

                // Définir le chemin relatif
                $base_dir = realpath(__DIR__ . '/../uploads/');
                $carte_id_path_rel = str_replace($base_dir, '', realpath($carte_id_path));
                $certif_med_path_rel = str_replace($base_dir, '', realpath($certif_med_path));
                $photo_id_path_rel = str_replace($base_dir, '', realpath($photo_id_path));

                try {
                    // Préparer la requête d'insertion
                    $stmt = $connexion->prepare("INSERT INTO LICENCE (username, sexe, date_naiss, ville, telephone, nationnalite, nom_p, prof_p, tel_p, prof_m, tel_m, mail_p, carte_id, certif_med, photo_id, cotisation_som, cotisation_m, valide) VALUES (:username, :sexe, :date_naiss, :ville, :telephone, :nationnalite, :nom_p, :prof_p, :tel_p, :prof_m, :tel_m, :mail_p, :carte_id, :certif_med, :photo_id, :cotisation_som, :cotisation_m, :valide)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':sexe', $sexe);
                    $stmt->bindParam(':date_naiss', $date_naiss);
                    $stmt->bindParam(':ville', $ville);
                    $stmt->bindParam(':telephone', $telephone);
                    $stmt->bindParam(':nationnalite', $nationality);
                    $stmt->bindParam(':nom_p', $nom_p);
                    $stmt->bindParam(':prof_p', $prof_p);
                    $stmt->bindParam(':tel_p', $tel_p);
                    $stmt->bindParam(':prof_m', $prof_m);
                    $stmt->bindParam(':tel_m', $tel_m);
                    $stmt->bindParam(':mail_p', $mail_p);
                    $stmt->bindParam(':carte_id', $carte_id_path_rel);
                    $stmt->bindParam(':certif_med', $certif_med_path_rel);
                    $stmt->bindParam(':photo_id', $photo_id_path_rel);
                    $stmt->bindParam(':cotisation_som', $cotisation_som);
                    $stmt->bindParam(':cotisation_m', $cotisation_m);
                    $stmt->bindParam(':valide', $valide);
                    $stmt->execute();

                    echo "Licence créée avec succès !";
                } catch (PDOException $e) {
                    echo "Erreur de requête : " . $e->getMessage();
                }
            } else {
                echo "Erreur lors du téléchargement des fichiers.";
            }
        } else {
            echo "Veuillez télécharger tous les fichiers requis.";
        }
    }
    ?>

</body>
</html>
<?php require('../includes/footer.php'); ?>