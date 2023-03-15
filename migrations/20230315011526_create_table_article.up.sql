-- Add up migration script here
CREATE TABLE IF NOT EXISTS article (
  id bigserial PRIMARY KEY,
  title text UNIQUE NOT NULL,
  body text NOT NULL,
  date_posted timestamptz NOT NULL,
  date_updated timestamptz NOT NULL,
  is_published boolean NOT NULL,
  author_id serial NOT NULL,
  FOREIGN KEY(author_id) REFERENCES account(id)
)
