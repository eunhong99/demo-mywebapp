<?php
// EC2インスタンス情報を取得（ページの先頭に追加）
$instance_id = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
$availability_zone = @file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone');
?>
<!DOCTYPE html>
<html>
<head>
    <title>シンプルWebアプリケーション</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>シンプルWebアプリケーション</h1>
        
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
    
    <!-- サーバー情報を表示するためのコード（ページの下部に追加） -->
    <div style="position: fixed; bottom: 10px; right: 10px; background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <strong>サーバー情報:</strong><br>
        インスタンスID: <?php echo $instance_id ?: 'Unknown'; ?><br>
        AZ: <?php echo $availability_zone ?: 'Unknown'; ?>
    </div>
</body>
</html>