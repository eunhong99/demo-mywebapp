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
</body>
</html>