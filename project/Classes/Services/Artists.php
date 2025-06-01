<?php

Class Artist extends Database
{
    /**
     * It retrieves all artists from the database
     * @return An associative array with artist information,
     *         or false if there was an error
     */
    function list(): array|false
    {
        $sql =<<<SQL
            SELECT ArtistId, Name
            FROM Artist
            ORDER BY Name;
        SQL;

        return $this->fetch("Error getting all artists", $sql, null);
    }

    /**
     * It retrieves information of an artist
     * @param $artistID The ID of the artist
     * @return An associative array with artist information,
     *         or false if there was an error
     */
    function getByID(int $artistID): array|string
    {
        $sql =<<<SQL
            SELECT ArtistId, Name
            FROM Artist
            WHERE ArtistId = :artistID;
        SQL;

        try {
            return $this->fetch(
                $sql, 
                [new BindValues("artistID", $artistID)]
            );
        } catch (EmptyFetch) {
            return ['error' => "An artist with that ID does not exist."];
        } catch (\Throwable $th) {
            return ['error' => $th];
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

        return $this->fetch(
            "Error searching for artists",
            $sql,
            [new BindValues("name", $searchText)],
        );
    }

    /**
     * It inserts a new artist in the database
     * @param $artist An associative array with artist information
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    function insert(array $artist): string
    {
        $sql =<<<SQL
            INSERT INTO Artist
                (Name)
            VALUES
                (:name);
        SQL;

        return $this->queryInsertId(
            "Error inserting a new artist",
            $sql,
            [new BindValues("name", $artist['name'])]
        );
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

        $getAlbumsResult = $this->fetch(
            "Error getting albums for artist", 
            $sqlGetAlbums, 
            [new BindValues("artistID", $artistID)]
        );

        if (is_array($getAlbumsResult)) {
            if (count($getAlbumsResult) > 0) {
                return ['error' => "You cannot delete an artist that still has albums."];
            } else {
                return $this->query(
                    "Error deleting an artist",
                    $sqlDeleteArtist,
                    [new BindValues("artistID", $artistID)]
                ) === 1;
            }
        } else {
            return $getAlbumsResult;
        }
    }
}