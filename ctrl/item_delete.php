<?php
//**************************************************
// 初期処理
//**************************************************
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

//**************************************************
// 変数取得
//**************************************************
$nItemId = isset($_POST['item_id']) ? $_POST['item_id'] : "";
$nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// 初期表示
//**************************************************
if($nStepFlg == "") {
    $item = getItemById($nItemId);
    if(!$item) {
        die('商品が見つかりません');
    }
}

//**************************************************
// STEP1（完了画面）
//**************************************************
$bRet = false;
if($nStepFlg == 1) {
    $bRet = deleteItem($nItemId);
}

//**************************************************
// HTML表示
//**************************************************
if($nStepFlg == "") {
    require_once('../view/item_delete.html');
} else if ($nStepFlg == 1) {
    require_once('../view/item_deleteOK.html');
}

// 削除完了後、index.phpにリダイレクト
if ($bRet) {
    header('Location: index.php?message=商品を削除しました');
    exit;
}
?> 