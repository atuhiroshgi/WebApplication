<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// 変数の初期化
$formData = [
    'item_name' => isset($_POST['item_name']) ? $_POST['item_name'] : "",
    'item_price' => isset($_POST['item_price']) ? $_POST['item_price'] : "",
    'item_text' => isset($_POST['item_text']) ? $_POST['item_text'] : "",
    'category_id' => isset($_POST['category_id']) ? $_POST['category_id'] : "0",
    'stop_flg' => isset($_POST['stop_flg']) ? $_POST['stop_flg'] : "0",
    'item_image' => isset($_POST['item_image']) ? $_POST['item_image'] : "",
];

// セッションからフォームデータを復元（エラー時）
if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']); // 使用後はセッションから削除
}

$step = isset($_POST['step']) ? $_POST['step'] : "";

// バリデーション処理
function validateItemData($data) {
    $errors = [];
    
    // 商品名チェック
    if($data['item_name'] === "") {
        $errors['item_name'] = "商品名を入力してください";
    } elseif(mb_strlen($data['item_name'], "UTF-8") > 50) {
        $errors['item_name'] = "商品名は50文字以内で入力してください";
    }

    // 価格チェック
    if($data['item_price'] === "") {
        $errors['item_price'] = "価格を入力してください";
    } elseif(!is_numeric($data['item_price']) || $data['item_price'] < 0 || $data['item_price'] > 8388607) {
        $errors['item_price'] = "価格は0以上8,388,607以下の数値で入力してください";
    }

    return $errors;
}

// メイン処理
if ($step == 1 || $step == 2) {
    $errors = validateItemData($formData);
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = "入力内容に問題があります：<br>" . implode("<br>", $errors);
        $_SESSION['form_data'] = $formData; // 入力値の保持
        header('Location: index.php');
        exit;
    }
}

// 商品登録処理
if ($step == 2) {
    $result = insertItem(
        $formData['item_name'],
        $formData['item_price'],
        $formData['item_text'],
        $formData['category_id'],
        $formData['stop_flg'],
        $formData['item_image']
    );

    if (!$result) {
        $_SESSION['error_message'] = "商品の登録に失敗しました。";
        $_SESSION['form_data'] = $formData; // 入力値の保持
        header('Location: index.php');
        exit;
    }
    
    $_SESSION['success_message'] = "商品を登録しました";
    header('Location: index.php');
    exit;
}

// ビューで使用する変数を設定（共通処理）
$sItemName = $formData['item_name'];
$sItemPrice = $formData['item_price'];
$nItemPrice = $formData['item_price']; // 追加：確認画面用
$sItemText = $formData['item_text'];
$nCategoryId = $formData['category_id'];
$nStopFlg = $formData['stop_flg'];
$sItemImage = $formData['item_image'];

// ビューの表示
if ($step === "") {
    require_once('../view/item_insert.html');
} elseif ($step == 1) {
    $_SESSION['form_data'] = $formData; // 確認画面用にデータを保持
    require_once('../view/item_insertCheck.html');
}
?> 