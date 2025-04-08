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
        
        // 日本時間に変換して表示
        $date = new DateTime($row["created_at"]);
        $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
        echo '<span class="date">' . $date->format('Y-m-d H:i:s') . ' (JST)</span>';
        
        echo '</div>';
        echo '<div class="message-body">' . nl2br(htmlspecialchars($row["message"])) . '</div>';
        echo '<div class="message-actions">';
        echo '<form action="delete_message.php" method="post" onsubmit="return confirm(\'このメッセージを削除してもよろしいですか？\');">';
        echo '<input type="hidden" name="id" value="' . $row["id"] . '">';
        echo '<button type="submit" class="delete-btn">削除</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>メッセージはまだありません。</p>";
}

$conn->close();
?>