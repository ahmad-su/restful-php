<?php

namespace FrameworkXYZ {

  use Exception;

  class Server
  {
    private $routes;

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

    public function serve()
    {
      set_exception_handler(function ($e) {

        $error_code = $e->getCode() ?: 500;
        $body = json_encode(["error" => $e->getMessage()]);
        $content_len = strlen($body);
        header("Content-Type: application/json", true, $error_code);
        header("Content-Length: $content_len");
        echo json_encode(["error" => $e->getMessage()]);
      });

      $method = $_SERVER['REQUEST_METHOD'];
      $path = $_SERVER['REQUEST_URI'];

      if (!array_key_exists($path, $this->routes)) {
        // var_dump($this->routes);
        throw new Exception("Unknown endpoint", 404);
      } else {
        if (!array_key_exists($method, $this->routes[$path])) {
          // var_dump($this->routes);
          throw new Exception("Method not allowed", 400);
        } else {
          header("Content-type: application/json");
          // var_dump($this->routes);
          $this->routes[$path][$method]();
        }
      }
    }
  }

  enum ContentType
  {
    case Json;
    case Html;
    case None;
  }
  class Response
  {
    public static function body(ContentType $type, string $body)
    {
      match ($type) {
        ContentType::Html => header('Content-type: text/html'),
        ContentType::Json => header('Content-type: application/json'),
        ContentType::None => header_remove('Content-type')
      };

      $content_length = strlen($body);
      header("Content-Length: $content_length");
      echo $body;
    }
  }
}
