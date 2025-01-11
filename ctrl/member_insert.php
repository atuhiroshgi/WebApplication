<?php
//**************************************************
// 初期処理
//**************************************************
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

//**************************************************
// 変数定義
//**************************************************
$bRet = false;
$arrErr = array();

//**************************************************
// 変数取得
//**************************************************
$sLastName = isset($_POST['last_name']) ? $_POST['last_name'] : "";
$sFirstName = isset($_POST['first_name']) ? $_POST['first_name'] : "";
$nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// 入力チェック
//**************************************************
if($nStepFlg == 1 || $nStepFlg == 2){
    // 苗字チェック
    if($sLastName == ""){
        $arrErr['last_name'] = "苗字を入力してください";
    }
    else if(mb_strlen($sLastName, "UTF-8") > 10) {
        $arrErr['last_name'] = "苗字は10文字以内で入力してください";
    }

    // 名前チェック
    if($sFirstName == ""){
        $arrErr['first_name'] = "名前を入力してください";
    }
    else if(mb_strlen($sFirstName, "UTF-8") > 10) {
        $arrErr['first_name'] = "名前は10文字以内で入力してください";
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
    $bRet = insertMember($sFirstName, $sLastName);

    // 登録失敗時は入力画面に戻す
    if($bRet == false){
        $nStepFlg = "";
    }
}

//**************************************************
// HTML表示
//**************************************************
if($nStepFlg == ""){
    require_once('../view/member_insert.html');
} else if ($nStepFlg == 1) {
    require_once('../view/member_insertCheck.html');
} else if ($nStepFlg == 2) {
    require_once('../view/member_insertOK.html');
}

// 登録完了後、一覧画面へリダイレクト
if ($bRet) {
    header('Location: member_list.php?message=メンバーを登録しました');
    exit;
}
?>