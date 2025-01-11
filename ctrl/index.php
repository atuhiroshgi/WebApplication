<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// データベース接続
$dbh = db_connect();

// 管理画面のデータを取得
$adminId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
$pageData = getAdminIndexData($_GET, $adminId);

// ビュー変数の展開
extract($pageData); // $categories, $items, $members, $adminName, $adminIdを展開

// 検索パラメータの展開
extract($pageData['searchParams']); // 全ての検索パラメータを展開

require_once('../view/index.html');
?>