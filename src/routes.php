<?php

namespace handlers {

  function health_check()
  {
    // $data = ["health-check" => "Success"];
    // $content_length = strlen(json_encode($data));
    // header("Content-Length: $content_length");
    // echo (json_encode($data));

    $body = "";
    $content_len = strlen($body);
    header("Content-Length: $content_len");
    echo "";
  }
}
