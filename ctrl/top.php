<?php
//**************************************************
// Controller: リクエスト処理とビジネスロジックの制御
//**************************************************
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// データベース接続
$dbh = db_connect();

// ログイン情報の取得と確認
$sLoginId = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : "";
$sLoginPass = isset($_SESSION['login_pass']) ? $_SESSION['login_pass'] : "";
$loginOk = loginCheck($sLoginId, $sLoginPass);
$userName = $loginOk ? getUserName($sLoginId, $sLoginPass) : "";

// 検索パラメータの取得
$searchParams = [
    'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : '',
    'filter' => isset($_GET['filter']) ? $_GET['filter'] : 'all',
    'category' => isset($_GET['category']) ? $_GET['category'] : 'all'
];

// Modelを使用してデータを取得
if (!empty($searchParams['keyword'])) {
    switch ($searchParams['filter']) {
        case 'available':
            $items = searchItemsByKeywordAndStatus($searchParams['keyword'], 0);
            break;
        case 'stopped':
            $items = searchItemsByKeywordAndStatus($searchParams['keyword'], 1);
            break;
        default:
            $items = searchItemsByKeyword($searchParams['keyword']);
    }
} else {
    switch ($searchParams['filter']) {
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

// カテゴリフィルタリング
if ($searchParams['category'] !== 'all') {
    $items = array_filter($items, function($item) use ($searchParams) {
        return $item['category_id'] == $searchParams['category'];
    });
}

// デバッグログ
error_log("Debug - Items retrieved for top page: " . print_r($items, true));

//**************************************************
// Viewの表示
//**************************************************
require_once('../view/top.html');
?>