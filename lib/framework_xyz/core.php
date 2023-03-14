<?php

namespace FrameworkXYZ {

  use Exception;

  class Server
  {
    private $routes;
    private static $instance;

    private function __construct()
    {
      $this->routes = array();
    }

    public static function new()
    {
      if (!isset(self::$instance)) {
        self::$instance = new Server();
      }
      return self::$instance;
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
        // $content_len = strlen($body);
        // header("Content-Length: $content_len");
        // echo json_encode(["error" => $e->getMessage()]);
        header("Content-Type: application/json", true, $error_code);
        Response::body(ContentType::Json, $body);
      });

      $method = $_SERVER['REQUEST_METHOD'];
      $path = $_SERVER['REQUEST_URI'];

      //Remove X-Powered-By header for security
      header('X-Powered-By:');
      header_remove('X-Powered-By');

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

  class Env
  {
    public static function readFile(string $path): array
    {
      if (!is_readable($path)) {
        throw new \Exception("The 'env' file  can not be reached");
      }
      $array = array();
      $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
          continue;
        }
        list($var, $value) = explode('=', $line, 2);
        $array[trim($var)] = trim($value);
      }

      return $array;
    }
  }
}
