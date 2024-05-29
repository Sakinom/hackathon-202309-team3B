<?php
session_start(); //ログイン機能を作った際に必要
if (empty($_SESSION['id'])) {
    //リダイレクト
    header('Location: /auth/login.php');
}

require_once('./components/dbconnect.php');

$sql = "SELECT id, last_name, first_name, nickname, generation, image from users where posse = :posse";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':posse', 1);
$stmt->execute();
$posse_1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->bindValue(':posse', 2);
$stmt->execute();
$posse_2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->bindValue(':posse', 3);
$stmt->execute();
$posse_3 = $stmt->fetchAll(PDO::FETCH_ASSOC);

// var_dump($posse_1);
// var_dump($posse_2);
// var_dump($posse_3);
// foreach($posse_3 as $member) {
//     var_dump($member['last_name']);
// }


$sql = "SELECT friend_id from friends where user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['id']);
$stmt->execute();
$friends = $stmt->fetchAll(PDO::FETCH_COLUMN);

// var_dump($friends);


if (isset($_POST['follow'])) {
    $followValue = intval($_POST["follow"]);
    // var_dump($_POST["follow"]);
    // フォローを追加する関数
    $sql = "INSERT INTO friends (user_id, friend_id) VALUES (:user_id, :friend_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['id']);
    $stmt->bindValue(':friend_id', $followValue);
    $stmt->execute();

    $sql = "SELECT friend_id from friends where user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['id']);
    $stmt->execute();
    $friends = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>はばずおるん？</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <!-- モーダル用のCSS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Modaal/0.4.4/css/modaal.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;700&display=swap" rel="stylesheet">
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
                    <li><a href="./myprofile.php">プロフィール</a></li>
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
    <main id="">
        <div class="p-top-container">
            <section class="l-section">
                <div class="l-container">
                    <h2 class="p-heading">
                        POSSE生のプロフィール一覧
                    </h2>
                    <ul class="tab no_dot">
                        <li><a href="#posse1">POSSE1</a></li>
                        <li><a href="#posse2">POSSE2</a></li>
                        <li><a href="#posse3">POSSE3</a></li>
                    </ul>


                    <div id="posse1" class="area">
                        <h2>posse1のメンバー</h2>
                        <ul class="no_dot">
                            <?php foreach ($posse_1 as $member) { ?>
                                <li class="prof_box">

                                    <div class="prof_content">
                                        <img src="./assets/img/profile/<?= $member['image']; ?>" alt="自画像" class="pro__img">
                                        <div class="prof_text">
                                            <h2 class="prof_name"><?= $member['last_name'] . $member['first_name']; ?></h2>
                                            <?= $member['generation']; ?>期生
                                            <form action="./proflist.php" method="post">
                                                <input type="hidden" name="follow" value="<?= $member['id']; ?>">
                                                <button type="submit" class="follow_button"><?= in_array($member['id'], $friends) ? 'followed' : 'follow'; ?></button>
                                                <!-- <button type="submit" class="follow_button">follow</button> -->
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                        <!--/area-->
                    </div>
                    <div id="posse2" class="area">
                        <h2>poss2のメンバー</h2>
                        <ul class="no_dot">
                            <?php foreach ($posse_2 as $member) { ?>
                                <li>
                                    <div class="prof_content">
                                        <img src="./assets/img/profile/<?= $member['image']; ?>" alt="自画像" class="pro__img">
                                        <div class="prof_text">
                                            <h2 class="prof_name"><?= $member['last_name'] . $member['first_name']; ?></h2>
                                            <?= $member['generation']; ?>期生
                                            <form action="./proflist.php" method="post">
                                                <input type="hidden" name="follow" value="<?= $member['id']; ?>">
                                                <button type="submit" class="follow_button"><?= in_array($member['id'], $friends) ? 'followed' : 'follow'; ?></button>
                                                <!-- <button type="submit" class="follow_button">follow</button> -->
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>

                        </ul>
                        <!--/area-->
                    </div>
                    <div id="posse3" class="area">
                        <h2>posse3のメンバー</h2>
                        <ul class="no_dot">
                            <?php foreach ($posse_3 as $member) { ?>
                                <li>
                                    <div class="prof_content">
                                        <img src="./assets/img/profile/<?= $member['image']; ?>" alt="自画像" class="pro__img">
                                        <div class="prof_text">
                                            <h2 class="prof_name"><?= $member['last_name'] . $member['first_name']; ?></h2>
                                            <?= $member['generation']; ?>期生
                                            <form action="./proflist.php" method="post">
                                                <input type="hidden" name="follow" value="<?= $member['id']; ?>">
                                                <button type="submit" class="follow_button"><?= in_array($member['id'], $friends) ? 'followed' : 'follow'; ?></button>
                                                <!-- <button type="submit" class="follow_button">follow</button> -->
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                        <!--/area-->
                    </div>
                </div>
            </section>
        </div>

    </main>
    <div>
        <div>
            <footer class="footer">

                <div class="footer_logo">
                    <span>
                        <img src="../assets/img/logo.svg" alt="POSSEのロゴ">
                    </span>
                </div>
                <div>
                    <div class="p-footer__copyright">
                        <small lang="en">©︎2022 POSSE</small>
                    </div>
                </div>
            </footer>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Modaal/0.4.4/js/modaal.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
    <script src="./assets/scripts/openbtn.js"></script>
    <script src="./assets/scripts/index.js"></script>
    <script src="./assets/scripts/proflist.js"></script>
</body>

</html>