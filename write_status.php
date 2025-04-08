<?php
// エラー表示を有効化
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// ログファイルの設定
$log_file = 'db_operations.log';
$max_log_entries = 50;

// ログエントリの追加
function add_log_entry($status, $message) {
    global $log_file, $max_log_entries;
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "$timestamp | $status | $message\n";
    
    // ファイルが存在しない場合は作成
    if (!file_exists($log_file)) {
        file_put_contents($log_file, "");
        chmod($log_file, 0666);
    }
    
    // 現在のログを読み込む
    $current_log = file_get_contents($log_file);
    $log_lines = explode("\n", $current_log);
    
    // 最大エントリ数を超える場合は古いエントリを削除
    if (count($log_lines) > $max_log_entries) {
        $log_lines = array_slice($log_lines, -$max_log_entries);
    }
    
    // 新しいエントリを追加
    array_unshift($log_lines, $log_entry);
    file_put_contents($log_file, implode("\n", $log_lines));
}

// POSTリクエストの処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? 'テストユーザー';
    $message = $_POST['message'] ?? 'テストメッセージ - ' . date('H:i:s');
    
    try {
        // データベース接続
        $start_time = microtime(true);
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        
        // 接続確認
        if ($conn->connect_error) {
            add_log_entry("失敗", "DB接続エラー: " . $conn->connect_error);
            echo json_encode(['status' => 'error', 'message' => 'データベース接続エラー']);
            exit;
        }
        
        // データ挿入
        $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $message);
        
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            $execution_time = round((microtime(true) - $start_time) * 1000, 2);
            add_log_entry("成功", "ID: $insert_id | 名前: $name | メッセージ: $message | 実行時間: {$execution_time}ms");
            echo json_encode(['status' => 'success', 'id' => $insert_id, 'execution_time' => $execution_time]);
        } else {
            add_log_entry("失敗", "クエリエラー: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'クエリ実行エラー']);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        add_log_entry("失敗", "例外: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    exit;
}

// ログの表示
header("Refresh: 2"); // 2秒ごとに自動更新
?>
<!DOCTYPE html>
<html>
<head>
    <title>データベース書き込みステータスログ</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .log-container { 
            height: 500px; 
            overflow-y: auto; 
            border: 1px solid #ccc; 
            padding: 10px;
            background-color: #f9f9f9;
        }
        .success { color: green; }
        .error { color: red; }
        .log-entry { margin-bottom: 5px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .timestamp { color: #666; font-size: 0.9em; }
        .test-form { margin-top: 20px; padding: 15px; background-color: #f0f0f0; border: 1px solid #ddd; }
        button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>データベース書き込みステータスログ</h1>
    <p>最終更新時間: <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <div class="test-form">
        <h3>テスト書き込み</h3>
        <form id="testWriteForm">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="テストユーザー"><br><br>
            <label for="message">メッセージ:</label>
            <input type="text" id="message" name="message" value="テストメッセージ"><br><br>
            <button type="submit">書き込みテスト実行</button>
        </form>
        <div id="result" style="margin-top: 10px;"></div>
    </div>
    
    <h2>書き込みログ</h2>
    <div class="log-container">
        <?php
        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
            $log_lines = explode("\n", $log_content);
            
            foreach ($log_lines as $line) {
                if (empty(trim($line))) continue;
                
                $parts = explode(" | ", $line);
                if (count($parts) >= 3) {
                    $timestamp = $parts[0];
                    $status = $parts[1];
                    $message = $parts[2];
                    
                    $class = ($status == "成功") ? "success" : "error";
                    
                    echo "<div class='log-entry'>";
                    echo "<span class='timestamp'>$timestamp</span> - ";
                    echo "<span class='$class'>$status</span>: ";
                    echo "<span class='message'>$message</span>";
                    echo "</div>";
                }
            }
        } else {
            echo "<p>ログはまだありません</p>";
        }
        ?>
    </div>
    
    <p><small>このページは2秒ごとに自動更新されます</small></p>
    
    <script>
        document.getElementById('testWriteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var name = document.getElementById('name').value;
            var message = document.getElementById('message').value;
            
            // 現在時刻を追加
            if (message === 'テストメッセージ') {
                message += ' - ' + new Date().toLocaleTimeString();
            }
            
            var resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '処理中...';
            
            fetch('write_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'name=' + encodeURIComponent(name) + '&message=' + encodeURIComponent(message)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resultDiv.innerHTML = '<span style="color:green;">✅ 書き込み成功 (ID: ' + data.id + ', 実行時間: ' + data.execution_time + 'ms)</span>';
                } else {
                    resultDiv.innerHTML = '<span style="color:red;">❌ 書き込み失敗: ' + data.message + '</span>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<span style="color:red;">❌ エラー: ' + error.message + '</span>';
            });
        });
    </script>
</body>
</html>