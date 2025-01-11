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

// フォーム用の変数を初期化
$sItemId = "";
$sItemName = "";
$nItemPrice = "";
$sItemText = "";
$nCategoryId = "";
$nStopFlg = "";
$sItemImage = "";
$nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// 変数取得
//**************************************************
error_log("POST data: " . print_r($_POST, true));
error_log("Step flag: " . $nStepFlg);

// GETまたはPOSTから商品IDを取得
$itemId = isset($_GET['item_id']) ? $_GET['item_id'] : (isset($_POST['item_id']) ? $_POST['item_id'] : null);
error_log("Requested item_id: " . $itemId);

//**************************************************
// 商品情報取得
//**************************************************
$item = getItemById($itemId);
error_log("Retrieved item: " . print_r($item, true));

// 商品情報をフォーム用の変数に設定
if ($nStepFlg == "") {
    // 初期表示時は商品情報から取得
    if ($item) {
        $sItemId = $item['item_id'];
        $sItemName = $item['item_name'];
        $nItemPrice = $item['item_price'];
        $sItemText = $item['item_text'];
        $nCategoryId = $item['category_id'];
        $nStopFlg = (int)$item['stop_flg'];  // 確実に数値に変換
        $sItemImage = $item['item_image'];
        error_log("Initial display values - stop_flg: " . $nStopFlg);
    }
} else {
    // POST送信時はフォームの値を使用
    $sItemId = $itemId;
    $sItemName = isset($_POST['item_name']) ? $_POST['item_name'] : "";
    $nItemPrice = isset($_POST['item_price']) ? $_POST['item_price'] : "";
    $sItemText = isset($_POST['item_text']) ? $_POST['item_text'] : "";
    $nCategoryId = isset($_POST['category_id']) ? $_POST['category_id'] : "";
    $nStopFlg = isset($_POST['stop_flg']) ? ($_POST['stop_flg'] == '1' ? 1 : 0) : 0;
    $sItemImage = isset($_POST['item_image']) ? $_POST['item_image'] : "";
    
    // デバッグログ追加
    error_log("POST データ - stop_flg: " . (isset($_POST['stop_flg']) ? $_POST['stop_flg'] : 'not set'));
    error_log("変換後 - stop_flg: " . $nStopFlg);
}

//**************************************************
// 入力チェック
//**************************************************
if ($nStepFlg == "1") {
    // 商品名チェック
    if ($sItemName == "") {
        $arrErr['item_name'] = "商品名を入力してください";
    }
    
    // 価格チェック
    if ($nItemPrice == "") {
        $arrErr['item_price'] = "価格を入力してください";
    } elseif (!is_numeric($nItemPrice) || $nItemPrice < 0) {
        $arrErr['item_price'] = "価格は0以上の数値を入力してください";
    }

    // エラーがある場合は最初のステップに戻す
    if (count($arrErr) > 0) {
        $nStepFlg = "";
    }
}

//**************************************************
// 更新処理
//**************************************************
if ($nStepFlg == "2" && count($arrErr) == 0) {
    try {
        $pdo = db_connect();
        
        // 更新前のデータを確認
        error_log("=== 更新処理開始 ===");
        error_log("POST データ: " . print_r($_POST, true));
        
        // stop_flgの値を確実に整数に変換
        $nStopFlg = (isset($_POST['stop_flg'])) ? (int)$_POST['stop_flg'] : 1;
        error_log("変換後の stop_flg: " . $nStopFlg . " (型: " . gettype($nStopFlg) . ")");
        
        // 更新前のデータを取得
        $beforeUpdate = getItemById($sItemId);
        error_log("更新前のデータ: " . print_r($beforeUpdate, true));
        
        $result = updateItem($sItemId, $sItemName, $nItemPrice, $sItemText, $nCategoryId, $nStopFlg, $sItemImage);
        
        if ($result) {
            // 更新後のデータを確認
            $afterUpdate = getItemById($sItemId);
            error_log("更新後のデータ: " . print_r($afterUpdate, true));
            
            header("Location: ./index.php");
            exit;
        } else {
            error_log("更新失敗");
        }
    } catch (PDOException $e) {
        error_log("エラー発生: " . $e->getMessage());
    }
}

//**************************************************
// HTML表示
//**************************************************
if ($nStepFlg == "") {
    require_once('../view/item_update.html');
} else if ($nStepFlg == "1") {
    require_once('../view/item_updateCheck.html');
} else if ($nStepFlg == "2") {
    require_once('../view/item_update_ok.html');
}
?> 