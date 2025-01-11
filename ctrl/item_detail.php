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
$sLoginId = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : "";
$sLoginPass = isset($_SESSION['login_pass']) ? $_SESSION['login_pass'] : "";
$itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

//**************************************************
// データ取得処理
//**************************************************
// ログインチェックとユーザー名取得
$loginOk = loginCheck($sLoginId, $sLoginPass);
$userName = $loginOk ? getUserName($sLoginId, $sLoginPass) : "";

// 商品詳細を取得
$item = getItemById($itemId);

// 商品が存在しない場合はトップページへリダイレクト
if (!$item) {
    header("Location: top.php");
    exit();
}

//**************************************************
// HTMLを出力
//**************************************************
require_once('../view/item_detail.html');
?> 