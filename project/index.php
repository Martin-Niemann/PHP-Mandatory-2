<?php

define('POS_ENTITY', 1);
//define('ENTITY_EMPLOYEES', 'employees');
//define('ENTITY_DEPARTMENTS', 'departments');

require_once 'classes/Utils.php';

$url = $_SERVER['REQUEST_URI']; 
// If there is a trailing slash, it is removed, so that it is not taken into account by the explode function
if (substr($url, strlen($url) - 1) == '/') {
    $url = substr($url, 0, strlen($url) - 1);
}
// Everything up to the folder where this file exists is removed.
// This allows the API to be deployed to any directory in the server
$url = substr($url, strpos($url, basename(__DIR__)));

$urlPieces = explode('/', urldecode($url));

header('Content-Type: application/json');
header('Accept-version: v1');
http_response_code(200);

$pieces = count($urlPieces);

if ($pieces > 5) {              // The route is more than five levels deep
    http_response_code(400);
    echo "you can't go this deep man";
    //echo Utils::formatError();
} else if ($pieces == 1) {      // No entity is being passed to the route
    echo "this is the root man";
    //echo Utils::APIDescription();
} else {    
    switch ($urlPieces[POS_ENTITY]) {
        case ENTITY_EMPLOYEES:
            require_once 'src/employee.php';

            $employee = new Employee();

            // By default, the first range will be retrieved
            if (isset($_GET['range'])) {
                $range = $_GET['range'];
            } else {
                $range = 1;
            }

            // By default, data will be retrieved sorted by last name
            if (isset($_GET['sort'])) {
                $sortField = $_GET['sort'];

                if (!in_array($sortField, ['last_name', 'first_name', 'dept_name', 'gender', 'birth_date', 'hire_date', 'salary'])) {
                    $sortField = 'last_name';
                }    
            } else {
                $sortField = 'last_name';
            }    

            if (isset($_GET['name'])) {     // A search
                $result = $employee->search($_GET['name'], $range, $sortField);
            } else {                        // A list
                $result = $employee->list($range, $sortField);
            }
            if (isset($result['error'])) {
                http_response_code(500);
            }
            echo Utils::addHATEOAS($result, ENTITY_EMPLOYEES);
            $employee = null;

            break;
        case ENTITY_DEPARTMENTS:
            require_once('src/department.php');

            $department = new Department();

            // By default, data will be retrieved sorted by department name
            if (isset($_GET['sort'])) {
                $sortField = $_GET['sort'];

                if (!in_array($sortField, ['name', 'manager'])) {
                    $sortField = 'name';
                }
            } else {
                $sortField = 'name';
            }

            $result = $department->list($sortField);
            if (isset($result['error'])) {
                http_response_code(500);
            }
            echo Utils::addHATEOAS($result, ENTITY_DEPARTMENTS);

            break;
        default:
            http_response_code(400);
            echo Utils::formatError();
    }
}