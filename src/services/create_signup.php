<?php
header('Location: ../auth/signup.php');

require_once('../auth/signup.php');

require_once('../components/dbconnect.php');

try {
  // トランザクション開始
  $pdo->beginTransaction();

  // ユーザー情報を格納
  $stt = $pdo->prepare('INSERT INTO users(last_name, first_name, nickname, generation, posse, image) VALUES(:last_name, :first_name, :nickname, :generation, :posse, :image)');

  $stt->execute([
    "last_name" => $_POST['last_name'],
    "first_name" => $_POST['first_name'],
    "nickname" => $_POST['nickname'],
    "generation" => $_POST['generation'],
    "posse" => (int)$_POST['posse'],
    "image" => empty($image) ? null : $image,
  ]);

  $lastInsertId = $pdo->lastInsertId(); //usersで最後に挿入したidを取得

  $token = hash('sha256', uniqid(rand(), 1)); //パスワードのハッシュ化
  $email = $_POST['email'];

  // ログイン情報の保存
  $stt = $pdo->prepare('INSERT INTO login(user_id, email, token, created_at) VALUES(:user_id, :email, :token, :created_at)');

  $stt->execute([
    "user_id" => $lastInsertId,
    "email" => $email,
    "token" => $token,
    "created_at" => date('Y-m-d H:i:s'),
  ]);

  mb_language("Japanese");
  mb_internal_encoding("UTF-8");

  $mail_from_address = "HarborSHive@example.jp";
  $mail_header = "Content-Type: text/plain; charset=UTF-8 \n" . 
    "From: " . $mail_from_address . "\n" . 
    "Sender: " . $mail_from_address . " \n" .
    "Return-Path: " . $mail_from_address . " \n" .
    "Reply-To: " . $mail_from_address . " \n" .
    "Content-Transfer-Encoding: BASE64\n"; 
  $is_mail_succeeded = mb_send_mail(
    $email,
    "HarborSHiveにてユーザー登録を行いました。",
    "こちらからパスワード登録をしてください。 http://localhost:8080/auth/login_signup.php?token=$token&email=$email",
    $mail_header,
    "-f " . $mail_from_address
  );


  if ($is_mail_succeeded) {
    $message = "メールを送信しました";
  } else {
    $message = "メールの送信に失敗しました";
  }


  $pdo->commit();
} catch (PDOException $e) {
  $pdo->rollBack();
  echo '保存に失敗しました。<br>';
  die("接続エラー：{$e->getMessage()}");
}
