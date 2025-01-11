<?php
/**
 * データベースに接続する関数
 * 
 * @return PDO データベース接続オブジェクト
 */
function db_connect() {
    try {
        $dsn = 'mysql:host=localhost;dbname=db1223839;charset=utf8mb4';
        $user = 'root';
        $password = '';
        
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 文字エンコーディングを設定
        $dbh->exec("SET NAMES utf8mb4");
        $dbh->exec("SET CHARACTER SET utf8mb4");
        $dbh->exec("SET COLLATION_CONNECTION = utf8mb4_unicode_ci");
        
        return $dbh;
        
    } catch (PDOException $e) {
        handleError($e);
        exit;
    }
}

/**
 * エラーを処理する関数
 * 
 * @param PDOException $e PDOException オブジェクト
 */
function handleError($e) {
    error_log("Database Error: " . $e->getMessage());
    die("システムエラーが発生しました。管理者にお問い合わせください。");
}
?>