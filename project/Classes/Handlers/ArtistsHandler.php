<?php

require_once 'Classes/Services/Artists.php';

Class ArtistsHandler {
    public static function handleArtists($urlPieces) {
        if (count($urlPieces) === 0) {
            self::root();
        } else {
            self::withID($urlPieces);
        }
    }

    private static function root() {
        $artist = new Artist();

        if (isset($_GET['s'])) {     
            $result = $artist->search($_GET['s']);
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = json_decode(file_get_contents('php://input'), true);
            $insertId = $artist->insert($_POST);
            $result = $artist->getByID($insertId);
            http_response_code(201);
        }
        else {                        
            $result = $artist->list();
        }

        if (isset($result['error'])) {
            http_response_code(500);
        }
        echo Utils::addHATEOAS($result, ENTITY_ARTISTS);
    }

    private static function withID(array $urlPieces) {
        $artist = new Artist();
        $artistID = $urlPieces[0];

        if (array_key_exists("albums", $urlPieces)) {
            echo "oh shit there's more than just an ID";
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $result = $artist->removeById($artistID);
            } else {
                $result = $artist->getByID($artistID);
            }
        }
        echo Utils::addHATEOAS($result, ENTITY_ARTISTS);
    }
}