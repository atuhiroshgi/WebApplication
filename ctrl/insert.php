<?php
//**************************************************
// 初期処理
//**************************************************
    //データベース接続関数の定義ファイルを読み込み
    require_once('../model/dbconnect.php');

    //データベース操作関数の定義ファイルを読み込み
    require_once('../model/dbfunction.php');

//**************************************************
// 変数定義
//**************************************************
    //エラー検知用
    $bRet = false;

    //エラーメッセージ用
    $arrErr = array();

//**************************************************
// 変数取得
//**************************************************
    //苗字
    $sLastName = isset($_POST['last_name']) ? $_POST['last_name'] : "";

    //名前
    $sFirstName = isset($_POST['first_name']) ? $_POST['first_name'] : "";

    //処理ステップ
    $nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";


//**************************************************
// STEP1（確認画面）
//**************************************************
    if($nStepFlg == 1 || $nStepFlg == 2){

        // 苗字チェック
        if($sLastName == ""){
            $arrErr['last_name'] = "苗字を入力してください";
        }
        else if(mb_strlen($sLastName, "UTF-8") > 10) {
            $arrErr['last_name'] = "苗字は10文字以内で入力してください";
        }

        // 名前チェック
        if($sFirstName == ""){
            $arrErr['first_name'] = "名前を入力してください";
        }
        else if(mb_strlen($sFirstName, "UTF-8") > 10) {
            $arrErr['first_name'] = "名前は10文字以内で入力してください";
        }

        //入力エラーがある場合は最初のステップに戻す
        if(count($arrErr) > 0){
            $nStepFlg = "";
        }
    }

//**************************************************
// STEP2（完了画面）
//**************************************************
    if($nStepFlg == 2 && count($arrErr) == 0){

        //データ登録
        $bRet = insertMember($sFirstName, $sLastName);

        //DB登録エラーがある場合は最初のステップに戻す
        if($bRet == false){
            $nStepFlg = "";
        }
    }

//**************************************************
// HTML表示
//**************************************************
    if($nStepFlg == ""){
        require_once('../view/insert.html');
    } else if ($nStepFlg == 1) {
        require_once('../view/insertCheck.html');
    } else if ($nStepFlg == 2) {
        require_once('../view/insertOK.html');
    }
?>