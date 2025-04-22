<?php
require('../includes/header.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: connection.php");
    exit();
}

$user = $_SESSION['user'];

// Connexion à la base de données pour vérifier si l'utilisateur a une licence
require_once('../includes/dbconnect.php');
$connexion = dbconnect();
$stmt = $connexion->prepare("SELECT * FROM LICENCE WHERE username = :username");
$stmt->bindParam(':username', $user['username']);
$stmt->execute();
$licence = $stmt->fetch();

// Si l'utilisateur a déjà une licence, ne pas afficher le lien
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
</head>
<body>
    <h2>Profil de <?php echo htmlspecialchars($user['username']); ?></h2>
    <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
    <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['mail']); ?></p>
    <p><strong>Nom d'utilisateur:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <br>
    
    <?php if (!$licence): ?>
        <p>Envie de créer une licence sportive ? <a href="licence.php">Licence</a></p>
    <?php else: ?>
        <p>Vous avez déjà une licence sportive.</p>
    <?php endif; ?>

</body>
</html>

<?php
require('../includes/footer.php');
?>
