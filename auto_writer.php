<!DOCTYPE html>
<html>
<head>
    <title>自動データベース書き込みツール</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .control-panel { 
            padding: 15px; 
            background-color: #f0f0f0; 
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .status-panel {
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            height: 300px;
            overflow-y: auto;
        }
        .success { color: green; }
        .error { color: red; }
        button { padding: 8px 15px; margin-right: 10px; cursor: pointer; }
        .start { background-color: #4CAF50; color: white; border: none; }
        .stop { background-color: #f44336; color: white; border: none; }
        .settings { margin-top: 15px; }
        label { display: inline-block; width: 150px; }
    </style>
</head>
<body>
    <h1>自動データベース書き込みツール</h1>
    
    <div class="control-panel">
        <h2>制御パネル</h2>
        <button id="startBtn" class="start">開始</button>
        <button id="stopBtn" class="stop" disabled>停止</button>
        
        <div class="settings">
            <div>
                <label for="interval">書き込み間隔 (秒):</label>
                <input type="number" id="interval" min="1" max="60" value="5">
            </div>
            <div>
                <label for="prefix">メッセージプレフィックス:</label>
                <input type="text" id="prefix" value="自動書き込み">
            </div>
        </div>
    </div>
    
    <h2>ステータス</h2>
    <div class="status-panel" id="statusPanel">
        <p>自動書き込みを開始するには「開始」ボタンをクリックしてください。</p>
    </div>
    
    <script>
        let isRunning = false;
        let intervalId = null;
        let operationCount = 0;
        let successCount = 0;
        let failureCount = 0;
        
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const statusPanel = document.getElementById('statusPanel');
        
        function addStatusMessage(message, isError = false) {
            const timestamp = new Date().toLocaleTimeString();
            const msgElement = document.createElement('div');
            msgElement.innerHTML = `<span style="color:#666;">${timestamp}</span> - ${message}`;
            if (isError) {
                msgElement.classList.add('error');
            }
            statusPanel.appendChild(msgElement);
            statusPanel.scrollTop = statusPanel.scrollHeight;
        }
        
        function performWrite() {
            const prefix = document.getElementById('prefix').value;
            const message = `${prefix} #${operationCount+1} - ${new Date().toLocaleTimeString()}`;
            
            addStatusMessage(`書き込み実行中: "${message}"...`);
            operationCount++;
            
            fetch('write_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'name=自動テスト&message=' + encodeURIComponent(message)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    successCount++;
                    addStatusMessage(`✅ 書き込み成功 (ID: ${data.id}, 実行時間: ${data.execution_time}ms)`);
                } else {
                    failureCount++;
                    addStatusMessage(`❌ 書き込み失敗: ${data.message}`, true);
                }
                updateTitle();
            })
            .catch(error => {
                failureCount++;
                addStatusMessage(`❌ エラー: ${error.message}`, true);
                updateTitle();
            });
        }
        
        function updateTitle() {
            document.title = `書き込みツール (${successCount}成功/${failureCount}失敗)`;
        }
        
        startBtn.addEventListener('click', function() {
            const interval = parseInt(document.getElementById('interval').value, 10) * 1000;
            
            isRunning = true;
            startBtn.disabled = true;
            stopBtn.disabled = false;
            document.getElementById('interval').disabled = true;
            document.getElementById('prefix').disabled = true;
            
            addStatusMessage(`自動書き込みを開始しました (間隔: ${interval/1000}秒)`);
            
            // 即時実行
            performWrite();
            
            // 定期実行
            intervalId = setInterval(performWrite, interval);
        });
        
        stopBtn.addEventListener('click', function() {
            isRunning = false;
            startBtn.disabled = false;
            stopBtn.disabled = true;
            document.getElementById('interval').disabled = false;
            document.getElementById('prefix').disabled = false;
            
            if (intervalId !== null) {
                clearInterval(intervalId);
                intervalId = null;
            }
            
            addStatusMessage(`自動書き込みを停止しました (${operationCount}回実行, ${successCount}成功, ${failureCount}失敗)`);
        });
    </script>
</body>
</html>