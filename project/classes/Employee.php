<?php

require_once 'Database.php';
require_once 'Department.php';
require_once 'Logger.php';

#[AllowDynamicProperties]
Class Employee extends Database
{

    /**
     * It retrieves all employees from the database
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    function getAll(): array|false
    {
        $sql =<<<SQL
            SELECT nEmployeeID, cFirstName, cLastName, dBirth
            FROM employee
            ORDER BY cFirstName, cLastName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error getting all employees: ', $e);
            return false;
        }
    }

    /**
     * It retrieves employees from the database based 
     * on a text search on the first and last name
     * @param $searchText The text to search in the database
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    function search(string $searchText): array|false
    {
        $sql =<<<SQL
            SELECT nEmployeeID, cFirstName, cLastName, dBirth
            FROM employee
            WHERE cFirstName LIKE :firstName
            OR cLastName LIKE :lastName
            ORDER BY cFirstName, cLastName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstName', "%$searchText%");
            $stmt->bindValue(':lastName', "%$searchText%");
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error searching for employees: ', $e);
            return false;
        }
    }

    /**
     * It retrieves information of an employee
     * @param $employeeID The ID of the employee
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    function getByID(int $employeeID): array|false
    {
        $sql =<<<SQL
            SELECT 
                employee.cFirstName AS first_name, 
                employee.cLastName AS last_name, 
                employee.cEmail AS email, 
                employee.dBirth AS birth_date, 
                employee.nDepartmentID AS department_id, 
                department.cName AS department_name
            FROM employee INNER JOIN department
                ON employee.nDepartmentID = department.nDepartmentID
            WHERE nEmployeeID = :employeeID;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':employeeID', $employeeID);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving employee information: ', $e);
            return false;
        }
    }

    /**
     * It validates employee data before putting it into the database
     * @param $employee Employee data in an associative array
     * @return<array> An array with all validation error messages
     */
    function validate(array $employee): array
    {
        $firstName = trim($employee['first_name'] ?? '');
        $lastName = trim($employee['last_name'] ?? '');
        $email = trim($employee['email'] ?? '');
        $birthDate = trim($employee['birth_date'] ?? '');
        $departmentID = (int) ($employee['department'] ?? 0);
        
        $validationErrors = [];
        
        if ($firstName === '') {
            $validationErrors[] = 'First name is mandatory.';
        }
        if ($lastName === '') {
            $validationErrors[] = 'Last name is mandatory.';
        }
        if ($email === '') {
            $validationErrors[] = 'Email is mandatory.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validationErrors[] = 'Invalid email format.';
        }
        if ($birthDate === '') {
            $validationErrors[] = 'Birth date is mandatory.';
        } elseif (!DateTime::createFromFormat('Y-m-d', $birthDate)) {
            $validationErrors[] = 'Invalid birth date format.';
        } elseif (DateTime::createFromFormat('Y-m-d', $birthDate) > new DateTime('-16 years')) {
            $validationErrors[] = 'The employee must be at least 16 years old.';
        }
        if ($departmentID === 0) {
            $validationErrors[] = 'Department is mandatory.';
        } else {
            $department = new Department();
            if (!$department->getByID($departmentID)) {
                $validationErrors[] = 'The department does not exist.';
            }
        }
        
        return $validationErrors;
    }

    /**
     * It inserts a new employee in the database
     * @param $employee An associative array with employee information
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    function insert(array $employee): bool
    {
        $sql =<<<SQL
            INSERT INTO employee
                (cFirstName, cLastName, cEmail, dBirth, nDepartmentID)
            VALUES
                (:firstName, :lastName, :email, :birthDate, :departmentID);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstName', $employee['first_name']);
            $stmt->bindValue(':lastName', $employee['last_name']);
            $stmt->bindValue(':email', $employee['email']);
            $stmt->bindValue(':birthDate', $employee['birth_date']);
            $stmt->bindValue(':departmentID', $employee['department_id']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new employee: ', $e);
            return false;
        }
    }

    /**
     * It updates an employee in the database
     * @param $employee An employee object
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    function update(int $employeeID, Employee $employee): bool
    {
        $sql =<<<SQL
            UPDATE employee 
            SET cFirstName = :firstName, cLastName = :lastName, cEmail = :email, dBirth = :birthDate, nDepartmentID = :departmentID
            WHERE nEmployeeID = :employeeID
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstName', $employee->first_name);
            $stmt->bindValue(':lastName', $employee->last_name);
            $stmt->bindValue(':email', $employee->email);
            $stmt->bindValue(':birthDate', $employee->birth_date);
            $stmt->bindValue(':departmentID', $employee->department_id);
            $stmt->bindValue(':employeeID', $employeeID);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new employee: ', $e);
            return false;
        }
    }

    /**
     * It removes an employee from the database
     * @param $employeeID The ID of the employee
     * @return true if the delete was successful,
     *         or false if there was an error
     */
    function removeById(int $employeeID): bool
    {
        $sql =<<<SQL
            DELETE FROM employee
            WHERE nEmployeeID = :nEmployeeID;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nEmployeeID', $employeeID);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error deleting an employee: ', $e);
            return false;
        }
    }
}