<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('error_log', 'debug.log');

require_once '../model/dbfunction.php';
session_start();

// POSTデータがある場合は更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータの取得
    $formData = [
        'id' => $_POST['id'] ?? '',
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? '',
        'address' => $_POST['address'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];

    $step = $_POST['step'] ?? "1";
    $errors = [];

    // バリデーション
    if ($step == "2") {
        if (empty($formData['first_name'])) {
            $errors['first_name'] = "名前を入力してください";
        }
        if (empty($formData['last_name'])) {
            $errors['last_name'] = "姓を入力してください";
        }
        if (empty($formData['email'])) {
            $errors['email'] = "メールアドレスを入力してください";
        }
    }

    // 更新処理
    if ($step == "2" && empty($errors)) {
        $result = updateMember(
            $formData['id'],
            $formData['first_name'],
            $formData['last_name'],
            $formData['email'],
            $formData['postal_code'],
            $formData['address'],
            $formData['phone']
        );

        if ($result) {
            $_SESSION['success_message'] = "会員情報を更新しました";
            header('Location: index.php');
            exit;
        } else {
            $errors['system'] = "更新に失敗しました";
        }
    }
} 
// GET リクエストの場合は編集フォームを表示
else {
    $memberId = $_GET['id'] ?? '';
    if (empty($memberId)) {
        header('Location: index.php');
        exit;
    }
    
    // 会員データの取得
    $member = getMemberById($memberId);
    if (!$member) {
        header('Location: index.php');
        exit;
    }
}

// ビューの表示
require_once '../view/member_update.html';