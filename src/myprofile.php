<?php
session_start(); //ログイン機能を作った際に必要

if (empty($_SESSION['id'])) {
  //リダイレクト
  header('Location: /auth/login_page.php');
}

require_once('./components/dbconnect.php');


$user_id = $_SESSION['id'];

$user = $pdo->query("SELECT * from users where id = $user_id")->fetch(PDO::FETCH_ASSOC);
// var_dump($user);
$owned_tags = $pdo->query("select distinct name, image from owned_tags join tags on owned_tags.tag_id = tags.id where user_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>マイプロフィール</title>
  <link rel="stylesheet" href="../../assets/css/reset.css">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/profile.css">
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.8.1/css/perfect-scrollbar.min.css" rel="stylesheet" type="text/css"> -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.3/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.8.1/js/perfect-scrollbar.jquery.min.js"></script>
  <script src="../../assets/script/common.js" defer></script>
</head>

<body>
  <header class="l-header p-header">
    <div class="p-header_logo">
      <img src="./assets/img/logo.svg" alt="POSSE">
    </div>
    <div class="openbtn" id="openbtn"><span></span><span></span></div>
    <nav id="g-nav">
      <div id="g-nav-list"><!--ナビの数が増えた場合縦スクロールするためのdiv※不要なら削除-->
        <ul class="hamburger__list">
          <li><a href="./index.php">トップページ</a></li>
          <li><a href="./myprofile.php">マイプロフィール</a></li>
          <li><a href="./proflist.php">プロフィール一覧</a></li>
          <form action="/auth/signout.php" method="post">
            <input type="submit" value="ログアウト" class="signout_btn">
          </form>
        </ul>
      </div>

    </nav>
    <div class="p-header_inner">
      <nav class="p-header_nav">
        <ul class="p-header_nav_list">
          <li class="p-header_nav_item">
            <a href="./index.php">トップページ</a>
          </li>
          <li class="p-header_nav_item">
            <a href="./myprofile.php">プロフィール</a>
          </li>
          <li class="p-header_nav_item">
            <a href="./proflist.php">プロフィール一覧</a>
          </li>
          <li class="p-header_nav_item">
            <form action="/auth/signout.php" method="post">
              <input type="submit" value="ログアウト" class="signout_btn">
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <div id="container">
    <main class="l-main">
      <section class="profile">
        <div class="profile_img">
          <img src="./assets/img/profile/<?= $user['image'] ?>" alt="">
        </div>
        <div class="profile_content">
          <div class="title">
            <h1>マイプロフィール</h1>
          </div>
          <div class="profile_info">
            <h2 class="profile_name"><?= $user['last_name'] . $user['first_name'] ?></h2>
            <p>あだ名：<?= $user['nickname'] ?></p>
            <p><?= 'POSSE ' . $user['posse'] . '　' . $user['generation'] . '期生' ?></p>
          </div>
          <div class="profile_badge"></div>
        </div>
      </section>

      <section class="badges">
        <h3 class="badge_title">称号</h3>
        <?php foreach ($owned_tags as $badge) { ?>
          <div class="badge_wrapper">
            <div class="badge_img">
              <img src="./assets/img/<?= $badge['image'] ?>" alt="">
            </div>
            <p><?= $badge['name'] ?></p>
          </div>
        <?php } ?>
      </section>
    </main>
  </div>
  <!-- #/container -->

  <footer class="p-footer">
    <!-- フッター後からphpで追加 -->
  </footer>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="./assets/scripts/openbtn.js"></script>
  <script src="./assets/scripts/index.js"></script>
</body>

</html>