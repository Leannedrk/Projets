<?php
require('dbconnect.php');

//Load Session Variables
session_start();

// Vérifier si l'utilisateur a demandé la déconnexion
if (isset($_GET['disconnect']) && $_GET['disconnect'] == 1) {
    session_destroy();
    header("Location: /rapidos/index.php");
    exit();
}

// Vérifier si l'utilisateur est connecté
$loggedIn = isset($_SESSION['user']);
?>

<html lang="fr">
<head>
    <title>RAPIDOS</title>
    <link rel="icon" href="../logos/logo_rapidos.png" type="image">    <link rel="icon" href="logos/logo_rapidos.png" type="image">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">

    <!-- Font Awesome CSS -->
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.3.1/css/all.css'>

    <link rel="stylesheet" href="/rapidos/css/style.css">
    <script src="/rapidos/js/script.js"></script>

    <script>
        /** Display authentication modal form : */
        function authenticate() {
            // Display loginModal div and display it
            let modal = document.getElementById('loginModal');
            modal.style.display='block';
        }

        /** Disconnection */
        function disconnect() {
            window.location.href = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" + '?disconnect=1';
        }
    </script>
</head>


<body>
    <!-- NAV BAR -->

    <div class="navbar">
        <ul>
            <li class="dropdown">
                <a href="#">LE CLUB</a>
                <div class="dropdown-content">
                    <a href="/rapidos/pages/evenements.php">EVENEMENTS</a>
                    <a href="/rapidos/pages/partenaires.php">PARTENAIRES</a>
                    <a href="/rapidos/pages/docs.php">DOCUMENTS</a>
                </div>
            </li>        
            <li class="dropdown">
                <a href="#">COMPETITIONS</a>
                <div class="dropdown-content">
                    <a href="/rapidos/pages/competition.php">A VENIR</a>
                    <a href="/rapidos/pages/resultats.php">RESULTATS</a>
                </div>
            </li>
        <a href="/rapidos/index.php"><img src="/rapidos/logos/banniere_rapidos.png" alt="Logo" class="navbar-logo"></a>
        <li><a href="/rapidos/pages/media.php">MEDIAS</a></li>
        <?php 
        if ($loggedIn) { 
            ?>
            <li class="dropdown">
            <a href="#">MON COMPTE</a>
            <div class="dropdown-content">
            <a href="/rapidos/pages/profil.php">MON PROFIL</a>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['admin'] == 1) { ?>
                <a href="/rapidos/pages/admin_licence.php">GERER LICENCES</a>
            <?php } ?>
            <a href="#" onclick="disconnect();">DECONNEXION</a>
            </div>
            </li>
            <?php
        }
        else{
            ?>
            <li><a href="/rapidos/pages/connection.php" onclick="authenticate();">SE CONNECTER</a></li>
            <?php
        }
        ?>
            
    </ul>

    
    </div>
    <br>
    <br>
    <br>

    
