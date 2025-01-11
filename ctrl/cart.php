<?php
//**************************************************
// 初期処理
//**************************************************
    session_start();
    require_once('../model/dbconnect.php');
    require_once('../model/dbfunction.php');

//**************************************************
// ログインチェック
//**************************************************
    $sLoginId = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : "";
    $sLoginPass = isset($_SESSION['login_pass']) ? $_SESSION['login_pass'] : "";

    $loginOk = loginCheck($sLoginId, $sLoginPass);
    if($loginOk !== true){
        header("location: login.php");
        exit();
    }

    $userName = getUserName($sLoginId, $sLoginPass);

//**************************************************
// カート処理
//**************************************************
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            // 数量更新
            updateCart($_SESSION['cart'], $_POST['item_id'], (int)$_POST['quantity']);
        } else if (isset($_POST['item_id'])) {
            // カートに追加
            updateCart($_SESSION['cart'], $_POST['item_id']);
        }
    }

    // カート内商品情報を取得
    $cartItems = getCartItems(isset($_SESSION['cart']) ? $_SESSION['cart'] : []);

//**************************************************
// HTMLを出力
//**************************************************
    require_once('../view/cart.html');
?>