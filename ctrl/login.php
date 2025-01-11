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

//**************************************************
// 変数取得
//**************************************************
$sLoginId = isset($_POST['login_id']) ? $_POST['login_id'] : "";
$sLoginPass = isset($_POST['login_pass']) ? $_POST['login_pass'] : "";
$nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// 入力チェック
//**************************************************
if($nStepFlg == 1){
    // ログインIDチェック
    if($sLoginId == ""){
        $arrErr['login_id'] = "ログインIDまたはメールアドレスを入力してください";
    }

    // パスワードチェック
    if($sLoginPass == ""){
        $arrErr['login_pass'] = "パスワードを入力してください";
    }
}

//**************************************************
// ログインチェック
//**************************************************
$loginOk = false;
if ($nStepFlg == 1 && count($arrErr) == 0) {
    $loginOk = loginCheck($sLoginId, $sLoginPass);
    
    if($loginOk === true){
        //ログイン情報をSESSIONに保存
        $_SESSION['login_id'] = $sLoginId;
        $_SESSION['login_pass'] = $sLoginPass;

        //トップページへ遷移
        header("location: top.php");
        exit();
    }
    //ログインチェックNGで何か入力されているとき
    else if($sLoginId != "" || $sLoginPass != "") {
        $arrErr['common'] = "ログインできませんでした。";
    }
}

//**************************************************
// HTMLを出力
//**************************************************
require_once('../view/login.html');
?>