<?php

require_once 'Database.php';
require_once 'Department.php';

Class Artist extends Database
{
    /**
     * It retrieves all artists from the database
     * @return An associative array with artist information,
     *         or false if there was an error
     */
    function getAll(): array|false
    {
        $sql =<<<SQL
            SELECT ArtistId, Name
            FROM Artist
            ORDER BY Name;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error getting all artists: ', $e);
            return false;
        }
    }

    /**
     * It retrieves information of an artist
     * @param $artistID The ID of the artist
     * @return An associative array with artist information,
     *         or false if there was an error
     */
    function getByID(int $artistID): array|false
    {
        $sql =<<<SQL
            SELECT ArtistId, Name
            FROM Artist
            WHERE ArtistId = :artistID;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':artistID', $artistID);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving artist information: ', $e);
            return false;
        }
    }

    /**
     * It retrieves artists from the database based 
     * on a text search on the artist name
     * @param $searchText The text to search in the database
     * @return An associative array with artist information,
     *         or false if there was an error
     */
    function search(string $searchText): array|false
    {
        $sql =<<<SQL
            SELECT ArtistId, Name
            FROM Artist
            WHERE Name LIKE :name
            ORDER BY Name;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', "%$searchText%");
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error searching for artists: ', $e);
            return false;
        }
    }

    /**
     * It inserts a new artist in the database
     * @param $artist An associative array with artist information
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    function insert(array $artist): bool
    {
        $sql =<<<SQL
            INSERT INTO Artist
                (Name)
            VALUES
                (:name);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $artist['name']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new artist: ', $e);
            return false;
        }
    }

    /**
     * It removes an artist from the database if the artist has no albums
     * @param $artistID The ID of the artist
     * @return true if the delete was successful,
     *         or false if there was an error
     */
    function removeById(int $artistID): array|bool
    {
        $sqlGetAlbums =<<<SQL
            SELECT Album.AlbumId, Album.Title, Album.ArtistId 
            FROM Album 
            JOIN Artist 
            ON Album.ArtistId = Artist.ArtistId 
            WHERE Album.ArtistId = :artistID;
        SQL;

        $sqlDeleteArtist =<<<SQL
                DELETE FROM Artist
                WHERE ArtistId = :artistID;
            SQL;

        try {
            $stmt = $this->pdo->prepare($sqlGetAlbums);
            $stmt->bindValue(':artistID', $artistID);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['error' => "You cannot delete an artist that still has albums."];
            } else {
                $stmt = $this->pdo->prepare($sqlDeleteArtist);
                $stmt->bindValue(':artistID', $artistID);
                $stmt->execute();
                
                return $stmt->rowCount() === 1;   
            }
        } catch (PDOException $e) {
            Logger::logText('Error deleting an artist: ', $e);
            return false;
        }
    }
}