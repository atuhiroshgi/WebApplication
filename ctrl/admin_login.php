<?php
//**************************************************
// 初期処理
//**************************************************
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

//**************************************************
// 変数取得
//**************************************************
$sLoginId = isset($_POST['login_id']) ? $_POST['login_id'] : "";
$sLoginPass = isset($_POST['login_pass']) ? $_POST['login_pass'] : "";
$arrErr = array();

//**************************************************
// ログイン処理
//**************************************************
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 管理者ログインチェック
    $loginResult = adminLoginCheck($sLoginId, $sLoginPass);
    if ($loginResult !== false) {
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_id'] = $loginResult;
        header('Location: index.php');
        exit;
    } else {
        $arrErr['login'] = "ログインIDまたはパスワードが正しくありません。";
    }
}

//**************************************************
// HTMLを出力
//**************************************************
require_once('../view/admin_login.html');
?> 