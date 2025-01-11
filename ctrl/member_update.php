<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// エラーログを有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 変数初期化
$memberId = isset($_GET['id']) ? $_GET['id'] : '';
$arrErr = array();

// POSTデータがある場合は更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberId = $_POST['id'];
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // デバッグ用ログ
    error_log("更新処理開始: ID=" . $memberId);
    error_log("POST データ: " . print_r($_POST, true));

    // 入力チェック
    if (empty($lastName) || empty($firstName)) {
        $arrErr['name'] = "氏名を入力してください";
    }
    if (empty($email)) {
        $arrErr['email'] = "メールアドレスを入力してください";
    }
    if (empty($address)) {
        $arrErr['address'] = "住所を入力してください";
    }

    // エラーがなければ更新
    if (empty($arrErr)) {
        try {
            $result = updateMember($memberId, $firstName, $lastName, $email, $address);
            if ($result) {
                $_SESSION['success_message'] = "会員情報を更新しました";
                header('Location: index.php?scroll=member');
                exit;
            } else {
                $arrErr['update'] = "更新に失敗しました";
                error_log("更新失敗: result=" . var_export($result, true));
            }
        } catch (Exception $e) {
            error_log("更新エラー: " . $e->getMessage());
            $arrErr['update'] = "システムエラーが発生しました";
        }
    }
}

// 会員情報の取得
if ($memberId) {
    try {
        $member = getMemberById($memberId);
        if (!$member) {
            error_log("会員が見つかりません: ID=" . $memberId);
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        error_log("会員情報取得エラー: " . $e->getMessage());
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

require_once('../view/member_update.html');
?>
