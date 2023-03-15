-- Add up migration script here
CREATE TABLE IF NOT EXISTS account (
  id bigserial PRIMARY KEY,
  username VARCHAR (32) UNIQUE NOT NULL,
  password VARCHAR (50) NOT NULL,
  email VARCHAR (255) UNIQUE NOT NULL
);
