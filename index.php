<?php 
/** 
 * Employees REST API
 * Refer to README.md for API documentation
 * 
 * @author  Arturo Mora-Rioja
 * @version 1.0.0 October 2020
 * @version 2.0.0 September 2021. HATEOAS links added
 *                                The API can now be served from any directory in the server
 * @version 2.0.1 December 2024. Refactoring
 *                               Code convention improved
 */

define('POS_ENTITY', 1);
define('ENTITY_EMPLOYEES', 'employees');
define('ENTITY_DEPARTMENTS', 'departments');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo formatError();
    exit;
}

$url = strtok($_SERVER['REQUEST_URI'], '?');    // GET parameters are removed
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

if ($pieces > 2) {              // The route is more than one level deep
    http_response_code(400);
    echo formatError();
} else if ($pieces == 1) {      // No entity is being passed to the route
    echo APIDescription();
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
            echo addHATEOAS($result, ENTITY_EMPLOYEES);
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
            echo addHATEOAS($result, ENTITY_DEPARTMENTS);

            break;
        default:
            http_response_code(400);
            echo formatError();
    }
}

/**
 * Returns the API's URL path
 */
function urlPath(): string {
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') 
        || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/';     
}

/**
 * Returns the REST API description
 */
function APIDescription(): string {
    return addHATEOAS();
}

/**
 * Adds HATEOAS links to the data it receives as a parameter
 * 
 * @param   $information    Entity information to add the HATEOAS links to
 * @param   $entity         Name of the entity the HATEOAS links will be added to.
 *                          If false, only the HATEOAS links will be returned
 * @return  The information to be served by the API including its 
 *          corresponding HATEOAS links encoded as JSON
 */
function addHATEOAS(array|string $information = null, string $entity = null): string {
    $curDir = urlPath();

    if (!is_null($entity)) {
        $apiInfo[$entity] = $information;
    }
    $apiInfo['_links'] = array(
        array(
            'rel' => ($entity == ENTITY_EMPLOYEES ? 'self' : ENTITY_EMPLOYEES),
            'href' => $curDir . ENTITY_EMPLOYEES . '{?name=&range=&sort=}',
            'type' => 'GET'
        ),
        array(
            'rel' => ($entity == ENTITY_DEPARTMENTS ? 'self' : ENTITY_DEPARTMENTS),
            'href' => $curDir . ENTITY_DEPARTMENTS . '{?sort=}',
            'type' => 'GET'
        )
    );        
    return json_encode($apiInfo);
}

/**
 * Returns a format error
 */
function formatError(): string {
    $output['message'] = 'Incorrect format';
    return addHATEOAS($output, '_error');
}