<?php
require('../includes/header.php');
require_once('../includes/dbconnect.php');

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
$loggedIn = isset($_SESSION['user']);

// Connexion à la base de données
$connexion = dbconnect();

// Vérifier si l'utilisateur est un administrateur
function isAdmin($username, $connexion) {
    try {
        $stmt = $connexion->prepare("SELECT admin FROM membres WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user['admin'] == 1;
    } catch (PDOException $e) {
        echo "Erreur de requête : " . $e->getMessage();
        return false;
    }
}

// Gérer le téléchargement des fichiers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['media_files'])) {
    if (!$loggedIn) {
        echo "Vous devez être connecté pour ajouter des médias.";
        exit();
    }

    $username = $_SESSION['user']['username'];
    $description = $_POST['description'];
    $target_dir = __DIR__ . '/../uploads/media/';
    
    // Vérifier si le répertoire de destination existe, sinon créez-le
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    foreach ($_FILES['media_files']['name'] as $key => $name) {
        $target_file = $target_dir . basename($name);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifier si le fichier est un type autorisé
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'jfif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Désolé, seuls les fichiers JPG, JPEG, JFIF, PNG, GIF, MP4, AVI et MOV sont autorisés.";
            $uploadOk = 0;
        }

        // Vérifier si $uploadOk est défini à 0 par une erreur
        if ($uploadOk == 0) {
            echo "Désolé, votre fichier n'a pas été téléchargé.";
        } else {
            if (move_uploaded_file($_FILES["media_files"]["tmp_name"][$key], $target_file)) {
                // Insérer les informations du fichier dans la base de données
                $lien = basename($name);

                try {
                    $stmt = $connexion->prepare("INSERT INTO MEDIA (username, description, lien, date_uploaded) VALUES (:username, :description, :lien, NOW())");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':lien', $lien);
                    $stmt->execute();

                    echo "Le fichier $name a été téléchargé avec succès.";
                } catch (PDOException $e) {
                    echo "Erreur de requête : " . $e->getMessage();
                }
            } else {
                echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier $name.";
                // Afficher l'erreur spécifique
                echo "Erreur : " . $_FILES["media_files"]["error"][$key];
            }
        }
    }
}

// Supprimer un média
if (isset($_GET['delete_id'])) {
    if ($loggedIn && isset($_SESSION['user']['username'])) {
        $username = $_SESSION['user']['username'];

        // Vérifier si l'utilisateur est un administrateur
        if (!isAdmin($username, $connexion)) {
            echo "Vous devez être un administrateur pour supprimer un média.";
            exit();
        }

        $delete_id = $_GET['delete_id'];

        // Récupérer le lien du média avant la suppression
        try {
            $stmt = $connexion->prepare("SELECT lien FROM MEDIA WHERE id = :id");
            $stmt->bindParam(':id', $delete_id);
            $stmt->execute();
            $media = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($media) {
                // Supprimer le fichier physique
                $filePath = __DIR__ . '/../uploads/media/' . $media['lien'];
                if (file_exists($filePath)) {
                    unlink($filePath); // Supprime le fichier du serveur
                }

                // Supprimer l'entrée dans la base de données
                $stmt = $connexion->prepare("DELETE FROM MEDIA WHERE id = :id");
                $stmt->bindParam(':id', $delete_id);
                $stmt->execute();

                echo "Le média a été supprimé avec succès.";
            }
        } catch (PDOException $e) {
            echo "Erreur de requête : " . $e->getMessage();
        }
    } else {
        echo "Vous devez être connecté pour supprimer un média.";
    }
}

// Récupérer les médias depuis la base de données
try {
    $stmt = $connexion->prepare("SELECT * FROM MEDIA ORDER BY date_uploaded DESC");
    $stmt->execute();
    $medias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de requête : " . $e->getMessage();
    exit();
}

$groupedMedias = [];
foreach ($medias as $media) {
    $key = $media['username'] . '|' . $media['date_uploaded'] . '|' . $media['description'];
    $groupedMedias[$key][] = $media;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Déclaration du jeu de caractères et de la vue responsive -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="rapidos/css/style.css"> <!-- Lien vers le fichier CSS pour le style -->
    <style>
        /* Style pour les boutons dans la section d'upload */
        .upload-section button {
            background-color: #FF8A33;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px 0;
        }

        /* Style pour la galerie de médias */
        .media-gallery {
            width: 80%;
            margin: 0 auto;
            text-align: left;
        }

        /* Style pour chaque élément dans la galerie */
        .media-item {
            margin-bottom: 20px;
        }

        /* Style pour les miniatures des médias */
        .media-thumbnails img,
        .media-thumbnails video {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin: 5px;
        }

        /* Style pour les popups */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        /* Style du contenu de la popup */
        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 800px;
            position: relative;
            text-align: left;
        }
    </style>
    <title>Gestion des Médias</title>
</head>
<body>

<header class="header">
    <h1>Gestion des Médias</h1>
</header>

<!-- Si l'utilisateur est connecté, afficher le bouton pour ajouter un média -->
<?php if ($loggedIn): ?>
    <section class="upload-section">
        <button id="uploadButton">Ajouter un média</button>
    </section>
<?php else: ?>
    <!-- Si l'utilisateur n'est pas connecté, afficher un message -->
    <p>Vous devez être connecté pour ajouter un média.</p>
<?php endif; ?>

<!-- Popup pour l'ajout de médias -->
<div id="uploadPopup" class="popup">
    <div class="popup-content">
        <span class="close">&times;</span> <!-- Bouton de fermeture de la popup -->
        <h2>Ajouter un média</h2>
        <!-- Formulaire d'ajout de médias -->
        <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
            <label for="media_files">Fichiers :</label>
            <input type="file" name="media_files[]" multiple required> <!-- Champ pour sélectionner plusieurs fichiers -->

            <label for="description">Description :</label>
            <textarea name="description" rows="4" placeholder="Description (optionnel)"></textarea> <!-- Champ pour une description -->

            <button type="submit" class="okbtn">Ajouter le média</button> <!-- Bouton de soumission -->
        </form>
    </div>
</div>

<!-- Popup de confirmation pour la suppression d'un média -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span> <!-- Bouton de fermeture -->
        <p>Êtes-vous sûr de vouloir supprimer ce média ?</p> <!-- Message de confirmation -->
        <button id="confirmDeleteButton" class="okbtn">Supprimer</button>
        <button class="cancelbtn" onclick="closeModal('deleteModal')">Annuler</button>
    </div>
</div>

<!-- Affichage de la galerie de médias -->
<section class="media-gallery">
    <?php foreach ($groupedMedias as $key => $group): ?>
        <div class="media-item">
            <?php
            // Formatage de la date d'upload
            $dateUploaded = date("d/m/Y H:i", strtotime($group[0]['date_uploaded']));
            ?>
            <p><strong><?= htmlspecialchars($group[0]['username']); ?></strong> - Description : <?= htmlspecialchars($group[0]['description']); ?> - Publié le : <?= $dateUploaded; ?></p>
            
            <!-- Affichage des miniatures des médias -->
            <div class="media-thumbnails">
                <?php foreach ($group as $media): ?>
                    <?php
                    $filePath = __DIR__ . '/../uploads/media/' . $media['lien'];  // Chemin du fichier média
                    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION); // Récupérer l'extension du fichier
                    ?>
                    <!-- Affichage d'une image si le fichier est une image -->
                    <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'jfif'])): ?>
                        <img src="../uploads/media/<?= htmlspecialchars($media['lien']); ?>" alt="Image">
                    <?php elseif (in_array(strtolower($fileExtension), ['mp4', 'avi', 'mov'])): ?>
                        <!-- Affichage d'une vidéo si le fichier est une vidéo -->
                        <video width="100" height="100" controls>
                            <source src="../uploads/media/<?= htmlspecialchars($media['lien']); ?>" type="video/<?= $fileExtension; ?>">
                        </video>
                    <?php endif; ?>

                    <!-- Si l'utilisateur est connecté et est un administrateur, afficher le lien pour supprimer le média -->
                    <?php if ($loggedIn && isAdmin($_SESSION['user']['username'], $connexion)): ?>
                        <a href="?delete_id=<?= $media['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce média ?');">Supprimer</a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
</section>

<script>
    // Logique pour gérer l'affichage et la fermeture de la popup d'upload
    const uploadButton = document.getElementById('uploadButton');
    const uploadPopup = document.getElementById('uploadPopup');
    const closeButton = document.querySelector('.close');

    uploadButton.addEventListener('click', () => {
        uploadPopup.style.display = 'flex';
    });

    closeButton.addEventListener('click', () => {
        uploadPopup.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === uploadPopup) {
            uploadPopup.style.display = 'none';
        }
    });

    // Logique pour ouvrir et fermer les modals de suppression
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Confirmation de la suppression d'un média
    document.getElementById('confirmDeleteButton').addEventListener('click', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const deleteId = urlParams.get('delete_id');
        if (deleteId) {
            window.location.href = `?delete_id=${deleteId}`;
        }
    });

    window.onclick = function(event) {
        const deleteModal = document.getElementById('deleteModal');
        if (event.target == deleteModal) {
            closeModal('deleteModal');
        }
    }
</script>

</body>
</html>
<?php
require('../includes/footer.php'); 
