<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// POSTリクエストのみ受け付ける
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 商品IDの取得
$itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

if ($itemId > 0) {
    try {
        if (deleteItem($itemId)) {
            $_SESSION['success_message'] = "商品を削除しました。";
        } else {
            $_SESSION['error_message'] = "商品の削除に失敗しました。";
        }
    } catch (Exception $e) {
        error_log("削除処理エラー: " . $e->getMessage());
        $_SESSION['error_message'] = "システムエラーが発生しました。";
    }
}

// 商品一覧ページにリダイレクト
header('Location: index.php?scroll=true');
exit;
?> 