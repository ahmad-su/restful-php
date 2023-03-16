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

    global $env;
    //The user can craft their response body (in string) 
    //and pass it to Response::body function
    //They can craft the body somewhere (e.g. inside views/ folder)
    //and then call it here
    $body = json_encode($env);
    // FrameworkXYZ\Response::body(ContentType::Json, json_encode(['Message' => "It's RESTful"]));
    FrameworkXYZ\Response::body(ContentType::Json, $body);
  }
}

namespace handlers\account {

  use FrameworkXYZ;
  use FrameworkXYZ\ContentType;

  function get_account()
  {
    global $db_conn;
    // $db_conn = new \PDO("pgsql:host=localhost;port=5432;dbname=hendz_db;user=hendz;password=hendz123");
    $query = ($db_conn->query("select * from account;"))->fetchAll();
    // $result = $query->fetchAll();
    FrameworkXYZ\Response::body(ContentType::Json, json_encode($query));
  }
}
