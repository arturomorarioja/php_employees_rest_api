# Employees - REST API Example
Sample REST API. It retrieves employee and department information from the [test_db](https://github.com/datacharmer/test_db) test database.

## API Documentation

### Main usage:

http://_<base_url>_/_<end_point>_

### Endpoints:

| Method | Endpoint        | Description                         |
| ------ |:------------ |:----------------------------------- |
| GET    |/    | Returns the API description for GET methods     |
| GET    |/employees?range=_<range_number>_&sort=_<sort_field>_    | Returns employee data for the specified range sorted by the specified field     |
| GET    |/employees?name=_<search_text>_&range=_<range_number>_&sort=_<sort_field>_ | Returns employee data of those employees whose first or last name contains _<search_text>_ for the specified range sorted by the specified field |
| GET    |/departments?sort=_<sort_field>_ | Returns department data sorted by the specified field |

### Examples:

- GET http://localhost/php-mysql-employees-rest-api/
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=last_name
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=first_name
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=dept_name
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=gender
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=birth_date
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=hire_date
- GET http://localhost/php-mysql-employees-rest-api/employees?range=13&sort=salary
- GET http://localhost/php-mysql-employees-rest-api/employees?name=JoAnne
- GET http://localhost/php-mysql-employees-rest-api/departments?sort=name
- GET http://localhost/php-mysql-employees-rest-api/departments?sort=manager

### Sample Output:

Employees

```json
{
    "employees": [
        {
            "emp_no": "85120",
            "last_name": "Acton",
            "first_name": "Zeljko",
            "dept_name": "Development",
            "gender": "M",
            "birth_date": "08/11/1963",
            "hire_date": "06/04/1995",
            "salary": "59162"
        },
        {
            "emp_no": "408216",
            "last_name": "Acton",
            "first_name": "Shridhar",
            "dept_name": "Sales",
            "gender": "M",
            "birth_date": "29/06/1955",
            "hire_date": "18/07/1985",
            "salary": "62300"
        },
        ...
    ],
    "_links": [
        {
            "rel": "self",
            "href": "<server_path>/php-mysql-employees-rest-api/employees{?name=&range=&sort=}",
            "type": "GET"
        },
        {
            "rel": "departments",
            "href": "<server_path>/php-mysql-employees-rest-api/departments{?sort=}",
            "type": "GET"
        }
    ]
}
```

Departments

```json
{
    "departments": [
        {
            "dept_no": "d005",
            "dept_name": "Development",
            "manager": "DasSarma, Leon"
        },
        {
            "dept_no": "d004",
            "dept_name": "Production",
            "manager": "Ghazalie, Oscar"
        },
        {
            "dept_no": "d008",
            "dept_name": "Research",
            "manager": "Kambil, Hilary"
        },
        {
            "dept_no": "d002",
            "dept_name": "Finance",
            "manager": "Legleitner, Isamu"
        },
        {
            "dept_no": "d001",
            "dept_name": "Marketing",
            "manager": "Minakawa, Vishwani"
        },
        {
            "dept_no": "d006",
            "dept_name": "Quality Management",
            "manager": "Pesch, Dung"
        },
        {
            "dept_no": "d003",
            "dept_name": "Human Resources",
            "manager": "Sigstam, Karsten"
        },
        {
            "dept_no": "d009",
            "dept_name": "Customer Service",
            "manager": "Weedman, Yuchang"
        },
        {
            "dept_no": "d007",
            "dept_name": "Sales",
            "manager": "Zhang, Hauke"
        }
    ],
    "_links": [
        {
            "rel": "employees",
            "href": "<server_path>/php-mysql-employees-rest-api/employees{?name=&range=&sort=}",
            "type": "GET"
        },
        {
            "rel": "self",
            "href": "<server_path>/php-mysql-employees-rest-api/departments{?sort=}",
            "type": "GET"
        }
    ]
}
```

### Testing
The directory `postman` includes JSON files for an environment and a collection that can be imported to [Postman](https://www.postman.com/) to test the API .

## Tools
MariaDB / PHP8

## Author:
Arturo Mora-Rioja