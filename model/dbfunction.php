<?php

require_once 'dbconnect.php'; // データベース接続関数を含む外部ファイルの読み込み

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * メンバー情報を取得する関数
 * 
 * @param int $sMemberId メンバーID（オプション）
 * @param string $sLastName 苗字（オプション）
 * @return array メンバー情報の配列
 */
function selectMember($memberId = "", $lastName = "", $address = "") {
    try {
        $pdo = db_connect();
        
        $where = array();
        $params = array();
        
        // ID検索
        if (!empty($memberId)) {
            $where[] = "id = :id";
            $params[':id'] = $memberId;
        }
        
        // 苗字検索
        if (!empty($lastName)) {
            $where[] = "last_name LIKE :last_name";
            $params[':last_name'] = '%' . $lastName . '%';
        }
        
        // 住所検索
        if (!empty($address)) {
            $where[] = "address LIKE :address";
            $params[':address'] = '%' . $address . '%';
        }
        
        // WHERE句の構築
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = "SELECT * FROM user {$whereClause} ORDER BY id DESC";
        error_log("Search SQL: " . $sql); // デバッグ用
        
        $stmh = $pdo->prepare($sql);
        
        // パラメータのバインド
        foreach ($params as $key => $val) {
            $stmh->bindValue($key, $val, PDO::PARAM_STR);
            error_log("Binding {$key} = {$val}"); // デバッグ用
        }
        
        $stmh->execute();
        return $stmh->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Member Search Error: " . $e->getMessage());
        throw $e;
    }
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
 * @param string $sLoginId ログインID（メールアドレスまたはログインID）
 * @param string $sLoginPass ログインパスワード
 * @return bool 認証の成功/失敗
 */
function loginCheck($sLoginId = "", $sLoginPass = "") {
    $pdo = db_connect();

    try {
        // メールアドレスまたはログインIDでユーザーを検索
        $sql = "SELECT * FROM user WHERE (login_id = :login_id OR email = :email)";
        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(':login_id', $sLoginId, PDO::PARAM_STR);
        $stmh->bindValue(':email', $sLoginId, PDO::PARAM_STR);

        $stmh->execute();
        $user = $stmh->fetch(PDO::FETCH_ASSOC);

        // ユーザーが存在し、パスワードが一致する場合
        if ($user && password_verify($sLoginPass, $user['login_pass'])) {
            return true;
        }

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return false;
}

/**
 * ログインユーザー名を取得する関数
 * 
 * @param string $sLoginId ログインID（メールアドレスまたはログインID）
 * @param string $sLoginPass ログインパスワード
 * @return string ログインユーザー名
 */
function getUserName($sLoginId = "", $sLoginPass = "") {
    $pdo = db_connect();
    $sUserName = "";

    try {
        // メールアドレスまたはログインIDでユーザーを検索
        $sql = "SELECT * FROM user WHERE (login_id = :login_id OR email = :email)";
        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(':login_id', $sLoginId, PDO::PARAM_STR);
        $stmh->bindValue(':email', $sLoginId, PDO::PARAM_STR);

        $stmh->execute();
        $user = $stmh->fetch(PDO::FETCH_ASSOC);

        // ユーザーが存在し、パスワードが一致する場合
        if ($user && password_verify($sLoginPass, $user['login_pass'])) {
            $sUserName = $user["last_name"] . " " . $user["first_name"];
        }

    } catch (PDOException $Exception) {
        handleError($Exception);
    }

    return $sUserName;
}

/**
 * 商品一覧取得
 * 
 * @return array 商品一覧
 */
function selectItem() {
    try {
        $pdo = db_connect();
        
        // stop_flgの条件を削除して全ての商品を取得
        $sql = "SELECT * FROM item ORDER BY item_id DESC";
        
        $stmh = $pdo->prepare($sql);
        $stmh->execute();
        
        return $stmh->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Item Select Error: " . $e->getMessage());
        return array();
    }
}

/**
 * 商品検索
 * 
 * @param string $itemId 商品ID
 * @param string $itemName 商品名
 * @return array 検索結果
 */
function searchItems($conditions) {
    global $dbh;
    
    $sql = "SELECT * FROM item WHERE 1=1";
    $params = [];
    
    // 既存の検索条件
    if (!empty($conditions['item_id'])) {
        $sql .= " AND item_id = ?";
        $params[] = $conditions['item_id'];
    }
    if (!empty($conditions['item_name'])) {
        $sql .= " AND item_name LIKE ?";
        $params[] = '%' . $conditions['item_name'] . '%';
    }
    if (!empty($conditions['item_price'])) {
        $sql .= " AND item_price = ?";
        $params[] = $conditions['item_price'];
    }
    if (!empty($conditions['category_id'])) {
        $sql .= " AND category_id = ?";
        $params[] = $conditions['category_id'];
    }
    if (isset($conditions['stop_flg'])) {
        $sql .= " AND stop_flg = ?";
        $params[] = $conditions['stop_flg'];
    }
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 商品情報をデータベースに登録する
 * @param string $sItemName 商品名
 * @param int $nItemPrice 価格
 * @param string $sItemText 商品説明
 * @param int $nCategoryId カテゴリーID
 * @param int $nStopFlg 販売状態フラグ
 * @param string $sItemImage 商品画像
 * @return bool 成功:true 失敗:false
 */
function insertItem($sItemName, $nItemPrice, $sItemText, $nCategoryId, $nStopFlg, $sItemImage) {
    $bRet = false;
    
    try {
        // データベースに接続（既存の関数を使用）
        $conn = db_connect();

        // SQLを作成
        $sql = "INSERT INTO item (item_name, item_price, item_text, category_id, stop_flg, item_image) 
                VALUES (:item_name, :item_price, :item_text, :category_id, :stop_flg, :item_image)";
        
        // SQLを実行する準備
        $stmt = $conn->prepare($sql);
        
        // パラメータを設定
        $stmt->bindValue(':item_name', $sItemName);
        $stmt->bindValue(':item_price', $nItemPrice);
        $stmt->bindValue(':item_text', $sItemText);
        $stmt->bindValue(':category_id', $nCategoryId);
        $stmt->bindValue(':stop_flg', $nStopFlg);
        $stmt->bindValue(':item_image', $sItemImage);
        
        // SQLを実行
        $bRet = $stmt->execute();
        
    } catch(PDOException $e) {
        // エラー発生時は false を返却
        $bRet = false;
    }
    
    return $bRet;
}

/**
 * カートに商品を追加・更新する
 * 
 * @param array &$cart カートの参照
 * @param int $itemId 商品ID
 * @param int $quantity 数量（省略時は1を追加）
 * @return bool 処理結果
 */
function updateCart(&$cart, $itemId, $quantity = null) {
    if (!isset($cart)) {
        $cart = [];
    }
    
    if ($quantity === null) {
        // 数量指定がない場合は1つ追加
        $cart[$itemId] = isset($cart[$itemId]) ? $cart[$itemId] + 1 : 1;
    } else if ($quantity > 0) {
        // 数量を更新
        $cart[$itemId] = $quantity;
    } else {
        // 数量が0以下の場合は削除
        unset($cart[$itemId]);
    }
    
    return true;
}

/**
 * カート内の商品情報を取得
 * 
 * @param array $cart カート配列
 * @return array 商品情報の配列
 */
function getCartItems($cart) {
    $cartItems = [];
    if (!empty($cart)) {
        foreach ($cart as $itemId => $quantity) {
            $item = getItemById($itemId);
            if ($item) {
                $item['quantity'] = $quantity;
                $cartItems[] = $item;
            }
        }
    }
    return $cartItems;
}

/**
 * 商品を削除する関数
 * 
 * @param int $itemId 商品ID
 * @return bool 削除の成功/失敗
 */
function deleteItem($itemId) {
    try {
        $pdo = db_connect();
        $sql = "DELETE FROM item WHERE item_id = :item_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        handleError($e);
        return false;
    }
}

/**
 * 商品情報を更新する関数
 * 
 * @param int $itemId 商品ID
 * @param string $itemName 商品名
 * @param int $itemPrice 価格
 * @param string $itemText 商品説明
 * @param int $categoryId カテゴリーID
 * @param int $stopFlg 停止フラグ
 * @param string $itemImage 商品画像
 * @return bool 更新の成功/失敗
 */
function updateItem($itemId, $itemName, $itemPrice, $itemText, $categoryId, $stopFlg, $itemImage) {
    try {
        $pdo = db_connect();
        
        // デバッグ情報の出力
        error_log("=== updateItem 開始 ===");
        error_log("更新対象商品ID: " . $itemId);
        error_log("stop_flg: " . $stopFlg . " (型: " . gettype($stopFlg) . ")");
        
        // プリペアドステートメントの作成
        $sql = "UPDATE item 
                SET item_name = :item_name,
                    item_price = :item_price,
                    item_text = :item_text,
                    category_id = :category_id,
                    stop_flg = :stop_flg,
                    item_image = :item_image
                WHERE item_id = :item_id";
        
        $stmt = $pdo->prepare($sql);
        
        // バインド値の設定（型を明示的に指定）
        $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->bindValue(':item_name', $itemName, PDO::PARAM_STR);
        $stmt->bindValue(':item_price', $itemPrice, PDO::PARAM_INT);
        $stmt->bindValue(':item_text', $itemText, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':stop_flg', $stopFlg, PDO::PARAM_INT);
        $stmt->bindValue(':item_image', $itemImage, PDO::PARAM_STR);
        
        // バインドされた値の確認
        error_log("SQL: " . $sql);
        error_log("バインド値:");
        error_log("- item_id: " . $itemId);
        error_log("- item_name: " . $itemName);
        error_log("- item_price: " . $itemPrice);
        error_log("- category_id: " . $categoryId);
        error_log("- stop_flg: " . $stopFlg);
        error_log("- item_image: " . $itemImage);
        
        // クエリ実行
        $result = $stmt->execute();
        
        if ($result) {
            // 更新成功時、更新後のデータを確認
            $checkSql = "SELECT * FROM item WHERE item_id = :item_id";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
            $checkStmt->execute();
            $updatedData = $checkStmt->fetch(PDO::FETCH_ASSOC);
            error_log("更新後のデータ: " . print_r($updatedData, true));
        } else {
            error_log("更新失敗");
            error_log("エラー情報: " . print_r($stmt->errorInfo(), true));
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("データベースエラー: " . $e->getMessage());
        return false;
    }
}

/**
 * 新規会員を登録する関数
 * 
 * @param string $lastName 姓
 * @param string $firstName 名
 * @param string $postalCode 郵便番号
 * @param string $address 住所
 * @param string $phone 電話番号
 * @param string $email メールアドレス
 * @param string $password パスワード
 * @return bool 登録の成功/失敗
 */
function registerMember($lastName, $firstName, $postalCode, $address, $phone, $email, $password) {
    try {
        $pdo = db_connect();
        
        // メールアドレスの重複チェック
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        // パスワードのハッシュ化
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (
            last_name, first_name, postal_code, address, phone, 
            email, login_id, login_pass
        ) VALUES (
            :last_name, :first_name, :postal_code, :address, :phone,
            :email, :login_id, :login_pass
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':last_name', $lastName);
        $stmt->bindValue(':first_name', $firstName);
        $stmt->bindValue(':postal_code', $postalCode);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':login_id', $email);  // メールアドレスをログインIDとしても使用
        $stmt->bindValue(':login_pass', $hashedPassword);

        return $stmt->execute();

    } catch (PDOException $e) {
        handleError($e);
        return false;
    }
}

/**
 * 管理者ログインチェック
 * 
 * @param string $loginId ログインID
 * @param string $loginPass パスワード
 * @return string|false ログイン成功時はログインID、失敗時はfalse
 */
function adminLoginCheck($loginId, $loginPass) {
    try {
        $pdo = db_connect();
        
        $sql = "SELECT login_id FROM admin_user 
                WHERE login_id = :login_id 
                AND login_pass = :login_pass 
                AND stop_flg = 0";
                
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(':login_id', $loginId, PDO::PARAM_STR);
        $stmh->bindValue(':login_pass', $loginPass, PDO::PARAM_STR);
        $stmh->execute();
        
        $result = $stmh->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['login_id'] : false;
        
    } catch (PDOException $e) {
        error_log("Admin Login Error: " . $e->getMessage());
        return false;
    }
}

/**
 * ユーザー情報取得
 * 
 * @param int $userId ユーザーID
 * @return array ユーザー情報
 */
function selectUserById($userId) {
    try {
        $pdo = db_connect();
        
        $sql = "SELECT * FROM user WHERE id = :user_id";
        
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmh->execute();
        
        return $stmh->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("User Select Error: " . $e->getMessage());
        return array();
    }
}

/**
 * 商品IDによる商品情報取得
 * 
 * @param int $itemId 商品ID
 * @return array 商品情報
 */
function getItemById($itemId) {
    try {
        $pdo = db_connect();
        
        $sql = "SELECT * FROM item WHERE item_id = :item_id";
        
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(':item_id', $itemId, PDO::PARAM_INT);
        $stmh->execute();
        
        $result = $stmh->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($result)) {
            error_log("Item not found with ID: " . $itemId);
            return null;
        }
        
        return $result[0]; // 最初の結果を返す
        
    } catch (PDOException $e) {
        error_log("Get Item Error: " . $e->getMessage());
        return null;
    }
}

/**
 * 販売状態で商品を取得
 */
function selectItemByStatus($stop_flg) {
    global $dbh;
    
    try {
        $sql = "SELECT * FROM item WHERE stop_flg = :stop_flg ORDER BY item_id DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':stop_flg', $stop_flg, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleError($e);
        exit;
    }
}

/**
 * カテゴリIDで商品を取得
 */
function selectItemByCategory($category_id) {
    global $dbh;
    
    try {
        $sql = "SELECT * FROM item WHERE category_id = :category_id ORDER BY item_id DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleError($e);
        exit;
    }
}

/**
 * 全カテゴリを取得
 */
function selectAllCategories() {
    global $dbh;
    
    try {
        $sql = "SELECT * FROM category ORDER BY category_id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("カテゴリ取得エラー: " . $e->getMessage());
        error_log("SQL: " . $sql);
        handleError($e);
        exit;
    }
}

// カテゴリの定義（定数配列）
function getCategoryList() {
    return [
        0 => 'カレー',
        1 => 'ナン',
        2 => 'サイドメニュー',
        3 => 'ドリンク'
    ];
}

/**
 * 管理者IDから管理者情報を取得
 */
function selectAdminById($admin_id) {
    global $dbh;
    
    try {
        $sql = "SELECT * FROM admin_user WHERE admin_id = :admin_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleError($e);
        exit;
    }
}

function searchMember($conditions) {
    global $dbh;
    
    $sql = "SELECT * FROM user WHERE 1=1";
    $params = [];
    
    if (!empty($conditions['member_name'])) {
        // 姓名どちらにもあいまい検索を適用
        $sql .= " AND (
            last_name LIKE ? OR 
            first_name LIKE ? OR
            CONCAT(last_name, first_name) LIKE ? OR
            CONCAT(first_name, last_name) LIKE ?
        )";
        $searchName = '%' . $conditions['member_name'] . '%';
        $params[] = $searchName;  // 姓であいまい検索
        $params[] = $searchName;  // 名であいまい検索
        $params[] = $searchName;  // フルネームであいまい検索（姓名）
        $params[] = $searchName;  // フルネームであいまい検索（名姓）
    }
    if (!empty($conditions['email'])) {
        $sql .= " AND email LIKE ?";
        $params[] = '%' . $conditions['email'] . '%';
    }
    if (!empty($conditions['address'])) {
        $sql .= " AND address LIKE ?";
        $params[] = '%' . $conditions['address'] . '%';
    }
    
    try {
        error_log('実行SQL: ' . $sql);  // デバッグ用
        error_log('検索条件: ' . print_r($params, true));  // デバッグ用
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log('検索結果件数: ' . count($results));  // デバッグ用
        return $results;
    } catch (PDOException $e) {
        error_log('メンバー検索エラー: ' . $e->getMessage());
        return [];
    }
}

?>
