<?php
// Ce fichier servira à afficher les fichiers à télécharger
require('../includes/header.php');

// Connexion à la base de données
$connexion = dbconnect();

// Gérer la suppression de fichiers
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Vérifier si l'utilisateur est connecté et est un administrateur
    if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
        echo "Accès refusé.";
        exit();
    }

    // Récupérer les informations du fichier à supprimer
    try {
        $stmt = $connexion->prepare("SELECT lien FROM DOCS WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            $lien = $doc['lien'];
            $file_path = __DIR__ . "/../uploads/" . $lien;

            // Supprimer le fichier du serveur
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Supprimer l'entrée de la base de données
            $stmt = $connexion->prepare("DELETE FROM DOCS WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            echo "Document supprimé avec succès.";
        } else {
            echo "Document non trouvé.";
        }
    } catch (PDOException $e) {
        echo "Erreur de requête : " . $e->getMessage();
    }
}

// Gérer la modification de fichiers
if (isset($_POST['update']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $comm = $_POST['comm'];
    $lien = $_FILES['file']['name'] ? basename($_FILES['file']['name']) : null;

    // Si un nouveau fichier est téléchargé, le déplacer et mettre à jour le chemin
    if ($lien) {
        $target_dir = __DIR__ . "/../uploads/";

        // Vérifiez si le répertoire de destination existe, sinon créez-le
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

        // Vérifiez les permissions du répertoire de destination
        if (!is_writable($target_dir)) {
            echo "Désolé, le répertoire de destination n'est pas accessible en écriture.";
            exit();
        }

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            exit();
        }

        // Supprimer l'ancien fichier
        $stmt = $connexion->prepare("SELECT lien FROM DOCS WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            $old_file_path = $target_dir . $doc['lien'];
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }
    }

    // Mettre à jour les informations du fichier dans la base de données
    $stmt = $connexion->prepare("UPDATE DOCS SET titre = :titre, comm = :comm, lien = :lien WHERE id = :id");
    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':comm', $comm);
    $stmt->bindParam(':lien', $lien);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo "Document mis à jour avec succès.";
}

// Gérer le dépôt de fichiers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && !isset($_POST['update'])) {
    $target_dir = __DIR__ . "/../uploads/";

    // Vérifiez si le répertoire de destination existe, sinon créez-le
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifier si le fichier est un type autorisé
    $allowedTypes = ['pdf', 'png', 'jpg', 'jpeg', 'avif'];
    if (!in_array($fileType, $allowedTypes)) {
        echo "Désolé, seuls les fichiers PDF, PNG, JPG, JPEG et AVIF sont autorisés.";
        $uploadOk = 0;
    }

    // Vérifiez les permissions du répertoire de destination
    if (!is_writable($target_dir)) {
        echo "Désolé, le répertoire de destination n'est pas accessible en écriture.";
        $uploadOk = 0;
    }

    // Vérifier si $uploadOk est défini à 0 par une erreur
    if ($uploadOk == 0) {
        echo "Désolé, votre fichier n'a pas été téléchargé.";
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // Insérer les informations du fichier dans la base de données
            $titre = $_POST['titre'];
            $comm = $_POST['comm'];
            $lien = basename($_FILES["file"]["name"]);

            try {
                $stmt = $connexion->prepare("INSERT INTO DOCS (titre, comm, lien) VALUES (:titre, :comm, :lien)");
                $stmt->bindParam(':titre', $titre);
                $stmt->bindParam(':comm', $comm);
                $stmt->bindParam(':lien', $lien);
                $stmt->execute();

                echo "Le fichier a été téléchargé avec succès.";
            } catch (PDOException $e) {
                echo "Erreur de requête : " . $e->getMessage();
            }
        } else {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
        }
    }
}

// Récupérer les données de la table DOCS
try {
    $stmt = $connexion->prepare("SELECT * FROM DOCS");
    $stmt->execute();
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de requête : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Documents</title>
    <link rel="stylesheet" href="/rapidos/css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="../js/script.js"></script>
</head>
<body>
    <div classe="title">
        <h2>Documents à télécharger</h2>
    </div>
    <input type="text" class="searchinput" id="searchInput" onkeyup="searchFunction()" placeholder="Rechercher...">
    <table border="1" id="docsTable">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Commentaire</th>
                <th>Télécharger</th>
                <th>Modifier</th>
                <th>Supprimer</th>
                
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['admin'] == 1) { ?>
                    <th><button id="openModalBtn" class="addbtn" >+</button></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docs as $doc) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($doc['titre'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($doc['comm'] ?? ''); ?></td>
                    <td><a href="../uploads/<?php echo htmlspecialchars($doc['lien'] ?? ''); ?>" download title="Télécharger">
                        <i class="fas fa-download"></i>
                    </a></td>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['admin'] == 1) { ?>
                        <td>
                        <a href="#" class="editBtn" data-id="<?php echo $doc['id']; ?>" data-titre="<?php echo htmlspecialchars($doc['titre']); ?>" data-comm="<?php echo htmlspecialchars($doc['comm']); ?>" title="Modifier">
                        <i class="fas fa-edit"></i>
                        </a>
                    </td>  
                    <td><a href="#" class="deleteBtn"  data-id="<?php echo $doc['id']; ?>" title="Supprimer" >
                            <i class="fas fa-trash-alt"></i>
                        </a>    
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- popup pour ajouter un document -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['admin'] == 1) { ?>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Déposer un nouveau document</h2>
                <form action="docs.php" method="post" enctype="multipart/form-data">
                    <label for="titre">Titre:</label>
                    <input type="text" id="titre" name="titre" required><br><br>
                    <label for="comm">Commentaire:</label>
                    <input type="text" id="comm" name="comm" required><br><br>
                    <label for="file">Sélectionner un fichier (PDF, PNG, JPG, JPEG, AVIF):</label>
                    <input type="file" id="file" name="file" accept=".pdf, .png, .jpg, .jpeg, .avif" required><br><br>
                    <input type="submit" value="Déposer">
                    <button type="button" class="close" >Annuler</button>
                </form>
            </div>
        </div>

        <!-- popup pour modifier un document -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Modifier le document</h2>
                <form action="docs.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="id">
                    <label for="editTitre">Titre:</label>
                    <input type="text" id="editTitre" name="titre" required><br><br>
                    <label for="editComm">Commentaire:</label>
                    <input type="text" id="editComm" name="comm" required><br><br>
                    <label for="editFile">Sélectionner un nouveau fichier (PDF, PNG, JPG, JPEG, AVIF):</label>
                    <input type="file" id="editFile" name="file" accept=".pdf, .png, .jpg, .jpeg, .avif"><br><br>
                    <input type="submit" name="update" value="Mettre à jour">
                    <button type="button" class="close">Annuler</button>
                </form>
            </div>
        </div>

        <!-- popup pour confirmer la suppression -->
        <div id="confirmDeleteModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Confirmer la suppression</h2>
                <p>Êtes-vous sûr de vouloir supprimer ce document ?</p>
                <button class="cancelbtn" id="confirmDeleteBtn">Supprimer</button>
                <button type="button" class="close">Annuler</button>
            </div>
        </div>
    <?php } ?>

    <script src="../js/docjs.js"></script>

</body>
</html>

<?php
require('../includes/footer.php');
?>