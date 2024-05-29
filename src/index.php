<?php

session_start();
require 'vendor/autoload.php';

$token = "Vr4KGJleDjh0pbH9qIzwrooJYWMTf8VXhIqmcFeV6ct";

if (empty($_SESSION['id'])) {
    //リダイレクト
    header('Location: /auth/login.php');
}
// var_dump($_SESSION["id"]);
$user_id = $_SESSION["id"];

require_once('./components/dbconnect.php');

// $stmt = $pdo->prepare('select * from status where time = :user_id');

$stmt = $pdo->prepare('select last_name, first_name, time from users join status on users.id = status.user_id where users.id = :id and status.status = 1');
// $stmt = $pdo->prepare('select last_name, first_name from users where id = :id');
$stmt->bindValue(':id', $_SESSION['id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// var_dump($user);


// curl_setopt_array($ch, $options);
// $response = curl_exec($ch);
// curl_close($ch);
// echo $response;

$sql = "SELECT friend_id from friends where user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['id']);
$stmt->execute();
$friends = $stmt->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT * from users where status = 2";
$active = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// var_dump($active);

// echo 'hello';
    // var_dump($_POST['status']);
    // echo 'hello';
    if (isset($_POST['status'])) {

        $sql = "INSERT INTO status (user_id, status, time) VALUES (:user_id, :status, :time)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id']);
        $stmt->bindValue(':status', 1);
        $stmt->bindValue(':time', date("Y/m/d " . $_POST['time'] . ":00:00"));
        $stmt->execute();
        $stmt->bindValue(':user_id', $_SESSION['id']);
        $stmt->bindValue(':status', '2');
        $stmt->bindValue(':time', date("Y/m/d H:i:s"));
        $stmt->execute();

        $stmt = $pdo->prepare('select last_name, first_name, time from users join status on users.id = status.user_id where users.id = :id and status.status = 1');
        // $stmt = $pdo->prepare('select last_name, first_name from users where id = :id');
        $stmt->bindValue(':id', $_SESSION['id']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // var_dump($user);

        if (isset($user['last_name']) && isset($user['first_name'])) {
            $message = $user['last_name'] . $user['first_name'] . "さんが入室しました。" . $_POST['time'] . "時ごろに帰る予定です。";
            $query = http_build_query(['message' => $message]);
            $header = ['Authorization: Bearer ' . $token];
            $ch = curl_init('https://notify-api.line.me/api/notify');
            $options = [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_POST            => true,
                CURLOPT_HTTPHEADER      => $header,
                CURLOPT_POSTFIELDS      => $query
            ];

            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            curl_close($ch);
        }


        $sql = "UPDATE users SET status = 2 WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id']);
        $stmt->execute();



        // if (in_array($_POST['visitor'], $friends)) {

        $sql = "SELECT * from users where status = 2";
        $active = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


        // }
    } else if (isset($_POST['deactivate'])) {
        // echo 'de';
        $sql = "UPDATE status SET status = 0, time = :time WHERE user_id = :user_id and status = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id']);
        $stmt->bindValue(':time', date("Y/m/d H:i:s"));
        $stmt->execute();

        $sql = "UPDATE users SET status = 0 WHERE id = :user_id and status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id']);
        $stmt->execute();

        $visits = $pdo->query("SELECT DATE(time) AS date FROM status WHERE user_id = 12 GROUP BY DATE(time)")->fetchAll(PDO::FETCH_ASSOC);

        $owned_tags = $pdo->query("select * from owned_tags where user_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);

        // var_dump($owned_tags);
        // echo '<pre>';
        // var_dump($visits);
        // echo '</pre>';

        if ($owned_tags == [] && isset($visits)) {
            $pdo->query("insert into owned_tags (user_id, tag_id) value ($user_id, 1)");
        };
        // var_dump(count($visits));
        // var_dump(in_array(2 ,$owned_tags));
        if (count($visits) == 3) {
            $pdo->query("insert into owned_tags (user_id, tag_id) value ($user_id, 2)");
        }
        $sql = "SELECT * from users where status = 2";
        $active = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


        // echo '退室登録完了';
    } else {
        echo 'failed';
    }

?>





<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HarborSHive</title>
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
        <section class="p-top-hero">
            <div class="enter_exit_btn">
                <div class="float1">
                    <div><a href="#info" class="modal-open enter_link">入室</a></div>
                </div>
                <!-- モーダル -->
                <!--表示エリアのHTML。id 名にリンク先と同じ名前を指定します。※表示エリアはHTML の変更が可能なので、レイアウトを自由に変更できます。-->
                <section id="info">
                    <form action="./index.php" method="post">
                        <h2>今日は何時までいるの？</h2>
                        <p>下のバナーを動かして！</p>
                        <!-- レンジスライダー -->
                        <div class="range_slider">
                            <span class="range_slider_min"></span>
                            <div class="range_slider_input">
                                <div class="range_slider_input_current">
                                    <span></span>
                                </div>
                                <input type="range" name="time" min="0" max="24" value="12" step="1">
                            </div>
                            <span class="range_slider_max"></span>
                        </div>
                        <input type="hidden" name="status" value="1">
                        <div>
                            <button type="submit" class="btn btn-primary submit">登録</button>
                        </div>
                    </form>
                </section>
                <div class="float2">
                    <form action="./index.php" method="post" class="float2_form">
                        <!-- <div><a href="#info2" class="modal-open"><button type="submit" class="modal-open">退室</button></a></div> -->
                        <button type="submit" name="deactivate" class="btn btn-primary submit exit_btn">退室</button>
                        <!-- <div><a href="#info2" class="modal-open">退室</a></div> -->
                    </form>
                </div>
                <section id="info2">
                    <h2>おつかれさま！</h2>
                    <p>あなたの称号は</p>
                </section>
            </div>
        </section>
        <div class="p-top-container">
            <section class="l-section">
                <div class="l-container">
                    <h2 class="p-heading">
                        ハーバーズにいるメンバー
                    </h2>
                    <ul class="no_dot">
                        <?php foreach ($active as $member) { ?>
                            <li class="exist_box">
                                <div class="exist_content">
                                    <img src="./assets/img/profile/<?= $member['image']; ?>" alt="自画像" class="pro__img">
                                    <div class="prof_text">
                                        <h2 class="exist_name"><?= $member['last_name'] . $member['first_name']; ?></h2>
                                        POSSE<?= $member['posse']; ?>　<?= $member['generation']; ?>期生
                                        <!-- <form action="./proflist.php" method="post">
                                        <input type="hidden" name="follow" value="<?= $member['id']; ?>">
                                        <button type="submit" class="follow_button"><?= in_array($member['id'], $friends) ? 'followed' : 'follow'; ?></button> -->
                                        <!-- <button type="submit" class="follow_button">follow</button> -->
                                        <!-- </form> -->
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
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