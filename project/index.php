<?php

define('ENTITY_ARTISTS', 'artists');
define('ENTITY_ALBUMS', 'albums');
define('ENTITY_TRACKS', 'tracks');
define('ENTITY_MEDIA_TYPES', 'media_types');
define('ENTITY_GENRES', 'genres');
define('ENTITY_PLAYLISTS', 'playlists');

require_once 'initialize.php';
require_once 'Utils.php';
require_once 'Classes/Database.php';
require_once 'Classes/Exceptions.php';

$url = $_SERVER['REQUEST_URI']; 
// If there is a trailing slash, it is removed, so that it is not taken into account by the explode function
if (substr($url, strlen($url) - 1) == '/') {
    $url = substr($url, 0, strlen($url) - 1);
}
// Everything up to the folder where this file exists is removed.
// This allows the API to be deployed to any directory in the server
$url = substr($url, strpos($url, basename(__DIR__)));

// Remove query string
$urlPieces = explode('?', urldecode($url))[0];

$urlPieces = explode('/', urldecode($urlPieces));

// remove /v1/api
array_splice($urlPieces, 0, 3);

header('Content-Type: application/json');
header('Accept-version: v1');
http_response_code(200);

$pieces = count($urlPieces);

if ($pieces > 4) {              // The route is more than four levels deep
    http_response_code(400);
    echo Utils::formatError();
} else if ($pieces === 0) {      // No entity is being passed to the route
    echo Utils::APIDescription();
} else {    
    switch ($urlPieces[0]) {
        case ENTITY_ARTISTS:
            // Remove /artists and keep everything after
            array_splice($urlPieces, 0, 1);
            require_once 'Classes/Handlers/ArtistsHandler.php';
            ArtistsHandler::handleArtists($urlPieces);
            break;
        default:
            http_response_code(400);
            echo Utils::formatError();
            break;
            //array_splice($urlPieces, 0, 1);
            //echo implode(" ", $urlPieces);
    }
}