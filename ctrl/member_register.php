<?php
//**************************************************
// 初期処理
//**************************************************
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

//**************************************************
// 変数定義
//**************************************************
$arrErr = array();
$bRet = false;

//**************************************************
// 変数取得
//**************************************************
$sLastName = isset($_POST['last_name']) ? $_POST['last_name'] : "";
$sFirstName = isset($_POST['first_name']) ? $_POST['first_name'] : "";
$nAge = isset($_POST['age']) ? $_POST['age'] : "";
$sPostalCode = isset($_POST['postal_code']) ? $_POST['postal_code'] : "";
$sAddress = isset($_POST['address']) ? $_POST['address'] : "";
$sPhone = isset($_POST['phone']) ? $_POST['phone'] : "";
$sEmail = isset($_POST['email']) ? $_POST['email'] : "";
$sPassword = isset($_POST['password']) ? $_POST['password'] : "";
$sPasswordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : "";
$nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// 入力チェック
//**************************************************
if($nStepFlg == 1 || $nStepFlg == 2){
    // 氏名チェック
    if($sLastName == ""){
        $arrErr['last_name'] = "姓を入力してください";
    }
    else if(mb_strlen($sLastName, "UTF-8") > 20) {
        $arrErr['last_name'] = "姓は20文字以内で入力してください";
    }

    if($sFirstName == ""){
        $arrErr['first_name'] = "名を入力してください";
    }
    else if(mb_strlen($sFirstName, "UTF-8") > 20) {
        $arrErr['first_name'] = "名は20文字以内で入力してください";
    }

    // 郵便番号チェック
    if($sPostalCode == ""){
        $arrErr['postal_code'] = "郵便番号を入力してください";
    }
    else {
        // ハイフンを除去して数字のみにする
        $cleanPostalCode = str_replace('-', '', $sPostalCode);
        if(!preg_match("/^\d{7}$/", $cleanPostalCode)) {
            $arrErr['postal_code'] = "郵便番号は7桁の数字で入力してください（例：123-4567）";
        }
    }

    // 住所チェック
    if($sAddress == ""){
        $arrErr['address'] = "住所を入力してください";
    }
    else if(mb_strlen($sAddress, "UTF-8") > 100) {
        $arrErr['address'] = "住所は100文字以内で入力してください";
    }

    // 電話番号チェック
    if($sPhone == ""){
        $arrErr['phone'] = "電話番号を入力してください";
    }
    else {
        // ハイフンを除去して数字のみにする
        $cleanPhone = str_replace('-', '', $sPhone);
        if(!preg_match("/^0\d{9,10}$/", $cleanPhone)) {
            $arrErr['phone'] = "電話番号は正しい形式で入力してください（例：090-1234-5678）";
        }
    }

    // メールアドレスチェック
    if($sEmail == ""){
        $arrErr['email'] = "メールアドレスを入力してください";
    }
    else if(!filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
        $arrErr['email'] = "正しいメールアドレスの形式で入力してください（例：example@example.com）";
    }
    else {
        // メールアドレスの重複チェック
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->bindValue(':email', $sEmail);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $arrErr['email'] = "このメールアドレスは既に登録されています";
        }
    }

    // パスワードチェック
    if($sPassword == ""){
        $arrErr['password'] = "パスワードを入力してください";
    }
    else if(strlen($sPassword) < 8) {
        $arrErr['password'] = "パスワードは8文字以上で入力してください";
    }
    else if(!preg_match("/^[a-zA-Z0-9]+$/", $sPassword)) {
        $arrErr['password'] = "パスワードは半角英数字のみ使用できます";
    }

    // パスワード確認チェック
    if($sPasswordConfirm == ""){
        $arrErr['password_confirm'] = "確認用パスワードを入力してください";
    }
    else if($sPassword !== $sPasswordConfirm) {
        $arrErr['password_confirm'] = "パスワードが一致しません";
    }

    // 年齢チェック
    if($nAge != "" && (!is_numeric($nAge) || $nAge < 0 || $nAge > 150)) {
        $arrErr['age'] = "年齢は0から150の間で入力してください";
    }

    // エラーがある場合は入力画面に戻す
    if(count($arrErr) > 0){
        $nStepFlg = "";
    }
}

//**************************************************
// 登録処理
//**************************************************
if($nStepFlg == 2 && count($arrErr) == 0){
    $bRet = registerMember($sLastName, $sFirstName, $sPostalCode, $sAddress, $sPhone, $sEmail, $sPassword);

    if($bRet === false){
        $arrErr['common'] = "登録に失敗しました。メールアドレスが既に使用されている可能性があります。";
        $nStepFlg = "";
    }
}

//**************************************************
// HTML表示
//**************************************************
if($nStepFlg == ""){
    require_once('../view/member_register.html');
} else if ($nStepFlg == 1) {
    require_once('../view/member_register_check.html');
} else if ($nStepFlg == 2) {
    require_once('../view/member_register_ok.html');
}

// 登録完了後、ログインページへリダイレクト
if ($bRet) {
    header('Location: login.php?message=会員登録が完了しました。登録したメールアドレスとパスワードでログインしてください。');
    exit;
}
?> 