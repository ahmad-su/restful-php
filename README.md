### RESTful API in PHP
1. clone repo
2. go to cloned repo
3. run `php -S localhost:8080`
4. test the route using curl, run: `curl -i -X [HTTP METHOD] localhost:8080[URI]` example: `curl -i -X GET localhost:8080/health-chek`
5. perform integration test: [incoming]


###TODO: 
1. Refactor APP to use OOP paradigm and MVC pattern
2. Prepare DB (will use PG)
3. add logic to perform CRUD to DB
4. create Object mapping.
5. add logic to handle telemetry data (logging)
6. add logic to handle session / authorization / authentication
7. add api documentation (like rapidoc/swaggerui)
