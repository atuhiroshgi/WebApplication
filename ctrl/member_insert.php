<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// 変数の初期化
$formData = [
    'last_name' => isset($_POST['last_name']) ? $_POST['last_name'] : "",
    'first_name' => isset($_POST['first_name']) ? $_POST['first_name'] : "",
];

// セッションからフォームデータを復元（エラー時）
if (isset($_SESSION['form_data'])) {
    $formData = array_merge($formData, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}

$step = isset($_POST['step']) ? $_POST['step'] : "";
$errors = [];

// バリデーション処理
function validateMemberData($data) {
    $errors = [];
    
    // 苗字チェック
    if ($data['last_name'] === "") {
        $errors['last_name'] = "苗字を入力してください";
    } elseif (mb_strlen($data['last_name'], "UTF-8") > 10) {
        $errors['last_name'] = "苗字は10文字以内で入力してください";
    }
    
    // 名前チェック
    if ($data['first_name'] === "") {
        $errors['first_name'] = "名前を入力してください";
    } elseif (mb_strlen($data['first_name'], "UTF-8") > 10) {
        $errors['first_name'] = "名前は10文字以内で入力してください";
    }
    
    return $errors;
}

// 入力チェックと確認画面表示
if ($step == "1" || $step == "2") {
    $errors = validateMemberData($formData);
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        $_SESSION['form_data'] = $formData;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// 登録処理
if ($step == "2" && empty($errors)) {
    try {
        $result = insertMember($formData['first_name'], $formData['last_name']);
        
        if ($result) {
            $_SESSION['success_message'] = "メンバーを登録しました";
            header('Location: member_list.php');
            exit;
        } else {
            $_SESSION['error_message'] = "メンバーの登録に失敗しました";
            $_SESSION['form_data'] = $formData;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (Exception $e) {
        error_log("メンバー登録エラー: " . $e->getMessage());
        $_SESSION['error_message'] = "システムエラーが発生しました";
        $_SESSION['form_data'] = $formData;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ビューで使用する変数を設定
$sLastName = $formData['last_name'];
$sFirstName = $formData['first_name'];
$arrErr = $errors;

// ビューの表示
if ($step === "") {
    require_once('../view/member_insert.html');
} elseif ($step == "1") {
    require_once('../view/member_insertCheck.html');
}
?>