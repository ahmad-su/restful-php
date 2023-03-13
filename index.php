<?php
require_once __DIR__ . '/lib/framework_xyz/engine.php';
require_once __DIR__ . '/src/controllers/handlers.php';

$server = new FrameworkXYZ\Server();
$server->addRoute('GET', '/health-check', 'handlers\health_check');
$server->addRoute('GET', '/about', 'handlers\about');
$server->serve();

//TODO: 
//1. Refactor APP to use OOP paradigm and MVC pattern
//2. Prepare DB (will use PG)
//3. add logic to perform CRUD to DB
//4. create Object mapping.
//5. add logic to handle telemetry data (logging)
//6. add logic to handle session / authorization / authentication
//7. add api documentation
