<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// 変数の初期化
$formData = [
    'login_id' => isset($_POST['login_id']) ? $_POST['login_id'] : "",
    'login_pass' => isset($_POST['login_pass']) ? $_POST['login_pass'] : "",
];
$step = isset($_POST['step']) ? $_POST['step'] : "";
$errors = [];

// バリデーション処理
function validateLoginData($data) {
    $errors = [];
    
    // ログインIDチェック
    if ($data['login_id'] === "") {
        $errors['login_id'] = "ログインIDまたはメールアドレスを入力してください";
    }
    
    // パスワードチェック
    if ($data['login_pass'] === "") {
        $errors['login_pass'] = "パスワードを入力してください";
    }
    
    return $errors;
}

// ログイン処理
if ($step == 1) {
    // 入力チェック
    $errors = validateLoginData($formData);
    
    // エラーがない場合はログインチェック
    if (empty($errors)) {
        $loginResult = loginCheck($formData['login_id'], $formData['login_pass']);
        
        if ($loginResult === true) {
            // ログイン情報をSESSIONに保存
            $_SESSION['login_id'] = $formData['login_id'];
            $_SESSION['login_pass'] = $formData['login_pass'];
            
            // トップページへ遷移
            header("Location: top.php");
            exit();
        } else if ($formData['login_id'] !== "" || $formData['login_pass'] !== "") {
            $errors['common'] = "ログインできませんでした。";
        }
    }
}

// ビューで使用する変数を設定
$sLoginId = $formData['login_id'];
$sLoginPass = $formData['login_pass'];
$arrErr = $errors;

// ビューの表示
require_once('../view/login.html');
?>