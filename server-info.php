<?php
// タイムアウト設定を追加
$context = stream_context_create(['http' => ['timeout' => 1]]);

// サーバー情報を取得して表示
$instance_id = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id', false, $context);
$availability_zone = @file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone', false, $context);
$private_ip = @file_get_contents('http://169.254.169.254/latest/meta-data/local-ipv4', false, $context);

// 取得できない場合はシステム情報から取得を試みる
if (!$instance_id) {
    $instance_id = @exec('hostname');
}
if (!$availability_zone) {
    $hostname = @exec('hostname -f');
    if (preg_match('/\.([a-z0-9\-]+)\./', $hostname, $matches)) {
        $availability_zone = $matches[1];
    }
}
if (!$private_ip) {
    $private_ip = @$_SERVER['SERVER_ADDR'];
}

header('Content-Type: application/json');
echo json_encode([
    'instance_id' => $instance_id ?: 'Unknown',
    'availability_zone' => $availability_zone ?: 'Unknown',
    'private_ip' => $private_ip ?: 'Unknown',
    'timestamp' => date('Y-m-d H:i:s'),
    'hostname' => @gethostname()
]);
?>