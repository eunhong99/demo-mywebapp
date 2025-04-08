<?php
require_once 'config.php';

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// メッセージの取得
$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // 結果を出力
    while($row = $result->fetch_assoc()) {
        echo '<div class="message">';
        echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
        echo '<p>' . htmlspecialchars($row["message"]) . '</p>';
        echo '<p class="date">' . $row["created_at"] . '</p>';
        echo '</div>';
    }
} else {
    echo "メッセージはまだありません。";
}

$conn->close();
?>