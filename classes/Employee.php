<?php
/**
 * Employee class
 * 
 * @author Arturo Mora-Rioja
 * @version 1.0 August 2020
 */
require_once 'Connection.php';

define('NUM_ROWS', 25);

Class Employee 
{
    const ERROR_CONN = 'There was an error while trying to connect to the database';
    const ERROR_QUERY = 'There was an error while executing a query: ';

    /**
     * Retrieves information of employees
     * 
     * @param  the range number of rows to return, in groups of 25
     * @param  field by which to sort the retrieved information. None if an empty string
     * @return array an array with employee information or an error message if there was an error
     */
    public function list(int $range, string $sort = ''): array
    {
        $db = new DB();
        $con = $db->connect();
        if ($con) {
            $offset = ($range - 1) * NUM_ROWS;
            $query = <<<'SQL'
                SELECT employees.emp_no, employees.last_name, employees.first_name, 
                    departments.dept_name, employees.gender, 
                    DATE_FORMAT(employees.birth_date, "%d/%m/%Y") as birth_date, 
                    DATE_FORMAT(employees.hire_date, "%d/%m/%Y") as hire_date, 
                    salaries.salary
                FROM employees 
                    LEFT JOIN dept_emp ON employees.emp_no = dept_emp.emp_no 
                    LEFT JOIN departments ON dept_emp.dept_no = departments.dept_no
                    LEFT JOIN salaries ON employees.emp_no = salaries.emp_no
                WHERE dept_emp.from_date <= NOW() 
                  AND dept_emp.to_date > NOW()
                  AND salaries.from_date <= NOW() 
                  AND salaries.to_date > NOW()
            SQL;
            
            switch ($sort) {
                case 'last_name':
                    $query .= 'ORDER BY employees.last_name';
                    break;
                case 'first_name':
                    $query .= 'ORDER BY employees.first_name';
                    break;
                case 'department':
                    $query .= 'ORDER BY departments.dept_name';
                    break;
                case 'gender':
                    $query .= 'ORDER BY employees.gender';
                    break;
                case 'birth_date':
                    $query .= 'ORDER BY employees.birth_date';
                    break;
                case 'hire_date':
                    $query .= 'ORDER BY employees.hire_date';
                    break;
                case 'salary':
                    $query .= 'ORDER BY salaries.salary';
                    break;
            }

            $query .= ' LIMIT ' . NUM_ROWS . " OFFSET $offset;";

            try {
                $stmt = $con->query($query);                
                $db->disconnect($con);
                
                return $stmt->fetchAll();                
            } catch (PDOException $e) {
                $db->disconnect($con);
                return ['error' => self::ERROR_QUERY . $e->getMessage()];
            }
        } else {
            return ['error' => self::ERROR_CONN];
        }
    }

    /**
     * Retrieves the employees whose first or last name includes a certain text
     * 
     * @param  text upon which to execute the search
     * @param  the range number of rows to return, in groups of 25
     * @param  field by which to sort the retrieved information. None if an empty string
     * @return array an array with employee information or an error message if there was an error
     */
    public function search(string $searchText, int $range, string $sort = ''): array
    {
        $db = new DB();
        $con = $db->connect();
        if ($con) {
            $offset = ($range - 1) * NUM_ROWS;
            $query = <<<'SQL'
                SELECT employees.emp_no, employees.last_name, employees.first_name, 
                    departments.dept_name, employees.gender, 
                    DATE_FORMAT(employees.birth_date, "%d/%m/%Y") as birth_date, 
                    DATE_FORMAT(employees.hire_date, "%d/%m/%Y") as hire_date, salaries.salary
                FROM employees 
                    LEFT JOIN dept_emp ON employees.emp_no = dept_emp.emp_no 
                    LEFT JOIN departments ON dept_emp.dept_no = departments.dept_no
                    LEFT JOIN salaries ON employees.emp_no = salaries.emp_no
                WHERE dept_emp.from_date <= NOW() 
                  AND dept_emp.to_date > NOW()
                  AND salaries.from_date <= NOW() 
                  AND salaries.to_date > NOW()
                  AND (
                    employees.last_name LIKE ? 
                    OR employees.first_name LIKE ?
                )
            SQL;

            switch ($sort) {
                case 'last_name':
                    $query .= 'ORDER BY employees.last_name';
                    break;
                case 'first_name':
                    $query .= 'ORDER BY employees.first_name';
                    break;
                case 'department':
                    $query .= 'ORDER BY departments.dept_name';
                    break;
                case 'gender':
                    $query .= 'ORDER BY employees.gender';
                    break;
                case 'birth_date':
                    $query .= 'ORDER BY employees.birth_date';
                    break;
                case 'hire_date':
                    $query .= 'ORDER BY employees.hire_date';
                    break;
                case 'salary':
                    $query .= 'ORDER BY salaries.salary';
                    break;
            }

            $query .= ' LIMIT ' . NUM_ROWS . " OFFSET $offset;";

            try {
                $stmt = $con->prepare($query);
                $stmt->execute(['%' . $searchText . '%', '%' . $searchText . '%']);                                
                $db->disconnect($con);
                return $stmt->fetchAll();                
            } catch (PDOException $e) {
                $db->disconnect($con);
                return ['error' => self::ERROR_QUERY . $e->getMessage()];
            }
        } else {
            return ['error' => self::ERROR_CONN];
        }
    }
}