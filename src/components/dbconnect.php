<?php
$dbn = 'mysql:dbname=hackathon; host=db; charset=utf8';
$user = 'root';
$pass = 'root';

try {
  $pdo = new PDO($dbn, $user, $pass);
} catch (PDOException $e) {
  die("接続エラー：{$e->getMessage()}");
}
