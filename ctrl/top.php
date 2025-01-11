<?php
//**************************************************
// 初期処理
//**************************************************
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// データベース接続
$dbh = db_connect();

//**************************************************
// ユーザー情報取得
//**************************************************
$userName = '';
if (isset($_SESSION['user_id'])) {
    // ユーザー情報を取得
    $userId = $_SESSION['user_id'];
    $userInfo = selectUserById($userId);
    if (!empty($userInfo)) {
        $userName = $userInfo[0]['last_name'] . ' ' . $userInfo[0]['first_name'];
    }
}

//**************************************************
// 商品一覧取得
//**************************************************
// 販売状態フィルターの取得
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// フィルターに応じた商品の取得
switch ($filter) {
    case 'available':
        $items = selectItemByStatus(0); // 販売中の商品のみ
        break;
    case 'stopped':
        $items = selectItemByStatus(1); // 販売停止中の商品のみ
        break;
    default:
        $items = selectItem(); // すべての商品
}

error_log("Debug - Items retrieved for top page: " . print_r($items, true));

//**************************************************
// HTML表示
//**************************************************
require_once('../view/top.html');
?>