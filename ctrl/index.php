<?php
//**************************************************
// 初期処理
//**************************************************
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// データベース接続
$dbh = db_connect();

// 管理者情報の取得
$adminName = '';
$adminId = '';
if (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];
    $adminInfo = selectAdminById($adminId);
    if (!empty($adminInfo)) {
        $adminName = $adminInfo[0]['login_id'];
    }
}

// フォーム用の変数を初期化
// 商品関連
$sItemId = isset($_GET['item_id']) ? $_GET['item_id'] : '';
$sItemName = isset($_GET['item_name']) ? $_GET['item_name'] : '';
$sItemPrice = isset($_GET['item_price']) ? $_GET['item_price'] : '';
$sItemText = isset($_GET['item_text']) ? $_GET['item_text'] : '';
$sCategoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$sStopFlg = isset($_GET['stop_flg']) ? $_GET['stop_flg'] : '';
$sItemImage = isset($_GET['item_image']) ? $_GET['item_image'] : '';

// メンバー関連
$sMemberId = isset($_GET['member_id']) ? $_GET['member_id'] : '';
$sLastName = isset($_GET['last_name']) ? $_GET['last_name'] : '';
$sFirstName = isset($_GET['first_name']) ? $_GET['first_name'] : '';
$sLoginId = isset($_GET['login_id']) ? $_GET['login_id'] : '';
$sLoginPass = isset($_GET['login_pass']) ? $_GET['login_pass'] : '';
$sAddress = isset($_GET['address']) ? $_GET['address'] : '';

// カテゴリ一覧を取得
$categories = getCategoryList();

// フィルターの取得（販売状態とカテゴリ）
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';

// メンバー検索用の変数を初期化
$sMemberName = isset($_GET['member_name']) ? $_GET['member_name'] : '';
$sEmail = isset($_GET['email']) ? $_GET['email'] : '';
$sAddress = isset($_GET['address']) ? $_GET['address'] : '';

// メンバー検索の処理
$members = [];
if (!empty($sMemberName) || !empty($sEmail) || !empty($sAddress)) {
    $searchConditions = array_filter([
        'member_name' => $sMemberName,
        'email' => $sEmail,
        'address' => $sAddress
    ]);
    
    $members = searchMember($searchConditions);
} else {
    $members = selectMember();
}

// 商品検索とフィルターの処理
$items = [];
if ($sItemId || $sItemName || $sItemPrice) {
    // 商品検索の実装
    $items = searchItems([
        'item_id' => $sItemId,
        'item_name' => $sItemName,
        'item_price' => $sItemPrice
    ]);
} else if ($categoryFilter !== 'all') {
    $items = selectItemByCategory($categoryFilter);
} else if ($statusFilter !== 'all') {
    switch ($statusFilter) {
        case 'available':
            $items = selectItemByStatus(0);
            break;
        case 'stopped':
            $items = selectItemByStatus(1);
            break;
        default:
            $items = selectItem();
    }
} else {
    $items = selectItem();
}

require_once('../view/index.html');
?>