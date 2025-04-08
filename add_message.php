<?php
require_once 'config.php';

// POSTデータの取得
$name = $_POST['name'] ?? '';
$message = $_POST['message'] ?? '';

// 入力チェック
if (empty($name) || empty($message)) {
    echo "名前とメッセージを入力してください。";
    exit;
}

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// SQLインジェクション対策
$stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $message);

// 実行
if ($stmt->execute()) {
    header("Location: index.php");
} else {
    echo "エラー: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>