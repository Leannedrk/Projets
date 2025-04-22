<?php
require('../includes/header.php');

// Connexion à la base de données
$connexion = dbconnect();

// Vérifier si l'ID du document est passé via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les informations du document
    $stmt = $connexion->prepare("SELECT * FROM DOCS WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) {
        echo "Document non trouvé.";
        exit();
    }
} else {
    echo "ID manquant.";
    exit();
}

// Gérer la modification du fichier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $titre = $_POST['titre'];
    $comm = $_POST['comm'];
    $lien = $_FILES['file']['name'] ? basename($_FILES['file']['name']) : $doc['lien']; // Garder l'ancien fichier si aucun nouveau n'est téléchargé

    // Si un nouveau fichier est téléchargé, le déplacer et mettre à jour le lien
    if ($lien !== $doc['lien']) {
        $target_dir = __DIR__ . "/../uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $lien;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifier si le fichier est un type autorisé
        $allowedTypes = ['pdf', 'png', 'jpg', 'jpeg', 'avif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Désolé, seuls les fichiers PDF, PNG, JPG, JPEG et AVIF sont autorisés.";
            exit();
        }

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "Erreur lors du téléchargement du fichier.";
            exit();
        }

        // Supprimer l'ancien fichier si nécessaire
        $old_file_path = $target_dir . $doc['lien'];
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }

    // Mettre à jour les informations dans la base de données
    $stmt = $connexion->prepare("UPDATE DOCS SET titre = :titre, comm = :comm, lien = :lien WHERE id = :id");
    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':comm', $comm);
    $stmt->bindParam(':lien', $lien);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo "Document mis à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Document</title>
    <link rel="stylesheet" href="/rapidos/css/style.css">
</head>
<body>
    <h2>Modifier le document</h2>
    <form action="modifier_docs.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
        <label for="titre">Titre:</label>
        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($doc['titre']); ?>" required><br><br>
        <label for="comm">Commentaire:</label>
        <input type="text" id="comm" name="comm" value="<?php echo htmlspecialchars($doc['comm']); ?>" required><br><br>
        <label for="file">Sélectionner un nouveau fichier (PDF, PNG, JPG, JPEG, AVIF) :</label>
        <input type="file" id="file" name="file" accept=".pdf, .png, .jpg, .jpeg, .avif"><br><br>
        <input type="submit" name="update" value="Mettre à jour">
        <button type="button" class="cancelbtn" onclick="window.location.href='docs.php';">Annuler</button>
    </form>
</body>
</html>
