<?php
require_once __DIR__ . '/src/routes.php';

set_exception_handler(function ($e) {
  $error_code = $e->getCode() ?: 500;
  header("Content-Type: application/json", true, $error_code);
  echo json_encode(["error" => $e->getMessage()]);
});

$http_method = $_SERVER['REQUEST_METHOD'];
$http_uri = $_SERVER['REQUEST_URI'];
$data = ["health-check" => "OK, We are alive!"];
$routes = ["/health-check" => ["GET" => "routes\get_health_check"]];

if (!array_key_exists($http_uri, $routes)) {
  throw new Exception("Unknown endpoint", 404);
} else {
  if (!array_key_exists($http_method, $routes[$http_uri])) {
    throw new Exception("Method not allowed", 400);
  } else {
    header("Content-Type: application/json");
    $routes[$http_uri][$http_method]();
  }
}

//TODO: 
//1. Refactor APP to use OOP paradigm and MVC pattern
//2. Prepare DB (will use PG)
//3. add logic to perform CRUD to DB
//4. create Object mapping.
//5. add logic to handle telemetry data (logging)
//6. add logic to handle session / authorization / authentication
//
