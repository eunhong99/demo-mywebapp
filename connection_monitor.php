<?php
// エラー表示を有効化
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

header("Refresh: 2"); // 2秒ごとに自動更新

echo "<h1>RDSデータベース接続モニター</h1>";
echo "<p>最終更新時間: " . date('Y-m-d H:i:s') . "</p>";

try {
    // 接続タイムアウトを短く設定
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    
    // 接続確認
    if ($conn->connect_error) {
        echo "<div style='background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000;'>";
        echo "<h2>⚠️ データベース接続失敗</h2>";
        echo "<p>エラー: " . $conn->connect_error . "</p>";
        echo "<p>接続先: " . DB_SERVER . "</p>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #ccffcc; padding: 10px; border: 1px solid #00cc00;'>";
        echo "<h2>✅ データベース接続成功</h2>";
        echo "<p>接続先: " . DB_SERVER . "</p>";
        echo "<p>MySQLバージョン: " . $conn->server_info . "</p>";
        
        // データベース選択
        if ($conn->select_db(DB_DATABASE)) {
            echo "<p>データベース '" . DB_DATABASE . "' に接続中</p>";
            
            // 接続統計情報
            $result = $conn->query("SHOW STATUS LIKE 'Uptime'");
            if ($row = $result->fetch_assoc()) {
                echo "<p>データベースアップタイム: " . $row['Value'] . " 秒</p>";
            }
            
            // レプリケーション情報（可能であれば）
            $result = $conn->query("SHOW SLAVE STATUS");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<p>レプリケーションステータス: " . $row['Slave_IO_Running'] . "/" . $row['Slave_SQL_Running'] . "</p>";
            }
            
            // 最新のメッセージを表示
            $result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
            if ($result->num_rows > 0) {
                echo "<h3>最新のメッセージ:</h3>";
                echo "<table border='1' style='width: 100%;'>";
                echo "<tr><th>ID</th><th>名前</th><th>メッセージ</th><th>日時</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["message"]) . "</td>";
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p>データベース '" . DB_DATABASE . "' の選択に失敗: " . $conn->error . "</p>";
        }
        
        echo "</div>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<div style='background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000;'>";
    echo "<h2>⚠️ エラーが発生しました</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
<p><small>このページは2秒ごとに自動更新されます</small></p>