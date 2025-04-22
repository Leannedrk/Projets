<!-- filepath: /c:/wamp64/www/rapidos/pages/evenements.php -->
<?php
require('../includes/header.php');
require_once('../includes/dbconnect.php');

// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
$loggedIn = isset($_SESSION['user']);

// Connexion à la base de données
$connexion = dbconnect();

// Gérer l'ajout, la modification et la suppression d'événements (réservé aux administrateurs)
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['user']['admin']) && $_SESSION['user']['admin'] == 1) {
        $username = $_SESSION['user']['username'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $event_date = $_POST['event_date'];

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Modification de l'événement existant
            $id = $_POST['id'];
            try {
                $stmt = $connexion->prepare("UPDATE EVENEMENTS SET title = :title, description = :description, event_date = :event_date WHERE id = :id AND username = :username");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':event_date', $event_date);
                $stmt->execute();

                $successMessage = "L'événement '$title' a été modifié avec succès.";
            } catch (PDOException $e) {
                echo "Erreur de requête : " . $e->getMessage();
            }
        } else {
            // Ajout d'un nouvel événement
            try {
                $stmt = $connexion->prepare("INSERT INTO EVENEMENTS (username, title, description, event_date) VALUES (:username, :title, :description, :event_date)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':event_date', $event_date);
                $stmt->execute();

                $successMessage = "L'événement '$title' a été ajouté avec succès.";
            } catch (PDOException $e) {
                echo "Erreur de requête : " . $e->getMessage();
            }
        }
    } else {
        echo "Vous n'êtes pas autorisé à ajouter ou modifier des événements.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete_id'])) {
    if (isset($_SESSION['user']['admin']) && $_SESSION['user']['admin'] == 1) {
        $id = $_GET['delete_id'];
        try {
            $stmt = $connexion->prepare("DELETE FROM EVENEMENTS WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $successMessage = "L'événement a été supprimé avec succès.";
        } catch (PDOException $e) {
            echo "Erreur de requête : " . $e->getMessage();
        }
    } else {
        echo "Vous n'êtes pas autorisé à supprimer des événements.";
    }
}

// Récupérer les événements existants
try {
    $stmt = $connexion->prepare("SELECT * FROM EVENEMENTS ORDER BY event_date DESC");
    $stmt->execute();
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de requête : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Événements</title>
    <link rel="stylesheet" href="/rapidos/css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
    <header class="header">
        <br>
        <h1 class="title-left">Gestion des Événements</h1>
        <br>
    </header>

    <?php if ($loggedIn && $_SESSION['user']['admin'] == 1): ?>
        <section class="upload-section">
            <button id="uploadButton" style="margin-left: 50px;">Ajouter un événement</button>
        </section>
    <?php else: ?>
        <p></p>
    <?php endif; ?>

    <div id="uploadPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Ajouter un événement</h2>
            <form id="uploadForm" action="evenements.php" method="post">
                <label for="title">Titre :</label>
                <input type="text" name="title" required>

                <label for="description">Description :</label>
                <textarea name="description" rows="4" placeholder="Description (optionnel)"></textarea>

                <label for="event_date">Date de l'événement :</label>
                <input type="datetime-local" name="event_date" required>

                <button type="submit" class="okbtn">Ajouter l'événement</button>
            </form>
        </div>
    </div>

    <!-- Popup de succès -->
    <div id="successPopup" class="popup" style="display: <?= $successMessage ? 'block' : 'none'; ?>;">
        <div class="popup-content">
            <span class="close" onclick="closeSuccessPopup()">&times;</span>
            <p id="successMessage"><?= htmlspecialchars($successMessage); ?></p>
            <button class="okbtn" onclick="closeSuccessPopup()">OK</button>
        </div>
    </div>

    <!-- Popup de modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <form id="editForm" method="post" action="evenements.php">
                <input type="hidden" name="id" id="editId">
                <label for="editTitle">Titre :</label>
                <input type="text" name="title" id="editTitle" required>
                <label for="editDescription">Description :</label>
                <textarea name="description" id="editDescription" rows="4" placeholder="Description (optionnel)"></textarea>
                <label for="editEventDate">Date de l'événement :</label>
                <input type="datetime-local" name="event_date" id="editEventDate" required>
                <button type="submit" class="okbtn">Modifier l'événement</button>
            </form>
        </div>
    </div>

    <!-- Popup de confirmation de suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <p>Êtes-vous sûr de vouloir supprimer cet événement ?</p>
            <button id="confirmDeleteButton" class="okbtn">Supprimer</button>
            <button class="cancelbtn" onclick="closeModal('deleteModal')">Annuler</button>
        </div>
    </div>

    <section class="event-gallery">
        <?php foreach ($evenements as $evenement): ?>
            <div class="event-item">
                <p><strong><?= htmlspecialchars($evenement['title']); ?></strong></p>
                <p>Publié par <?= htmlspecialchars($evenement['username']); ?>, le <?= htmlspecialchars(date('d M. Y, H:i', strtotime($evenement['event_date']))); ?></p>
                <p>Description : <?= htmlspecialchars($evenement['description']); ?></p>
                
                <?php if ($loggedIn && $_SESSION['user']['admin'] == 1): ?>
                    <a href="#" onclick="openEditModal(<?= $evenement['id']; ?>, '<?= htmlspecialchars($evenement['title']); ?>', '<?= htmlspecialchars($evenement['description']); ?>', '<?= htmlspecialchars($evenement['event_date']); ?>')">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="#" onclick="openDeleteModal(<?= $evenement['id']; ?>)">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                <?php endif; ?>
            </div>
            <hr>
        <?php endforeach; ?>
    </section>

    <script>
        document.getElementById('uploadButton').addEventListener('click', function() {
            document.getElementById('uploadPopup').style.display = 'block';
        });

        function closePopup() {
            document.getElementById('uploadPopup').style.display = 'none';
        }

        function closeSuccessPopup() {
            document.getElementById('successPopup').style.display = 'none';
        }

        function openEditModal(id, title, description, eventDate) {
            document.getElementById('editId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editEventDate').value = eventDate;
            document.getElementById('editModal').style.display = 'block';
        }

        function openDeleteModal(id) {
            document.getElementById('confirmDeleteButton').onclick = function() {
                window.location.href = '?delete_id=' + id;
            };
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>

<?php
require('../includes/footer.php');
?>