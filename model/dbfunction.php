<?php

require_once 'dbconnect.php'; // データベース接続関数を含む外部ファイルの読み込み

/**
 * メンバー情報を取得する関数
 * 
 * @param int $sMemberId メンバーID（オプション）
 * @param string $sLastName 苗字（オプション）
 * @return array メンバー情報の配列
 */
function selectMember($sMemberId = "", $sLastName = "") {
    $arrResult = [];
    $pdo = db_connect();

    try {
        $sSql = "SELECT * FROM user";
        $sWhere = [];

        if ($sMemberId !== "") {
            $sWhere[] = "id = :id";
        }
        if ($sLastName !== "") {
            $sWhere[] = "last_name LIKE :last_name";
        }

        if (!empty($sWhere)) {
            $sSql .= " WHERE " . implode(" AND ", $sWhere);
        }

        $stmh = $pdo->prepare($sSql);

        if ($sMemberId !== "") {
            $stmh->bindValue(':id', (int)$sMemberId, PDO::PARAM_INT);
        }
        if ($sLastName !== "") {
            $stmh->bindValue(':last_name', "%" . $sLastName . "%", PDO::PARAM_STR);
        }

        $stmh->execute();
        $arrResult = $stmh->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return $arrResult;
}

/**
 * メンバーを新規登録する関数
 * 
 * @param string $sFirstName 名前
 * @param string $sLastName 苗字
 * @return bool 登録の成功/失敗
 */
function insertMember($sFirstName, $sLastName) {
    $pdo = db_connect();

    try {
        $sql = "INSERT INTO user (last_name, first_name) VALUES (:last_name, :first_name)";
        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(':last_name', $sLastName, PDO::PARAM_STR);
        $stmh->bindValue(':first_name', $sFirstName, PDO::PARAM_STR);

        $stmh->execute();
        return true;

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return false;
}

/**
 * メンバー情報を更新する関数
 * 
 * @param int $sMemberId メンバーID
 * @param string $sFirstName 名前
 * @param string $sLastName 苗字
 * @return bool 更新の成功/失敗
 */
function updateMember($sMemberId, $sFirstName, $sLastName) {
    $pdo = db_connect();

    try {
        $sql = "UPDATE user SET last_name = :last_name, first_name = :first_name WHERE id = :id";
        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(':id', (int)$sMemberId, PDO::PARAM_INT);
        $stmh->bindValue(':last_name', $sLastName, PDO::PARAM_STR);
        $stmh->bindValue(':first_name', $sFirstName, PDO::PARAM_STR);

        $stmh->execute();
        return true;

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return false;
}

/**
 * メンバーを削除する関数
 * 
 * @param int $sMemberId メンバーID
 * @return bool 削除の成功/失敗
 */
function deleteMember($sMemberId) {
    $pdo = db_connect();

    try {
        $sql = "DELETE FROM user WHERE id = :id";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(':id', (int)$sMemberId, PDO::PARAM_INT);

        $stmh->execute();
        return true;

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return false;
}

/**
 * ログイン認証を行う関数
 * 
 * @param string $sLoginId ログインID
 * @param string $sLoginPass ログインパスワード（ハッシュ化推奨）
 * @return bool 認証の成功/失敗
 */
function loginCheck($sLoginId = "", $sLoginPass = "") {
    $pdo = db_connect();

    try {
        $sql = "SELECT * FROM shop_member WHERE login_id = :login_id AND login_pass = :login_pass";
        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(':login_id', $sLoginId, PDO::PARAM_STR);
        $stmh->bindValue(':login_pass', $sLoginPass, PDO::PARAM_STR);

        $stmh->execute();
        $arrUser = $stmh->fetch(PDO::FETCH_ASSOC);

        return $arrUser !== false;

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return false;
}

/**
 * ログインユーザー名を取得する関数
 * 
 * @param string $sLoginId ログインID
 * @param string $sLoginPass ログインパスワード（ハッシュ化推奨）
 * @return string ログインユーザー名
 */
function getUserName($sLoginId = "", $sLoginPass = "") {
    $pdo = db_connect();
    $sUserName = "";

    try {
        $sql = "SELECT last_name, first_name FROM shop_member WHERE login_id = :login_id AND login_pass = :login_pass";
        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(':login_id', $sLoginId, PDO::PARAM_STR);
        $stmh->bindValue(':login_pass', $sLoginPass, PDO::PARAM_STR);

        $stmh->execute();
        $arrUser = $stmh->fetch(PDO::FETCH_ASSOC);

        if ($arrUser) {
            $sUserName = $arrUser["last_name"] . " " . $arrUser["first_name"];
        }

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return $sUserName;
}

/**
 * エラーハンドリング関数
 * 
 * @param PDOException $Exception 発生した例外オブジェクト
 */
function handleError($Exception) {
    error_log('Error: ' . $Exception->getMessage());
    die('システムエラーが発生しました。管理者にお問い合わせください。');
}

?>
