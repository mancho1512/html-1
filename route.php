<?php

include_once('db.php');

$slug = explode("?", $_SERVER["REQUEST_URI"])[0];

$routes = [
    '/' => 'PHP/accueil.php'
];

// Vérifier si l'URL correspond à une route définie
if (array_key_exists($slug, $routes)) {
    // Inclure le fichier correspondant à la route
    include $routes[$slug];
} else {
    // Inclure la page 404
    include '404.html';
    // Optionnel : Envoyer un header HTTP 404
    header("HTTP/1.0 404 Not Found");
}
?>