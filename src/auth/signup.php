<?php
session_start(); //ログイン機能を作った際に必要
// if (empty($_SESSION['id'])) {
//   //リダイレクト
//   header('Location: /auth/login.php');
// }

require_once('../components/dbconnect.php');

// $users = $pdo->query("SELECT * from users")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
  if (!empty($_FILES['image']['name'])) { //ファイルが選択されていれば$imageにファイル名を代入
    $image = uniqid(mt_rand(), true); //ファイル名をユニーク化
    $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1); //アップロードされたファイルの拡張子を取得
    $file = dirname(__FILE__) . '/../assets/img/profile/' . $image;
    $sql = "INSERT INTO users(image) VALUES (:image)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':image', $image, PDO::PARAM_STR);

    move_uploaded_file($_FILES['image']['tmp_name'], $file); //imagesディレクトリにファイル保存
    if (file_exists($file)) { //画像ファイルかのチェック
      $message = '画像をアップロードしました';
      // $stmt->execute();
    } else {
      $message = '画像ファイルではありません';
    }
  } else {
    $message = '画像ファイルがありません';
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー登録</title>
  <link rel="stylesheet" href="../assets/css/reset.css">
  <link rel="stylesheet" href="../assets/css/signup.css">
</head>

<body>
  <!-- header -->
  <header class="p-header l-header">
    <!-- ハンバーガーメニュー後から追加 -->
        <div class="p-header_logo">
            <img src="../assets/img/logo.svg" alt="POSSE">
        </div>
    </header>

  <!-- /.header -->
  <main class="l-main">
    <div class="title">
      <h1>ユーザー登録</h1>
    </div>

    <section class="signup">
      <form action="../services/create_signup.php" method="POST" enctype="multipart/form-data" class="signup_form">
        <div class="signup_form_container">
          <!-- 登録フォームの項目をテーブルとして表示 -->
          <table class="table">
            <tr class="input_item">
              <th class="required">
                苗字
              </th>
              <td>
                <input type="text" name="last_name" id="last_name" required class="signup_form_input">
              </td>
            </tr>
            <tr class="input_item">
              <th class="non_required">
                名前
              </th>
              <td>
                <input type="text" name="first_name" id="first_name" class="signup_form_input">
              </td>
            </tr>
            <tr class="input_item">
              <th class="non_required">
                あだ名
              </th>
              <td>
                <input type="text" name="nickname" id="nickname" class="signup_form_input">
              </td>
            </tr>
            <tr class="input_item">
              <th class="required">
                メールアドレス
              </th>
              <td>
                <input type="email" name="email" id="email" required class="signup_form_input">
              </td>
            </tr>
            <tr class="input_item">
              <th class="required">
                POSSE
              </th>
              <td class="type">
                <select name="posse">
                  <option value="">選択してください</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </td>
            </tr>
            <tr class="input_item">
              <th class="required">
                期生
              </th>
              <td class="type">
                <select name="generation">
                  <option value="">選択してください</option>
                  <option value="1.0">1.0</option>
                  <option value="1.5">1.5</option>
                  <option value="2.0">2.0</option>
                  <option value="2.5">2.5</option>
                  <option value="3.0">3.0</option>
                  <option value="3.5">3.5</option>
                  <option value="4.0">4.0</option>
                </select>
              </td>
            </tr>
            <tr class="input_item">
              <th class="non_required">
                顔写真
              </th>
              <td>
                <input id="image" type="file" name="image" class="signup_form_input">
              </td>
            </tr>
          </table>
          <!-- ./table -->
        </div>

        <div class="submit">
          <!-- フォーム送信ボタン -->
          <input type="submit" class="submit_btn" name="submit" value="登録" >
        </div>
      </form>
    </section>
  </main>

</body>
</html>