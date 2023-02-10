# Installation
* use `app` script in terminal
* `./app start`
* `./app composer install`
* `./app doctrine:schema:create`
* `./app doctrine:schema:create --env=test`
* `./app console doctrine:fixtures:load`

# Testing
* `./app tests`

# Usage
* Add employee: `POST http://localhost:8888/employees` no payload
* Add business trip for employee: `POST http://localhost:8888/employees/{employeeId}/business-trips` with payload
```
{
    "startDate": "YYYY-MM-DD HH:MM:SS",
    "endDate": "YYYY-MM-DD HH:MM:SS",
    "countryCode": "XX"
}
```
* List business trips of employee: `GET http://localhost:8888/employees/{employeeId}/business-trips`