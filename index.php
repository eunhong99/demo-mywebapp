<?php
// EC2インスタンス情報を取得（複数の方法を試す）
$instance_id = '';
$private_ip = '';
$az = 'Unknown';

// 方法1: メタデータサービスから取得（タイムアウト設定を追加）
$context = stream_context_create(['http' => ['timeout' => 1]]);
$instance_id = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id', false, $context);
$private_ip = @file_get_contents('http://169.254.169.254/latest/meta-data/local-ipv4', false, $context);
$availability_zone = @file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone', false, $context);

if ($availability_zone) {
    $az = $availability_zone;
}

// 方法2: システムコマンドから取得
if (!$instance_id) {
    $instance_id = @exec('hostname');
}

if (!$private_ip) {
    $private_ip = @exec("hostname -I | awk '{print $1}'");
}

// 方法3: サーバー変数から取得
if (!$private_ip) {
    $private_ip = @$_SERVER['SERVER_ADDR'];
}

// IPアドレスからAZを判断
if ($private_ip && $az == 'Unknown') {
    // VPC設計に基づいたマッピング
    if (preg_match('/^10\.0\.1\./', $private_ip)) {
        $az = 'ap-northeast-1a (Public)';
    } elseif (preg_match('/^10\.0\.2\./', $private_ip)) {
        $az = 'ap-northeast-1c (Public)';
    } elseif (preg_match('/^10\.0\.3\./', $private_ip)) {
        $az = 'ap-northeast-1a (Private)';
    } elseif (preg_match('/^10\.0\.4\./', $private_ip)) {
        $az = 'ap-northeast-1c (Private)';
    }
}

// ホスト名からAZを推測（最終手段）
if ($az == 'Unknown' && $instance_id) {
    if (strpos($instance_id, 'ap-northeast-1a') !== false || 
        strpos($instance_id, '1a') !== false) {
        $az = 'ap-northeast-1a';
    } elseif (strpos($instance_id, 'ap-northeast-1c') !== false || 
              strpos($instance_id, '1c') !== false) {
        $az = 'ap-northeast-1c';
    }
    
    // インスタンスIDの最後の文字でAZを推測
    if ($az == 'Unknown' && preg_match('/([a-z0-9])$/', $instance_id, $matches)) {
        $last_char = $matches[1];
        if (in_array($last_char, ['0', '2', '4', '6', '8', 'a', 'c', 'e'])) {
            $az = 'ap-northeast-1a (推測)';
        } else {
            $az = 'ap-northeast-1c (推測)';
        }
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
    
    <footer>
        <div class="footer-container">
            <strong>サーバー情報:</strong>
            インスタンスID: <?php echo $instance_id ?: 'Unknown'; ?> |
            プライベートIP: <?php echo $private_ip ?: 'Unknown'; ?> |
            アベイラビリティゾーン: <?php echo $az; ?>
        </div>
    </footer>
</body>
</html>