<?php

require_once 'Database.php';

require_once 'Logger.php';

Class Project extends Database
{
    /**
     * It retrieves all projects from the database
     * @return An associative array with project information,
     *         or false if there was an error
     */
    function getAll(): array|false
    {
        $sql =<<<SQL
            SELECT nProjectID, cName
            FROM project
            ORDER BY cName
        SQL;
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error getting all projects: ', $e);
            return false;
        }
    }

    /**
     * It retrieves the project asociated with an id
     * @param $employeeID The ID of the project
     * @return A Project with project name and id,
     *         or false if there was an error
     */
    function getByID(int $projectID): Project|false
    {
        $sql =<<<SQL
            SELECT 
                nProjectID AS project_id, 
                cName AS project_name
            WHERE nProjectID = :projectID
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving project information: ', $e);
            return false;
        }
    }

    /**
     * It retrieves all projects an employee is assigned to
     * @param $employeeID The ID of the employee
     * @return An associative array with project name and id,
     *         or false if there was an error
     */
    function getByEmployeeID(int $employeeID): array|false
    {
        $sql =<<<SQL
            SELECT 
                project.nProjectID AS project_id, 
                project.cName AS project_name
            FROM project INNER JOIN emp_proy
                ON project.nProjectID = emp_proy.nProjectID
            WHERE emp_proy.nEmployeeID = :employeeID
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':employeeID', $employeeID);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving project information: ', $e);
            return false;
        }
    }

    /**
     * It updates a project in the database
     * @param $projectID A project ID
     * @param $project An project object
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    function update(int $projectID, Project $project): bool
    {
        $sql =<<<SQL
            UPDATE project 
            SET cName = :project_name
            WHERE nProjectID = :projectID
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':project_name', $project->project_name);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new employee: ', $e);
            return false;
        }
    }
}