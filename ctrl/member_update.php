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
    // フォームからのPOSTデータ取得
    $sMemberId = isset($_POST['id']) ? $_POST['id'] : "";
    $sLastName = isset($_POST['last_name']) ? $_POST['last_name'] : "";
    $sFirstName = isset($_POST['first_name']) ? $_POST['first_name'] : "";
    $nAge = isset($_POST['age']) ? $_POST['age'] : "";
    $sPostalCode = isset($_POST['postal_code']) ? $_POST['postal_code'] : "";
    $sAddress = isset($_POST['address']) ? $_POST['address'] : "";
    $sPhone = isset($_POST['phone']) ? $_POST['phone'] : "";
    $sEmail = isset($_POST['email']) ? $_POST['email'] : "";
    $sLoginId = isset($_POST['login_id']) ? $_POST['login_id'] : "";
    $sLoginPass = isset($_POST['login_pass']) ? $_POST['login_pass'] : "";
    $nStepFlg = isset($_POST['step']) ? $_POST['step'] : "";

//**************************************************
// STEP0（検索処理）
//**************************************************
    if($nStepFlg == ""){
        //メンバー情報の取得
        $arrResult = selectMember($sMemberId);
        if (!empty($arrResult)) {
            //各項目の値を取得
            $sLastName = $arrResult[0]['last_name'];
            $sFirstName = $arrResult[0]['first_name'];
            $nAge = $arrResult[0]['age'];
            $sPostalCode = $arrResult[0]['postal_code'];
            $sAddress = $arrResult[0]['address'];
            $sPhone = $arrResult[0]['phone'];
            $sEmail = $arrResult[0]['email'];
            $sLoginId = $arrResult[0]['login_id'];
        }
    }

//**************************************************
// STEP1（確認画面）
//**************************************************
    if($nStepFlg == 1 || $nStepFlg == 2){
        // 各項目のバリデーション
        if($sLastName == ""){
            $arrErr['last_name'] = "苗字を入力してください";
        }
        else if(mb_strlen($sLastName, "UTF-8") > 50) {
            $arrErr['last_name'] = "苗字は50文字以内で入力してください";
        }

        if($sFirstName == ""){
            $arrErr['first_name'] = "名前を入力してください";
        }
        else if(mb_strlen($sFirstName, "UTF-8") > 50) {
            $arrErr['first_name'] = "名前は50文字以内で入力してください";
        }

        if($nAge != "" && (!is_numeric($nAge) || $nAge < 0 || $nAge > 150)) {
            $arrErr['age'] = "年齢は0から150の間で入力してください";
        }

        if($sPostalCode != "" && !preg_match("/^\d{3}-?\d{4}$/", $sPostalCode)) {
            $arrErr['postal_code'] = "郵便番号は123-4567の形式で入力してください";
        }

        if($sPhone != "" && !preg_match("/^\d{2,4}-?\d{2,4}-?\d{4}$/", $sPhone)) {
            $arrErr['phone'] = "電話番号は正しい形式で入力してください";
        }

        if($sEmail != "" && !filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
            $arrErr['email'] = "メールアドレスの形式が正しくありません";
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
        //データ更新
        $bRet = updateMember($sMemberId, $sFirstName, $sLastName, $nAge, 
                           $sPostalCode, $sAddress, $sPhone, $sEmail, 
                           $sLoginId, $sLoginPass);

        //DB更新エラーがある場合は最初のステップに戻す
        if($bRet == false){
            $nStepFlg = "";
        }
    }

//**************************************************
// HTMLを出力
//**************************************************
    if($nStepFlg == ""){
        require_once('../view/member_update.html');
    } else if ($nStepFlg == 1) {
        require_once('../view/member_updateCheck.html');
    } else if ($nStepFlg == 2) {
        require_once('../view/member_updateOK.html');
    }
?>
