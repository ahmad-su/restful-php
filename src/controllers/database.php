<?php

namespace database {

  use FrameworkXYZ\DBMan;
  use FrameworkXYZ\DBVendor;
  use FrameworkXYZ\Env;

  $env = Env::readFile(__DIR__ . '/../../.env');
  $db_conn = DBMan::init(DBVendor::Postgres, 'localhost', $env['DB_NAME'], $env['DB_USER'], $env['DB_PASSWD'], true);
  $env = null;
}
