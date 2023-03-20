<?php

namespace handlers {

  use FrameworkXYZ;
  use FrameworkXYZ\ContentType;

  require_once __DIR__ . '/database.php';

  function health_check()
  {
    //A health-check should return empty body,
    //Therefore, it's best to delete the Content-type header as well
    // header_remove("Content-type");
    FrameworkXYZ\Response::body(ContentType::None, "");
  }
  function about()
  {

    //The user can craft their response body (in string) 
    //and pass it to Response::body function
    //They can craft the body somewhere (e.g. inside views/ folder)
    //and then call it here
    $body = json_encode(["aboutUs" => "We are cool!"]);
    FrameworkXYZ\Response::body(ContentType::Json, $body);
  }
}

namespace handlers\account {

  require_once __DIR__ . '/../models/object_mapping.php';

  use Exception;
  use FrameworkXYZ;
  use FrameworkXYZ\ContentType;
  use FrameworkXYZ\DBManager;
  use FrameworkXYZ\MemoryManager;

  function add_account()
  {
    global $db_conn;
    // $db_conn = new \PDO("pgsql:host=localhost;port=5432;dbname=hendz_db;user=hendz;password=hendz123");
    try {
      $request_body = json_decode(file_get_contents("php://input"));
    } catch (Exception $e) {
      throw new Exception("Failed to parse request body. " . $e->getMessage());
    };
    $username = $request_body->username;
    $password = $request_body->password;
    $email = $request_body->email;
    // var_dump($request_body);
    $query = $db_conn->query("insert into account (username, password, email) values ('$username' , '$password', '$email') returning username, email;");
    MemoryManager::drop([&$email, &$password, &$request_body, &$username]);
    // var_dump($username); //should return null
    // var_dump($email); //should return null
    // var_dump($password); //should return null
    // var_dump($username); //should return null
    $result = DBManager::fetchOneJson($query, "models\decode\Account");
    FrameworkXYZ\Response::body(ContentType::Json, $result);
  }
}

namespace handlers\post {

  use FrameworkXYZ;
  use FrameworkXYZ\ContentType;

  function get_post()
  {
    global $db_conn;
    // $db_conn = new \PDO("pgsql:host=localhost;port=5432;dbname=hendz_db;user=hendz;password=hendz123");
    $query = ($db_conn->query("select * from article where is_published = true;"))->fetchAll();
    // $result = $query->fetchAll();
    FrameworkXYZ\Response::body(ContentType::Json, json_encode($query));
  }
}
