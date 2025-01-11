<?php
session_start();

// カートを空にする
unset($_SESSION['cart']);

// 簡単な完了画面を表示
require_once("../view/complete.html")
?>