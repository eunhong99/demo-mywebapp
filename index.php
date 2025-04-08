<?php
// EC2インスタンス情報を取得（タイムアウト設定を追加）
$context = stream_context_create(['http' => ['timeout' => 1]]);
$instance_id = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id', false, $context);
$availability_zone = @file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone', false, $context);

// 取得できない場合はシステム情報から取得を試みる
if (!$instance_id) {
    $instance_id = @exec('hostname');
}
if (!$availability_zone) {
    // ホスト名からAZを推測（例：ip-10-0-1-100.ap-northeast-1.compute.internal）
    $hostname = @exec('hostname -f');
    if (preg_match('/\.([a-z0-9\-]+)\./', $hostname, $matches)) {
        $availability_zone = $matches[1];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>デモWebアプリケーション</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>デモWebアプリケーション</h1>
        
        <div class="form-container">
            <h2>新しいメッセージを追加</h2>
            <form action="add_message.php" method="post">
                <div class="form-group">
                    <label for="name">お名前:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="message">メッセージ:</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <button type="submit">送信</button>
            </form>
        </div>
        
        <div class="messages">
            <h2>メッセージ一覧</h2>
            <?php include 'list_messages.php'; ?>
        </div>
    </div>
    
    <!-- サーバー情報を表示するためのコード -->
    <div class="server-info">
        <strong>サーバー情報:</strong><br>
        インスタンスID: <?php echo $instance_id ?: 'Unknown'; ?><br>
        AZ: <?php echo $availability_zone ?: 'Unknown'; ?>
    </div>
</body>
</html>