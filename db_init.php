<?php
require_once 'config.php';

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// データベースが存在しない場合は作成
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_DATABASE;
if ($conn->query($sql) === TRUE) {
    echo "データベースが正常に作成されました。<br>";
} else {
    echo "データベース作成エラー: " . $conn->error . "<br>";
}

// データベースを選択
$conn->select_db(DB_DATABASE);

// テーブルの作成
$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "テーブルが正常に作成されました。";
} else {
    echo "テーブル作成エラー: " . $conn->error;
}

$conn->close();
?>