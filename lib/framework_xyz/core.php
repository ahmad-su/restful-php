<?php

namespace FrameworkXYZ {

  use Exception;
  use PDO;
  use PDOException;
  use ReflectionProperty;;

  class Server
  {
    private $routes;
    private static $instance;

    private function __construct()
    {
      $this->routes = array();
    }

    public static function init()
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
        $this->routes[$path][$method] =  $handler;
      }
    }

    public function serve()
    {
      set_exception_handler(function ($e) {

        $error_code = $e->getCode() ?: 500;
        $body = json_encode(["error" => $e->getMessage()]);
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
    case Xml;
    case Form;
  }

  class Request
  {
    public static function parse(string $class)
    {
      $request_body = json_decode(file_get_contents("php://input"));
      if (is_null($request_body)) {
        throw new Exception("Failed to parse request body.", 400);
      }

      $classProps = get_class_vars($class);
      foreach (array_keys($classProps) as $prop) {
        if (!property_exists($request_body, $prop)) {
          throw new Exception("Field '$prop' is missing from request body.", 400);
        }
        $reflection = new ReflectionProperty($class, $prop);
        if (gettype($request_body->$prop) != $reflection->getType()->getName()) {
          throw new Exception("Field '$prop' is assigned with an unexpected value.", 400);
        }
      }
      return $request_body;
    }
  }
  class Response
  {
    public static function body(ContentType $type, string $body)
    {
      match ($type) {
        ContentType::Html => header('Content-type: text/html'),
        ContentType::Json => header('Content-type: application/json'),
        ContentType::None => header_remove('Content-type'),
        default => header_remove('Content-type')
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

  enum DBVendor
  {
    case Postgres;
    case Mysql;
  }

  class DBManager
  {
    private static $dbconn;

    public static function init(DBVendor $dbv, string $db_addr, string $db_name, string $db_user, string $db_password, bool $is_persistent = false)
    {
      $db_vendor = match ($dbv) {
        DBVendor::Postgres => 'pgsql',
        DBVendor::Mysql => 'mysql',
        default => 'mysql',
      };
      if (!isset(self::$dbconn)) {
        try {
          self::$dbconn = new PDO("$db_vendor:host=$db_addr;dbname= $db_name;", $db_user, $db_password, array(\PDO::ATTR_PERSISTENT => $is_persistent));
        } catch (PDOException  $e) {
          throw new Exception("Couldn't establish database connection. " . $e->getMessage());
        }
      }
      return self::$dbconn;
    }

    public static function query(\PDO $dbconn, string $query): \PDOStatement
    {
      try {
        return $dbconn->query($query);
      } catch (\PDOException $e) {
        $messages = explode(':', $e->getMessage());
        $message = trim($messages[1]) . ". " . trim($messages[4]);
        $messages = null;
        throw new Exception($message, 400);
      }
    }

    public static function fetchOneJson(\PDOStatement $query, string $class): string
    {
      $json = self::fetchAllJson($query, $class);
      return substr($json, 1, (strlen($json) - 2));
    }
    public static function fetchAllJson(\PDOStatement $query, string $class): string
    {
      return json_encode($query->fetchAll(PDO::FETCH_CLASS, $class));
    }
  }

  class MemoryManager
  {
    public static function drop(array $vars)
    {
      if (empty($vars)) {
        return;
      }
      foreach ($vars as &$var) {
        $var = null;
      }
    }
  }
}
