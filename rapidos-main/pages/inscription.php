<?php
require('../includes/header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <form method="post" action="">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required><br><br>
        
        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required><br><br>
        
        <label for="mail">Email:</label>
        <input type="email" id="mail" name="mail" required><br><br>
        
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="S'inscrire">
    </form>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../includes/dbconnect.php'); // Inclure le fichier de connexion à la base de données

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $mail = $_POST['mail'];
        $username = $_POST['username'];
        $password_hash = hash('sha256', $_POST['password']); // Hacher le mot de passe en SHA-256
        $admin = 0; // Mettre l'admin à 0 automatiquement

       
    
        // Connexion à la base de données
        $connexion = dbconnect();

        try {
            // Préparer la requête d'insertion
            $stmt = $connexion->prepare("INSERT INTO MEMBRES (admin, nom, prenom, mail, username, password_hash) VALUES (:admin, :nom, :prenom, :mail, :username, :password_hash)");
            $stmt->bindParam(':admin', $admin);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->execute();

            echo "Inscription réussie. Bienvenue, " . htmlspecialchars($prenom) . "!";
            header("Location: profil.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur de requête : " . $e->getMessage();
        }
    }
    ?>
</body>

</html>

<?php
require('../includes/footer.php');