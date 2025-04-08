<?php
require_once 'config.php';

// POSTデータの取得
$part_number = $_POST['part_number'] ?? '';
$part_name = $_POST['part_name'] ?? '';
$quantity = intval($_POST['quantity'] ?? 0);
$date = $_POST['date'] ?? '';
$person_in_charge = $_POST['person_in_charge'] ?? '';
$notes = $_POST['notes'] ?? '';

// 入力チェック
if (empty($part_number) || empty($part_name) || $quantity <= 0 || empty($date) || empty($person_in_charge)) {
    echo "すべての必須項目を入力してください。";
    exit;
}

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// 接続確認
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// SQLインジェクション対策
$stmt = $conn->prepare("INSERT INTO parts (part_number, part_name, quantity, date, person_in_charge, notes) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisss", $part_number, $part_name, $quantity, $date, $person_in_charge, $notes);

// 実行
if ($stmt->execute()) {
    header("Location: index.php");
} else {
    echo "エラー: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>