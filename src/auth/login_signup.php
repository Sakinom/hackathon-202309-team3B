<?php
require_once('../components/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //ブラウザからのリクエストが、POSTメソッドなのかGETメソッドなのか、スクリプト側で判別
  session_start();
  // トークンとメールアドレスを取得
  $email = $_POST["email"];
  $token = $_POST["token"];

  $password = $_POST["password"];
  $password_confirm = $_POST["password_confirm"];

  if ($password !== $password_confirm) {
    $message = "パスワードが一致しません";
  } else {
    // トークンとメールアドレスのバリデーション
    $stt = $pdo->prepare("SELECT * FROM login WHERE email = :email and token = :token");
    $stt->bindValue(':email', $email);
    $stt->bindValue(':token', $token);
    $stt->execute();
    $login = $stt->fetch(PDO::FETCH_ASSOC); //PDOオブジェクトでデータベースからデータを取り出した際にデフォルトの配列の形式を指定
    if (!empty($login['token'])) {
      $diff = (new DateTime())->diff(new DateTime($login["created_at"])); //DateTimeで現在時刻を取得、diffメソッドを使って$login["created_at"]で指定した日付との日にちの差を得る
      $is_expired = $diff->days >= 1; //年月を含めて日数の差を算出し、その値が1以上の時に 'true' を$is_expiredに入れる
      if ($is_expired) {
        $message = "招待期限が切れています。管理者に連絡してください。";
      } else {
        try {
          $pdo->beginTransaction();
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          $stt = $pdo->prepare("UPDATE login SET password = :password, updated_at = :updated_at WHERE id = :id");
          $stt->bindValue(':password', $hashedPassword); 
          $stt->bindValue(':updated_at', (new DateTime())->format('Y-m-d H:i:s'));
          $stt->bindValue(':id', $login["id"]);
          $result = $stt->execute();

          $pdo->commit(); //エラーがなければ変更をコミットする

          $_SESSION['id'] = $login["id"]; // $_SESSION(セッション変数)の登録
          $_SESSION['message'] = "ユーザー登録に成功しました";
          header('Location: /index.php');
        } catch (PDOException $e) {
          $pdo->rollBack(); //失敗時にデータを元の形に戻す
          $message = $e->getMessage();
        }
      }
    } else {
      $message = "既に認証済みです。";
    }
  }
} else { //ブラウザからのリクエストがPOSTでなかった場合（つまりはGET）
  session_start();
  $token = isset($_GET['token']) ? $_GET['token'] : null; //$_GET['token']に値がセットされている場合は$tokenにその値を入れ、ない場合はnullを入れる
  $email = isset($_GET['email']) ? $_GET['email'] : null;

  if (is_null($token) || is_null($email)) { //$tokenと$emailのどちらかがnullか判断
  }

  // if (isset($_SESSION["id"])) {
  //   header('Location: /index.php');
  // }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../assets/css/reset.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="../../assets/css/login_signup.css">
  <title>ログイン登録</title>
</head>

<body>
  <main class="l-main">
    <div class="login_over">
      <div class="container">
        <div class="title">
          ログイン登録
        </div>
        <?php if (isset($message)) { ?>
          <p><?= $message ?></p>
        <?php } ?>
        <form method="POST">
          <div class="row mb-3">
            <label for="email" class="col-form-label">メールアドレス</label>
            <div>
              <input type="email" name="email" class="form-control" value="<?= $email ?>" id="email" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label for="password" class="col-form-label">新しいパスワード</label>
            <div>
              <input type="password" name="password" class="form-control" id="password">
            </div>
          </div>
          <div class="row mb-3">
            <label for="password_confirm" class="col-form-label">パスワード確認用</label>
            <div>
              <input type="password" name="password_confirm" class="form-control" id="password_confirm">
            </div>
          </div>
          <input type="hidden" name="token" id="token" value="<?= $token ?>">
          <button type="submit" disabled class="btn btn-primary submit">登録</button>
        </form>
      </div>
    </div>
  </main>
</body>
<script>
  const submitButton = document.querySelector('.btn.submit')
  const inputDoms = Array.from(document.querySelectorAll('.form-control'))
  const password = document.querySelector('#password')
  const passwordConfirm = document.querySelector('#password_confirm')
  const token = document.querySelector('#token')
  inputDoms.forEach(inpuDom => {
    inpuDom.addEventListener('input', event => { //input要素がユーザーの操作によって値が変更されたとき
      const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
      const isPasswordMatch = password.value === passwordConfirm.value
      submitButton.disabled = !(isFilled && isPasswordMatch)
    })
  })
  const signup = async () => { //非同期処理にする
    const res = await fetch('/services/signup.php', { //レスポンスを取得（await で fetch() が完了するまで待つ）
      method: 'PATCH', //データがすでに存在しているものに対して更新
      body: JSON.stringify({ //JSON 文字列に変換
        email: document.querySelector('#email').value,
        password: document.querySelector('#password').value,
        password_confirm: document.querySelector('#password_confirm').value,
        token: document.querySelector('#token').value,
      }),
      headers: {
        'Accept': 'application/json, */*', //クライアント側がJSONデータを処理できることを表す
        "Content-Type": "application/x-www-form-urlencoded" //実際にどんな形式のデータを送信したかを表す（今回はエンコードされたurlでデータが送受信される）
      },
    });
    const json = await res.json() //格納されていたjsonファイルのデータを取得する（jsonデータを加工するために必要）
    if (res.status === 200) { //ステータスコードが200だった場合 = つまりは成功だった場合
      alert(json["message"])
      location.href = '/roles/client/cli_main.php'
    } else {
      alert(json["error"]["message"])
    }
  }
</script>

</html>