<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// 変数の初期化
$formData = [
    'item_id' => isset($_POST['item_id']) ? $_POST['item_id'] : (isset($_GET['item_id']) ? $_GET['item_id'] : ""),
    'item_name' => isset($_POST['item_name']) ? $_POST['item_name'] : "",
    'item_price' => isset($_POST['item_price']) ? $_POST['item_price'] : "",
    'item_text' => isset($_POST['item_text']) ? $_POST['item_text'] : "",
    'category_id' => isset($_POST['category_id']) ? $_POST['category_id'] : "",
    'stop_flg' => isset($_POST['stop_flg']) ? ($_POST['stop_flg'] == '1' ? 1 : 0) : 0,
    'item_image' => isset($_POST['item_image']) ? $_POST['item_image'] : ""
];

$step = isset($_POST['step']) ? $_POST['step'] : "";

// 商品情報の取得
if ($step === "") {
    $item = getItemById($formData['item_id']);
    if ($item) {
        $formData = [
            'item_id' => $item['item_id'],
            'item_name' => $item['item_name'],
            'item_price' => $item['item_price'],
            'item_text' => $item['item_text'],
            'category_id' => $item['category_id'],
            'stop_flg' => (int)$item['stop_flg'],
            'item_image' => $item['item_image']
        ];
    }
}

// バリデーション処理
function validateItemData($data) {
    $errors = [];
    
    // 商品名チェック
    if ($data['item_name'] === "") {
        $errors['item_name'] = "商品名を入力してください";
    }
    
    // 価格チェック
    if ($data['item_price'] === "") {
        $errors['item_price'] = "価格を入力してください";
    } elseif (!is_numeric($data['item_price']) || $data['item_price'] < 0) {
        $errors['item_price'] = "価格は0以上の数値を入力してください";
    }

    return $errors;
}

// 入力チェック
$errors = [];
if ($step == "1") {
    $errors = validateItemData($formData);
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        $_SESSION['form_data'] = $formData;
        header('Location: ' . $_SERVER['PHP_SELF'] . '?item_id=' . $formData['item_id']);
        exit;
    }
}

// 更新処理
if ($step == "2" && empty($errors)) {
    try {
        $result = updateItem(
            $formData['item_id'],
            $formData['item_name'],
            $formData['item_price'],
            $formData['item_text'],
            $formData['category_id'],
            $formData['stop_flg'],
            $formData['item_image']
        );

        if ($result) {
            $_SESSION['success_message'] = "商品情報を更新しました。";
            header("Location: ./index.php");
            exit;
        } else {
            $_SESSION['error_message'] = "更新に失敗しました。";
            $_SESSION['form_data'] = $formData;
            header('Location: ' . $_SERVER['PHP_SELF'] . '?item_id=' . $formData['item_id']);
            exit;
        }
    } catch (Exception $e) {
        error_log("更新エラー: " . $e->getMessage());
        $_SESSION['error_message'] = "システムエラーが発生しました。";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?item_id=' . $formData['item_id']);
        exit;
    }
}

// ビューで使用する変数を設定
$sItemId = $formData['item_id'];
$sItemName = $formData['item_name'];
$sItemPrice = $formData['item_price'];
$nItemPrice = $formData['item_price'];
$sItemText = $formData['item_text'];
$nCategoryId = $formData['category_id'];
$nStopFlg = $formData['stop_flg'];
$sItemImage = $formData['item_image'];

// ビューの表示
if ($step == "") {
    require_once('../view/item_update.html');
} else if ($step == "1") {
    require_once('../view/item_updateCheck.html');
} else if ($step == "2") {
    require_once('../view/item_update_ok.html');
}
?> 