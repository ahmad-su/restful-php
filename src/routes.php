<?php

namespace routes {

  function get_health_check()
  {
    $data = ["health-check" => "Success"];
    echo (json_encode($data));
  }
}
