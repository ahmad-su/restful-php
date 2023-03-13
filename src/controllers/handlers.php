<?php

namespace handlers {

  use FrameworkXYZ;
  use FrameworkXYZ\ContentType;

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
    FrameworkXYZ\Response::body(ContentType::Json, json_encode(['Message' => "It's RESTful"]));
  }
}
