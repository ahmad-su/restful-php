### RESTful API in PHP
1. clone repo
2. go to cloned repo
3. run `php -S localhost:8080`
4. test the route using curl, run: `curl -i -X [HTTP METHOD] localhost:8080[URI]` example: `curl -i -X GET localhost:8080/health-chek`
5. perform integration test: [incoming]


###TODO: 
1. Refactor APP to use OOP paradigm and MVC pattern [OK, but still not satisfied]
2. Prepare DB (will use PG) [OK, containerized with docker]
3. add logic to perform CRUD to DB
4. create Object mapping.
5. add logic to handle telemetry data (logging)
6. add logic to handle session / authorization / authentication
7. add api documentation (like rapidoc/swaggerui)

Database migration info:
In this project I used `sqlx-cli` to manage database migration. [`cargo install sqlx-cli` to install it if you have cargo installed]
That may not be the convenience method in php community.
It's simply because sqlx-cli is already installed in my machine.

About the FrameworkXYZ:
I just haven't found a good name for this framework. I design this to be pattern agnostic. It's not tied to any architecturel pattern (like MVC / MVVC).
After I finished this project, I plan to extract and complete this "framework" based on use cases in this project.
Who knows, This framework may be useful for others

