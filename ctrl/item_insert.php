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
$bRet = false;
$arrErr = array();

//**************************************************
// 変数取得
//**************************************************
$sItemName = isset($_POST['item_name']) ? $_POST['item_name'] : "";
$nItemPrice = isset($_POST['item_price']) ? $_POST['item_price'] : "";
$sItemText = isset($_POST['item_text']) ? $_POST['item_text'] : "";
$nCategoryId = isset($_POST['category_id']) ? $_POST['category_id'] : "0";
$nStopFlg = isset($_POST['stop_flg']) ? $_POST['stop_flg'] : "0";
$sItemImage = isset($_POST['item_image']) ? $_POST['item_image'] : "";
$nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// 入力チェック
//**************************************************
if($nStepFlg == 1 || $nStepFlg == 2){
    // 商品名チェック
    if($sItemName == ""){
        $arrErr['item_name'] = "商品名を入力してください";
    }
    else if(mb_strlen($sItemName, "UTF-8") > 50) {
        $arrErr['item_name'] = "商品名は50文字以内で入力してください";
    }

    // 価格チェック
    if($nItemPrice == ""){
        $arrErr['item_price'] = "価格を入力してください";
    }
    else if(!is_numeric($nItemPrice) || $nItemPrice < 0 || $nItemPrice > 8388607) {
        $arrErr['item_price'] = "価格は0以上8,388,607以下の数値で入力してください";
    }

    // エラーがある場合は入力画面に戻る
    if(count($arrErr) > 0){
        $_SESSION['error_message'] = "入力内容に問題があります：<br>" . implode("<br>", $arrErr);
        header('Location: index.php');
        exit;
    }
}

//**************************************************
// 商品登録処理
//**************************************************
if($nStepFlg == 2 && count($arrErr) == 0){
    $bRet = insertItem($sItemName, $nItemPrice, $sItemText, $nCategoryId, $nStopFlg, $sItemImage);

    if($bRet == false){
        $_SESSION['error_message'] = "商品の登録に失敗しました。";
        header('Location: index.php');
        exit;
    }
}

//**************************************************
// HTML表示
//**************************************************
if($nStepFlg == ""){
    require_once('../view/item_insert.html');
} else if ($nStepFlg == 1) {
    require_once('../view/item_insertCheck.html');
} else if ($nStepFlg == 2) {
    $_SESSION['success_message'] = "商品を登録しました";
    header('Location: index.php');
    exit;
}
?> 