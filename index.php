<?php
require_once __DIR__ . '/src/routes.php';

set_exception_handler(function ($e) {
  //debugging
  // error_log($e->getMessage());

  $error_code = $e->getCode() ?: 500;
  $body = json_encode(["error" => $e->getMessage()]);
  $content_len = strlen($body);
  header("Content-Length: $content_len");
  header("Content-Type: application/json", true, $error_code);
  echo json_encode(["error" => $e->getMessage()]);
});

$http_method = $_SERVER['REQUEST_METHOD'];
$http_uri = $_SERVER['REQUEST_URI'];

$server = new Server();
$server->addRoute('GET', '/health-check', 'handlers\health_check');
// $server->addRoute('POST', '/health-check', 'handlers\health_check');
$server->serve($http_method, $http_uri);

class Server
{
  public $routes;

  public function __construct()
  {
    $this->routes = array();
  }

  public function addRoute(string $method, string $path, string $handler)
  {
    if (!array_key_exists($path, $this->routes)) {
      $array = [$method => $handler];
      $this->routes[$path] =  $array;
    } else {
      // $array = [$path => [$method => $handler]];
      $this->routes[$path][$method] =  $handler;
    }
  }

  public function serve(string $method, string $path)
  {

    if (!array_key_exists($path, $this->routes)) {
      // var_dump($this->routes);
      throw new Exception("Unknown endpoint", 404);
    } else {
      if (!array_key_exists($method, $this->routes[$path])) {
        // var_dump($this->routes);
        throw new Exception("Method not allowed", 400);
      } else {
        header("Content-Type: application/json");
        // var_dump($this->routes);
        $this->routes[$path][$method]();
      }
    }
  }
}
//TODO: 
//1. Refactor APP to use OOP paradigm and MVC pattern
//2. Prepare DB (will use PG)
//3. add logic to perform CRUD to DB
//4. create Object mapping.
//5. add logic to handle telemetry data (logging)
//6. add logic to handle session / authorization / authentication
//7. add api documentation
