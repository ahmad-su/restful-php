Note: **I'll try** to update this document (README) to keep the information up to date with the current development stage.

### RESTful API in PHP
PREREQUISITES 
1. php and **php_pgsql** (extension to work with postgresql)installed
2. docker engine installed
3. sqlx-cli installed [to run sql migrations] or you can port and modify the migrations to use any migration tool you like

### Running
1. clone repo
2. go to cloned repo
3. create `.env` file from existing example
4. make sure docker daemon is running then run `docker compose up -d` to build and run containerized database
5. run migrations `sqlx migrate run -D postgres://USER:PASSWORD@localhost/DBNAME`
6. run `php -S localhost:8080`
7. test the endpoints manually using curl, run: `curl -i -X [HTTP METHOD] localhost:8080[URI]` example: `curl -i -X GET localhost:8080/health-chek`
8. or perform integration test: [incoming]


###TODO: 
1. Refactor APP to use OOP paradigm and MVC pattern [DONE, but still not satisfied]
2. Prepare DB (will use PG) [DONE, containerized using docker]
3. add logic to perform CRUD to DB [We are here!]
4. create Object mapping.
5. add logic to handle telemetry data (logging)
6. add logic to handle authorization / authentication
7. add api documentation (like rapidoc/swaggerui)

Database migration info:
In this project I used `sqlx-cli` to manage database migration. [`cargo install sqlx-cli` to install it if you have cargo installed]
That may not be the convenience method in php community.
I choose it simply because sqlx-cli is already installed in my machine.

About the FrameworkXYZ:
I just haven't found a good name for this framework. I design this to be pattern agnostic. (It's not tied to any architecturel pattern like MVC / MVVC (but will povide convenient way to build MVC)).
After I finished this project, I plan to extract and complete this "framework" based on use cases in this project.
Who knows, This framework may be useful for others

