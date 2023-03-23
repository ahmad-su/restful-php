<?php

//deserialize
namespace models\deserialize {
  class Account
  {
    public string $username;
    public string $email;
  }

  class Article
  {
    public string $title;
    public string $body;
    public string $author;
    public string $date_posted;
    public string $date_updated;
  }
}

//serialize
namespace models\serialize {
  class Account
  {
    public string $username;
    public string $password;
    public string $email;
  }

  class Article
  {
    public string $title;
    public string $body;
  }
}
