<?php
$serveur = "localhost";
$utilisateur = "root";
$motDePasse = ""; 
$baseDeDonnees = "employees"; 

$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);


if ($connexion->connect_error) {
    die("Échec de la connexion : " . $connexion->connect_error);
}
?>
