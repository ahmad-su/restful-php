-- Add up migration script here
BEGIN;

  INSERT INTO account (username, password, email)
    values ('hendz','hendz12345', 'hendz-su@php.example'),
    ('ahmad-su','ahmad-su12345', 'ahmad-su@rust.example')
    ;
  INSERT INTO article (title, body, date_posted, date_updated, is_published, author_id)
    values ('coding is awesome', 'Coding is awesome. You should start coding right now to see its awesomeness.', NOW(), NOW(), true, (select id from account where username = 'hendz')),
    ('php is easy', 'Coding in php is easy. You should start coding in php right now to see its easyness.', NOW(), NOW(), false, (select id from account where username = 'hendz')),
    ('rust is powerful', 'Coding in rust will give you more power and elegance because rust is simply awesome. You should start coding in rust right now to see its power.', NOW(), NOW(), true, (select id from account where username = 'ahmad-su'))
    ;
END;
