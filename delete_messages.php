<?php
require_once 'config.php';

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// IDが有効かチェック
if ($id <= 0) {
    echo "無効なIDです。<a href='index.php'>戻る</a>";
    exit;
}

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error . " <a href='index.php'>戻る</a>");
}

// SQLインジェクション対策
$stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
$stmt->bind_param("i", $id);

// 実行
if ($stmt->execute()) {
    // 削除成功
    header("Location: index.php");
    exit;
} else {
    echo "エラー: " . $stmt->error . " <a href='index.php'>戻る</a>";
}

$stmt->close();
$conn->close();
?>