<?php

namespace FrameworkXYZ {

    use Exception;
    use PDO;
    use PDOException;
    use ReflectionProperty;

    ;

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

        public function addRoute(string $method, string $uri, string $handler)
        {
            //escape special char (add backslash)
            $uri = preg_replace("/\//", "\\/", $uri);

            //if uri contain dynamic path: e.g {any}
            //replace any {value} in the uri to regex pattern to match its value e.g: 123 in "/path/123/path"
            if (preg_match("/(\{[a-zA-Z0-9-_]{1,255}\})/", $uri)) {
                $uri = preg_replace("/(\{[a-zA-Z0-9-_]{1,255}\})/", "([a-zA-Z0-9-_]{1,255})", $uri);
            }

            if (!array_key_exists($uri, $this->routes)) {
                $array = [$method => $handler];
                $this->routes[$uri] =  $array;
            } else {
                $this->routes[$uri][$method] =  $handler;
            }
        }

        public function serve()
        {
            set_exception_handler(function ($e) {

                $error_code = $e->getCode() ?? 500;
                $body = json_encode(["error" => $e->getMessage()]);
                header("Content-Type: application/json", true, $error_code);
                Response::body(ContentType::Json, $body);
            });

            $method = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];

            //Remove X-Powered-By header for security
            header('X-Powered-By:');
            header_remove('X-Powered-By');

            $route_found = false;
            foreach (array_keys($this->routes) as $route) {
                if (preg_match("/$route\z/", $uri)) {
                    $route_found = true;
                    // echo "Success !!!";
                    // var_dump($routes[$route]);
                    //TODO: Add logic to parse dynamic path value
                    //PLAN: split static paths from the uri and then craft new regex to parse the dynamics
                    $dyn_expr_sign = "/(\(\[a-zA-Z0-9-_\]\{1,255\}\))/";
                    $static_paths = array();
                    $dyn_values = array();
                    if (preg_match($dyn_expr_sign, $route)) {
                        $static_paths = preg_split($dyn_expr_sign, $route);
                        //add () to each static path
                        //I just amazed php has closure :D
                        $static_paths = array_map(fn ($str): string => "(" . $str . ")", $static_paths);
                        //craft the expr that we will use to extract dynamic paths
                        $extractor_expr = '';
                        for ($i = 0; $i < count($static_paths); $i++) {
                            if ($i + 1 == count($static_paths)) {
                                $extractor_expr .= "$static_paths[$i]";
                            } else {
                                $extractor_expr .= "$static_paths[$i]|";
                            }
                        }
                        // echo "\r\n$extractor_expr\r\n";
                        $dyn_values = preg_split("/(?:$extractor_expr)/", $uri);
                        // var_dump($dyn_paths);

                        $dyn_values = array_values(array_filter($dyn_values));

                        // var_dump($static_paths);
                        // var_dump($dyn_paths);
                    }
                    if (!array_key_exists($method, $this->routes[$route])) {
                        // var_dump($this->routes);
                        throw new Exception("Method not allowed", 400);
                    } else {
                        header("Content-type: application/json");
                        // var_dump($this->routes);
                        if (!is_null($dyn_values)) {

                            $this->routes[$route][$method]($dyn_values);
                        } else {
                            $this->routes[$route][$method]();
                        }
                    }
                }
            }
            if (!$route_found) {
                throw new Exception("Unknown endpoint", 404);
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
        public static function get_client_ip()
        {
            $client_address = [
              $_SERVER['HTTP_CLIENT_IP'] ?? null,
              $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
              $_SERVER['HTTP_X_FORWARDED'] ?? null,
              $_SERVER['HTTP_FORWARDED_FOR'] ?? null,
              $_SERVER['HTTP_FORWARDED'] ?? null,
              $_SERVER['REMOTE_ADDR'] ?? null
            ];
            foreach ($client_address as $ip_addr) {
                if (isset($ip_addr)) {
                    return $ip_addr;
                }
            }
            $client_address = null;
            return 'UNKNOWN';
        }

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
        public static function readFile(string $file_path): array
        {
            if (!is_readable($file_path)) {
                throw new \Exception("The 'env' file  can not be reached");
            }
            $array = array();
            $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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

    class Security
    {
        public static function allow_address(array $addr)
        {

            $client_ip = Request::get_client_ip();
            if (!in_array($client_ip, $addr)) {
                throw new Exception("You are not allowed to perform this request.", 403);
            }
        }
    }
}
