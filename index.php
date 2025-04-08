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
    // ホスト名からAZを推測
    $hostname = @exec('hostname -f');
    if (preg_match('/\.([a-z0-9\-]+)\./', $hostname, $matches)) {
        $availability_zone = $matches[1];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>部品管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>部品管理システム</h1>
        
        <div class="form-container">
            <h2>新しい部品を登録</h2>
            <form action="add_part.php" method="post">
                <div class="form-group">
                    <label for="part_number">在庫番号:</label>
                    <input type="text" id="part_number" name="part_number" required>
                </div>
                <div class="form-group">
                    <label for="part_name">部品名:</label>
                    <input type="text" id="part_name" name="part_name" required>
                </div>
                <div class="form-group">
                    <label for="quantity">数量:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="date">日程:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="person_in_charge">担当者:</label>
                    <input type="text" id="person_in_charge" name="person_in_charge" required>
                </div>
                <div class="form-group">
                    <label for="notes">備考:</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <button type="submit">登録</button>
            </form>
        </div>
        
        <div class="parts-list">
            <h2>部品一覧</h2>
            <?php include 'list_parts.php'; ?>
        </div>
    </div>
    
    <div class="server-info">
        <strong>サーバー情報:</strong><br>
        インスタンスID: <?php echo $instance_id ?: 'Unknown'; ?><br>
        AZ: <?php echo $availability_zone ?: 'Unknown'; ?>
    </div>
</body>
</html>