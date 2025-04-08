<?php
require_once 'config.php';

// POSTメソッドで受け取る
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// IDが有効かチェック
if ($id <= 0) {
    echo "<html><head><title>エラー</title><link rel='stylesheet' href='style.css'></head><body>";
    echo "<div class='container'><h1>エラー</h1>";
    echo "<p>無効なIDです。</p>";
    echo "<p><a href='index.php' class='btn'>戻る</a></p></div></body></html>";
    exit;
}

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    echo "<html><head><title>エラー</title><link rel='stylesheet' href='style.css'></head><body>";
    echo "<div class='container'><h1>エラー</h1>";
    echo "<p>接続失敗: " . $conn->connect_error . "</p>";
    echo "<p><a href='index.php' class='btn'>戻る</a></p></div></body></html>";
    exit;
}

// SQLインジェクション対策
$stmt = $conn->prepare("DELETE FROM parts WHERE id = ?");
$stmt->bind_param("i", $id);

// 実行
if ($stmt->execute()) {
    // 削除成功
    header("Location: index.php");
    exit;
} else {
    echo "<html><head><title>エラー</title><link rel='stylesheet' href='style.css'></head><body>";
    echo "<div class='container'><h1>エラー</h1>";
    echo "<p>エラー: " . $stmt->error . "</p>";
    echo "<p><a href='index.php' class='btn'>戻る</a></p></div></body></html>";
}

$stmt->close();
$conn->close();
?>