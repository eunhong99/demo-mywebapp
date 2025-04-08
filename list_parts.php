<?php
require_once 'config.php';

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 部品情報を取得
$sql = "SELECT id, part_number, part_name, quantity, date, person_in_charge, notes, created_at FROM parts ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // テーブルヘッダー
    echo '<table class="parts-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>在庫番号</th>';
    echo '<th>部品名</th>';
    echo '<th>数量</th>';
    echo '<th>日程</th>';
    echo '<th>担当者</th>';
    echo '<th>備考</th>';
    echo '<th>登録日時</th>';
    echo '<th>操作</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // 部品情報を表示
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row["part_number"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["part_name"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["quantity"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["date"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["person_in_charge"]) . '</td>';
        echo '<td>' . nl2br(htmlspecialchars($row["notes"])) . '</td>';
        
        // 日本時間に変換して表示
        $date = new DateTime($row["created_at"]);
        $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
        echo '<td>' . $date->format('Y-m-d H:i:s') . '</td>';
        
        // 削除ボタン
        echo '<td>';
        echo '<form action="delete_part.php" method="post" onsubmit="return confirm(\'この部品情報を削除してもよろしいですか？\');">';
        echo '<input type="hidden" name="id" value="' . $row["id"] . '">';
        echo '<button type="submit" class="delete-btn">削除</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
} else {
    echo "<p>部品情報はまだ登録されていません。</p>";
}

$conn->close();
?>