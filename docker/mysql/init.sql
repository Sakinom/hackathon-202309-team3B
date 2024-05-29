DROP DATABASE IF EXISTS hackathon;

CREATE DATABASE hackathon;

USE hackathon;

-- ユーザー登録
CREATE TABLE IF NOT EXISTS users(
  id int PRIMARY KEY AUTO_INCREMENT,
  last_name VARCHAR(30),
  first_name VARCHAR(30),
  nickname VARCHAR(30),
  generation VARCHAR(5),
  posse INT,
  image VARCHAR(255),
  status TINYINT
) CHARSET = utf8;

-- ログイン登録
CREATE TABLE IF NOT EXISTS login(
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  email VARCHAR(100),
  password VARCHAR(255),
  token VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME,
  FOREIGN KEY (user_id) REFERENCES users(id)
) CHARSET = utf8;

-- 親しい友達登録
CREATE TABLE IF NOT EXISTS friends(
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  friend_id INT
) CHARSET = utf8;

-- 入退室ログ
CREATE TABLE IF NOT EXISTS status(
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  status TINYINT,
  time DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET = utf8;

-- 称号タグ一覧
CREATE TABLE IF NOT EXISTS tags(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50),
  image VARCHAR(255)
) CHARSET = utf8;

-- 称号獲得状況
CREATE TABLE IF NOT EXISTS owned_tags(
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  tag_id INT
) CHARSET = utf8;

-- 初期値
INSERT INTO
  tags(name, image)
VALUES
  ('初HarborS', 'best_choice.jpg'),
  ('HarborS猛打賞', 'golden_badge.jpg');

insert into status(user_id, status, time) VALUES
('1', '2', '2023/09/18'),
('1', '0', '2023/09/18'),
('1', '2', '2023/09/17'),
('1', '0', '2023/09/17'),
('1', '2', '2023/09/16'),
('1', '0', '2023/09/16');

