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

        //We  craft a response body (in string)
        //and pass it to Response::body function
        //we can craft the body somewhere (e.g. inside views/ folder)
        //and then call it here

        // var_dump($_SERVER);
        $body = json_encode(["aboutUs" => "We are cool!"]);
        FrameworkXYZ\Response::body(ContentType::Json, $body);
    }
}

namespace handlers\accounts {

    require_once __DIR__ . '/../models/object_mapping.php';

    use FrameworkXYZ;
    use FrameworkXYZ\ContentType;
    use FrameworkXYZ\DBManager;
    use FrameworkXYZ\MemoryManager;
    use FrameworkXYZ\Security;

    function POST()
    {
        //Restrict this endpoint
        //to only serve request from given addresses:
        //making not anybody could populate new account
        Security::allow_address(["127.0.0.1", "::1"]);

        //bring database connection (object) into this scope
        global $db_conn;

        //parse request body and map using the given class:
        //note: if the request body ddidn't contain the correct key
        //or the correct value, the request will be rejected (with 400 code)
        $req_body = FrameworkXYZ\Request::parse("models\serialize\Account");

        list($username, $password, $email) = [$req_body->username, $req_body->password, $req_body->email];
        $query = DBManager::query($db_conn, "insert into account (username, password, email) values ('$username' , '$password', '$email') returning username, email;");
        MemoryManager::drop([&$email, &$password, &$req_body, &$username]);
        // var_dump($username); //should return null
        // var_dump($email); //should return null
        // var_dump($password); //should return null
        // var_dump($username); //should return null
        $result = DBManager::fetchOneJson($query, "models\deserialize\Account");
        FrameworkXYZ\Response::body(ContentType::Json, $result);
    }
}

namespace handlers\posts {

    use FrameworkXYZ;
    use FrameworkXYZ\ContentType;

    function GET()
    {
        global $db_conn;
        // $db_conn = new \PDO("pgsql:host=localhost;port=5432;dbname=hendz_db;user=hendz;password=hendz123");
        $query = ($db_conn->query("select * from article where is_published = true;"))->fetchAll();
        // $result = $query->fetchAll();
        FrameworkXYZ\Response::body(ContentType::Json, json_encode($query));
    }
}

namespace handlers\authors\posts {

    use FrameworkXYZ;
    use FrameworkXYZ\ContentType;

    function GET(array $dyn_paths)
    {
        global $db_conn;
        $author_id = $dyn_paths[0];
        // $db_conn = new \PDO("pgsql:host=localhost;port=5432;dbname=hendz_db;user=hendz;password=hendz123");
        $query = $db_conn->query("select * from article where is_published = true and author_id = '$author_id';");
        // var_dump($query);
        $result = $query->fetchAll();
        // var_dump($result);
        FrameworkXYZ\Response::body(ContentType::Json, json_encode($result));
    }
}
