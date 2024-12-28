<?php
//**************************************************
// 初期処理
//**************************************************
    //SESSIONスタート
    session_start();

    //データベース接続関数の定義ファイルを読み込み
    require_once('../model/dbconnect.php');

    //データベース操作関数の定義ファイルを読み込み
    require_once('../model/dbfunction.php');

//**************************************************
// 変数定義
//**************************************************
    //エラーメッセージ用
    $arrErr = array();


//**************************************************
// 変数取得
//**************************************************
    //ログインID
    $sLoginId = isset($_POST['login_id']) ? $_POST['login_id'] : "";

    //ログインパスワード
    $sLoginPass = isset($_POST['login_pass']) ? $_POST['login_pass'] : "";

    //処理ステップ
    $nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";


//**************************************************
// STEP1（確認画面）
//**************************************************
    if($nStepFlg == 1){

        // ログインIDチェック
        if($sLoginId == ""){
            $arrErr['login_id'] = "ログインIDを入力してください";
        }
        else if(mb_strlen($sLoginId, "UTF-8") > 20) {
            $arrErr['login_id'] = "ログインIDは20文字以内で入力してください";
        }

        // パスワードチェック
        if($sLoginPass == ""){
            $arrErr['login_pass'] = "パスワードを入力してください";
        }
        else if(mb_strlen($sLoginPass, "UTF-8") > 20) {
            $arrErr['login_pass'] = "パスワードは20文字以内で入力してください";
        }
    }

//**************************************************
// ログインチェック
//**************************************************
    //ログインチェックを取得
    $loginOk = loginCheck($sLoginId, $sLoginPass);

    //ログインチェックがOKなら
    if($loginOk === true){
        //ログイン情報をSESSIONに保存
        $_SESSION['login_id']   = $sLoginId;
        $_SESSION['login_pass'] = $sLoginPass;

        //トップページへ遷移
        header("location: top.php");
        exit();
    }
    //ログインチェックNGで何か入力されているとき
    else if($sLoginId != "" || $sLoginPass != "") {
        $arrErr['common'] = "ログインできませんでした。";
    }
    //それ以外
    else {
        $arrErr['common'] = "";
   }

//**************************************************
// HTMLを出力
//**************************************************
    //画面へ表示
    require_once('../view/login.html');

?>