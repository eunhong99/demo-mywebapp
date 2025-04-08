<?php
require_once 'config.php';

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// メッセージを取得
$sql = "SELECT id, name, message, created_at FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // メッセージがある場合は表示
    while($row = $result->fetch_assoc()) {
        echo '<div class="message">';
        echo '<div class="message-header">';
        echo '<span class="name">' . htmlspecialchars($row["name"]) . '</span>';
        echo '<span class="date">' . htmlspecialchars($row["created_at"]) . '</span>';
        echo '</div>';
        echo '<div class="message-body">' . nl2br(htmlspecialchars($row["message"])) . '</div>';
        echo '<div class="message-footer">';
        echo '<a href="delete_message.php?id=' . $row["id"] . '" class="delete-btn" onclick="return confirm(\'このメッセージを削除してもよろしいですか？\');">削除</a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>メッセージはまだありません。</p>";
}

$conn->close();
?>