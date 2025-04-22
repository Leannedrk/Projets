<?php
require('../includes/header.php');
require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
$loggedIn = isset($_SESSION['user']);

// Connexion à la base de données
$connexion = dbconnect();

// Vérifier si un événement est à modifier
$event = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = (int)$_GET['id'];

    // Récupérer les informations de l'événement à modifier
    try {
        $stmt = $connexion->prepare("SELECT * FROM EVENEMENTS WHERE id = :id");
        $stmt->bindParam(':id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de requête : " . $e->getMessage();
        exit();
    }
}

// Gérer l'ajout ou la modification d'événements
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['description'], $_POST['event_date'])) {
    // Sécuriser et valider les entrées utilisateur
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $event_date = $_POST['event_date'];

    // Si on est en mode modification
    if ($event) {
        // Mise à jour de l'événement dans la base de données
        try {
            $stmt = $connexion->prepare("UPDATE EVENEMENTS SET title = :title, description = :description, event_date = :event_date WHERE id = :id");
            $stmt->bindParam(':id', $event_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->execute();

            echo "L'événement a été modifié avec succès.";
            header("Location: evenements.php"); // Rediriger vers la page des événements après la modification
            exit();
        } catch (PDOException $e) {
            echo "Erreur de requête : " . $e->getMessage();
        }
    } else {
        // Mode ajout d'événement
        try {
            $username = $_SESSION['user']['username'];
            $stmt = $connexion->prepare("INSERT INTO EVENEMENTS (username, title, description, event_date) VALUES (:username, :title, :description, :event_date)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->execute();

            echo "L'événement '$title' a été ajouté avec succès.";
            header("Location: evenements.php"); // Rediriger vers la page des événements après l'ajout
            exit();
        } catch (PDOException $e) {
            echo "Erreur de requête : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="rapidos/css/style.css">
    <title><?= $event ? 'Modifier' : 'Ajouter' ?> l'Événement</title>
</head>
<body>

    <header class="header">
        <h1><?= $event ? 'Modifier' : 'Ajouter' ?> l'Événement</h1>
    </header>

    <section class="event-edit-form">
        <form action="" method="POST">
            <label for="title">Titre de l'événement:</label>
            <input type="text" id="title" name="title" value="<?= $event ? htmlspecialchars($event['title']) : '' ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= $event ? htmlspecialchars($event['description']) : '' ?></textarea>

            <label for="event_date">Date de l'événement:</label>
            <input type="datetime-local" id="event_date" name="event_date" value="<?= $event ? htmlspecialchars($event['event_date']) : '' ?>" required>

            <button type="submit" class="okbtn"><?= $event ? 'Modifier' : 'Ajouter' ?> l'événement</button>
        </form>
    </section>

</body>
</html>
