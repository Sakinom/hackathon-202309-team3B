<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // var_dump($password);
    require_once('../components/dbconnect.php');


    $sql = "SELECT * FROM login join users on login.user_id = users.id WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $login = $stmt->fetch();

    if (!$login) { //パスワードがハッシュにマッチするかどうかを調べる password_verify(パスワード,ハッシュ値) かつ $userの中に中身があるかどうか
        // echo '1';
        $message = "アカウントが存在しません";
    } else if (!$login || $login["password"] == null) {
        // echo '2';
        $message = "パスワードが登録されていません<br>※送信済みメールからパスワードを設定してください";
    } else if (!$login || !password_verify($password, $login["password"])) {
        // echo '3';
        $message = "認証情報が正しくありません";
    } else {
        session_start();
        $_SESSION["id"] = $login["user_id"];
        $message = "ログインに成功しました";
        header('Location: ../index.php');
        // echo '4';
    }
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
    <link rel="stylesheet" href="../../assets/css/login.css">
    <title>ログイン画面</title>
</head>

<body>
    <main class="l-main">
        <div class="container">
            <div class="login_text">
                ログイン
            </div>
            <?php if (isset($message)) { ?>
                <p><?= $message ?></p>
            <?php } ?>

            <form method="POST" class="login_form">
                <div class="input_container">
                    <div class="row mb-3">
                        <div>
                            <input type="email" class="form-control" id="email" name="email" placeholder="email">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div>
                            <input type="password" class="form-control" id="password" name="password" placeholder="password">
                        </div>
                    </div>
                </div>
                <div>
                    <p><a href="./signup.php">新規登録はこちら</a></p>
                </div>
                <!-- <div>
                    <p><a href="./email_confirm.php">パスワード変更はこちら</a></p>
                </div> -->
                <button type="submit" disabled class="btn btn-primary submit">ログイン</button>
            </form>
        </div>
    </main>
</body>
<script>
    const EMAIL_REGEX = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/ //メールアドレスのパターン 正規表現
    const submitButton = document.querySelector('.btn.submit')
    const emailInput = document.querySelector('#email')
    const inputDoms = Array.from(document.querySelectorAll('.form-control'))
    inputDoms.forEach(inpuDom => {
        inpuDom.addEventListener('input', event => {
            const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
            submitButton.disabled = !(isFilled && EMAIL_REGEX.test(emailInput.value))
        })
    })
</script>

</html>