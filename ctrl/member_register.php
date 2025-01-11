<?php
session_start();
require_once('../model/dbconnect.php');
require_once('../model/dbfunction.php');

// 変数の初期化
$formData = [
    'last_name' => isset($_POST['last_name']) ? $_POST['last_name'] : "",
    'first_name' => isset($_POST['first_name']) ? $_POST['first_name'] : "",
    'age' => isset($_POST['age']) ? $_POST['age'] : "",
    'postal_code' => isset($_POST['postal_code']) ? $_POST['postal_code'] : "",
    'address' => isset($_POST['address']) ? $_POST['address'] : "",
    'phone' => isset($_POST['phone']) ? $_POST['phone'] : "",
    'email' => isset($_POST['email']) ? $_POST['email'] : "",
    'password' => isset($_POST['password']) ? $_POST['password'] : "",
    'password_confirm' => isset($_POST['password_confirm']) ? $_POST['password_confirm'] : ""
];

// セッションからフォームデータを復元（エラー時）
if (isset($_SESSION['form_data'])) {
    $formData = array_merge($formData, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}

$step = isset($_POST['step']) ? $_POST['step'] : "";
$errors = [];

// バリデーション処理
function validateMemberData($data, $pdo) {
    $errors = [];
    
    // 氏名チェック
    if ($data['last_name'] === "") {
        $errors['last_name'] = "姓を入力してください";
    } elseif (mb_strlen($data['last_name'], "UTF-8") > 20) {
        $errors['last_name'] = "姓は20文字以内で入力してください";
    }

    if ($data['first_name'] === "") {
        $errors['first_name'] = "名を入力してください";
    } elseif (mb_strlen($data['first_name'], "UTF-8") > 20) {
        $errors['first_name'] = "名は20文字以内で入力してください";
    }

    // 郵便番号チェック
    if ($data['postal_code'] === "") {
        $errors['postal_code'] = "郵便番号を入力してください";
    } else {
        $cleanPostalCode = str_replace('-', '', $data['postal_code']);
        if (!preg_match("/^\d{7}$/", $cleanPostalCode)) {
            $errors['postal_code'] = "郵便番号は7桁の数字で入力してください（例：123-4567）";
        }
    }

    // 住所チェック
    if ($data['address'] === "") {
        $errors['address'] = "住所を入力してください";
    } elseif (mb_strlen($data['address'], "UTF-8") > 100) {
        $errors['address'] = "住所は100文字以内で入力してください";
    }

    // 電話番号チェック
    if ($data['phone'] === "") {
        $errors['phone'] = "電話番号を入力してください";
    } else {
        $cleanPhone = str_replace('-', '', $data['phone']);
        if (!preg_match("/^0\d{9,10}$/", $cleanPhone)) {
            $errors['phone'] = "電話番号は正しい形式で入力してください（例：090-1234-5678）";
        }
    }

    // メールアドレスチェック
    if ($data['email'] === "") {
        $errors['email'] = "メールアドレスを入力してください";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "正しいメールアドレスの形式で入力してください";
    } else {
        // メールアドレスの重複チェック
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->bindValue(':email', $data['email']);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $errors['email'] = "このメールアドレスは既に登録されています";
        }
    }

    // パスワードチェック
    if ($data['password'] === "") {
        $errors['password'] = "パスワードを入力してください";
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = "パスワードは8文字以上で入力してください";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $data['password'])) {
        $errors['password'] = "パスワードは半角英数字のみ使用できます";
    }

    // パスワード確認チェック
    if ($data['password_confirm'] === "") {
        $errors['password_confirm'] = "確認用パスワードを入力してください";
    } elseif ($data['password'] !== $data['password_confirm']) {
        $errors['password_confirm'] = "パスワードが一致しません";
    }

    // 年齢チェック（任意項目）
    if ($data['age'] !== "" && (!is_numeric($data['age']) || $data['age'] < 0 || $data['age'] > 150)) {
        $errors['age'] = "年齢は0から150の間で入力してください";
    }

    return $errors;
}

// 入力チェックと確認画面表示
if ($step == "1" || $step == "2") {
    $pdo = db_connect();
    $errors = validateMemberData($formData, $pdo);
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        $_SESSION['form_data'] = $formData;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// 登録処理
if ($step == "2" && empty($errors)) {
    try {
        $result = registerMember(
            $formData['last_name'],
            $formData['first_name'],
            $formData['postal_code'],
            $formData['address'],
            $formData['phone'],
            $formData['email'],
            $formData['password']
        );
        
        if ($result) {
            header('Location: login.php?message=会員登録が完了しました。登録したメールアドレスとパスワードでログインしてください。');
            exit;
        } else {
            $_SESSION['error_message'] = "登録に失敗しました。メールアドレスが既に使用されている可能性があります。";
            $_SESSION['form_data'] = $formData;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (Exception $e) {
        error_log("会員登録エラー: " . $e->getMessage());
        $_SESSION['error_message'] = "システムエラーが発生しました";
        $_SESSION['form_data'] = $formData;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ビューで使用する変数を設定
$sLastName = $formData['last_name'];
$sFirstName = $formData['first_name'];
$nAge = $formData['age'];
$sPostalCode = $formData['postal_code'];
$sAddress = $formData['address'];
$sPhone = $formData['phone'];
$sEmail = $formData['email'];
$sPassword = $formData['password'];
$sPasswordConfirm = $formData['password_confirm'];
$arrErr = $errors;

// ビューの表示
if ($step === "") {
    require_once('../view/member_register.html');
} elseif ($step == "1") {
    require_once('../view/member_register_check.html');
} elseif ($step == "2") {
    require_once('../view/member_register_ok.html');
}
?> 