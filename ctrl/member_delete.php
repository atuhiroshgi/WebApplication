<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// POSTリクエストのみ受け付ける
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// メンバーIDの取得とバリデーション
$memberId = filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT);

if ($memberId) {
    try {
        // 削除前にメンバー情報を取得（ログ用）
        $memberInfo = getMemberById($memberId);
        
        if ($memberInfo) {
            // モデル（dbfunction）を使用して削除処理を実行
            if (deleteMember($memberId)) {
                $_SESSION['success_message'] = "メンバー「{$memberInfo['last_name']} {$memberInfo['first_name']}」を削除しました。";
                error_log("メンバー削除成功: ID={$memberId}, 名前={$memberInfo['last_name']} {$memberInfo['first_name']}");
            } else {
                $_SESSION['error_message'] = "メンバーの削除に失敗しました。";
                error_log("メンバー削除失敗: ID={$memberId}");
            }
        } else {
            $_SESSION['error_message'] = "指定されたメンバーが見つかりません。";
            error_log("メンバー未検出: ID={$memberId}");
        }
    } catch (Exception $e) {
        error_log("メンバー削除処理エラー: " . $e->getMessage());
        $_SESSION['error_message'] = "システムエラーが発生しました。";
    }
} else {
    $_SESSION['error_message'] = "無効なメンバーIDが指定されました。";
    error_log("無効なメンバーID指定: " . print_r($_POST, true));
}

// メンバー一覧ページにリダイレクト
header('Location: index.php?scroll=member');
exit;
?>
