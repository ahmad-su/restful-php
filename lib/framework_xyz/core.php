<?php

namespace FrameworkXYZ {

  use Exception;
  use PDO;
  use PDOException;;

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

  enum DBVendor
  {
    case Postgres;
    case Mysql;
  }
  class DBMan
  {
    private static $dbconn;

    public static function init(DBVendor $dbv, string $db_addr, string $db_name, string $db_user, string $db_password, bool $is_persistent = false)
    {
      $db_vendor = '';
      match ($dbv) {
        DBVendor::Postgres => $db_vendor = 'pgsql',
        DBVendor::Mysql => $db_vendor = 'mysql',
      };
      if (!isset($dbconn)) {
        try {
          self::$dbconn = new PDO("$db_vendor:host=$db_addr;dbname= $db_name;", $db_user, $db_password, array(\PDO::ATTR_PERSISTENT => $is_persistent));
        } catch (PDOException  $e) {
          throw new Exception("Couldn't connect to database. " . $e->getMessage());
        }
      }
      return self::$dbconn;
    }

    public static function fetchOneJson(\PDOStatement $query, string $class): string
    {
      return trim(json_encode($query->fetchAll(PDO::FETCH_CLASS, $class)), '[]');
    }
  }

  class MemoryManager
  {
    public static function drop(&$var1, &$var2 = null, &$var3 = null, &$var4 = null, &$var5 = null, &$var6 = null, &$var7 = null, &$var8 = null, &$var9 = null, &$var10 = null)
    {
      // if (empty($array)) {
      //   return;
      // }
      //
      // foreach ($array as $var) {
      //   $var = null;
      // }
      $var1 = null;
      $var2 = null;
      $var3 = null;
      $var4 = null;
      $var5 = null;
      $var6 = null;
      $var7 = null;
      $var8 = null;
      $var9 = null;
      $var10 = null;
    }
  }
}
