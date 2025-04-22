<?php 
// Constantes de connexion
define('USER', "root"); 
define('PASSWD', ""); 
define('SERVER', "localhost"); 
define('BASE', "rapidos"); 

// Fonction de connexion à la base de données
function dbconnect(){ 
  $dsn = "mysql:dbname=".BASE.";host=".SERVER; 
  try { 
    $connexion = new PDO($dsn, USER, PASSWD); 
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Active les exceptions pour les erreurs PDO
    $connexion->exec("set names utf8"); // Support utf8
  } catch(PDOException $e) { 
    printf("Échec de la connexion: %s\n", $e->getMessage()); 
    exit(); 
  } 
  return $connexion; 
}

?>
