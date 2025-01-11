<?php
//**************************************************
// 初期処理
//**************************************************
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// データベース接続
$dbh = db_connect();

// ログイン情報の取得
$sLoginId = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : "";
$sLoginPass = isset($_SESSION['login_pass']) ? $_SESSION['login_pass'] : "";

// ログインチェックとユーザー名取得
$loginOk = loginCheck($sLoginId, $sLoginPass);
$userName = $loginOk ? getUserName($sLoginId, $sLoginPass) : "";

//**************************************************
// 商品一覧取得
//**************************************************
// 検索キーワードとフィルターの取得
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// 検索、フィルター、カテゴリの組み合わせで商品を取得
if (!empty($keyword)) {
    switch ($filter) {
        case 'available':
            $items = searchItemsByKeywordAndStatus($keyword, 0);
            break;
        case 'stopped':
            $items = searchItemsByKeywordAndStatus($keyword, 1);
            break;
        default:
            $items = searchItemsByKeyword($keyword);
    }
} else {
    switch ($filter) {
        case 'available':
            $items = selectItemByStatus(0);
            break;
        case 'stopped':
            $items = selectItemByStatus(1);
            break;
        default:
            $items = selectItem();
    }
}

// カテゴリでフィルタリング
if ($category !== 'all') {
    $items = array_filter($items, function($item) use ($category) {
        return $item['category_id'] == $category;
    });
}

error_log("Debug - Items retrieved for top page: " . print_r($items, true));

//**************************************************
// HTML表示
//**************************************************
require_once('../view/top.html');
?>