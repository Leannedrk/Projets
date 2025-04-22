<?php
require('../includes/header.php');
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $connexion = dbconnect();

    try {
        // Préparer la requête
        $stmt = $connexion->prepare("SELECT * FROM MEMBRES WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (hash('sha256', $password) === $user['password_hash']) {
                // Connexion réussie
                $_SESSION['user'] = $user; // Stocker toutes les informations de l'utilisateur dans la session
                echo "Connexion réussie. Bienvenue, " . htmlspecialchars($user['username']) . "!";
                
                // Rediriger vers la page profil
                header("Location: profil.php");
                exit();
            } else {
                // Mot de passe incorrect
                echo "Mot de passe incorrect.";
            }
        } else {
            // Utilisateur non trouvé
            echo "Nom d'utilisateur incorrect.";
        }
    } catch (PDOException $e) {
        echo "Erreur de requête : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>
    <form method="post" action="">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Se connecter">
    </form>

    <p>Pas encore de compte? <a href="inscription.php">Inscrivez-vous ici</a></p>
</body>

</html>

<?php
require('../includes/footer.php');
