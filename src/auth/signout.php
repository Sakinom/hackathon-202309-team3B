<?php
session_start();

$_SESSION = [];
session_destroy(); //セッションに登録されたデータを全て破棄する

header('Location: /auth/login.php');
exit;