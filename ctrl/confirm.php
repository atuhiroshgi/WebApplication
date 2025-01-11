<?php
session_start();

require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// カート内商品情報を取得
$cartItems = getCartItems(isset($_SESSION['cart']) ? $_SESSION['cart'] : []);

// カートが空の場合はカートページにリダイレクト
if (empty($cartItems)) {
    header('Location: ../ctrl/cart.php');
    exit;
}

// 合計金額の計算
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['item_price'] * $item['quantity'];
}

// ビューの表示
require_once('../view/confirm.html'); 