<?php
// サーバー情報を取得して表示
$instance_id = file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
$availability_zone = file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone');
$private_ip = file_get_contents('http://169.254.169.254/latest/meta-data/local-ipv4');

header('Content-Type: application/json');
echo json_encode([
    'instance_id' => $instance_id,
    'availability_zone' => $availability_zone,
    'private_ip' => $private_ip,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>