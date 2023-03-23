<?php

namespace database {

  use FrameworkXYZ\DBManager;
  use FrameworkXYZ\DBVendor;
  use FrameworkXYZ\Env;

  $env = Env::readFile(__DIR__ . '/../../.env');
  $db_conn = DBManager::init(DBVendor::Postgres, 'localhost', $env['DB_NAME'], $env['DB_USER'], $env['DB_PASSWD'], true);
  $env = null;
}
