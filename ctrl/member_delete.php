<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// POSTリクエストのみ受け付ける
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// メンバーIDの取得
$memberId = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;

if ($memberId > 0) {
    try {
        if (deleteMember($memberId)) {
            $_SESSION['success_message'] = "メンバーを削除しました。";
        } else {
            $_SESSION['error_message'] = "メンバーの削除に失敗しました。";
        }
    } catch (Exception $e) {
        error_log("削除処理エラー: " . $e->getMessage());
        $_SESSION['error_message'] = "システムエラーが発生しました。";
    }
}

// メンバー一覧ページにリダイレクト
header('Location: index.php?scroll=member');
exit;
?>
