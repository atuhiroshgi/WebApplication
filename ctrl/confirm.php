<?php
session_start();

require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// 注文確認データを取得
$orderData = getOrderConfirmationData(isset($_SESSION['cart']) ? $_SESSION['cart'] : []);

// カートが空の場合はカートページにリダイレクト
if ($orderData === false) {
    header('Location: ../ctrl/cart.php');
    exit;
}

// ビューの表示
require_once('../view/confirm.html'); 