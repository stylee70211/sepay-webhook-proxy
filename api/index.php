<?php
header("Content-Type: application/json; charset=utf-8");

// 1. Điền mã API Token lấy trong mục API Access của SePay
$api_token = "ALSWE6QIKHU175PF0QAZSV8WJVU61ERMFQNFDLJGZOSTS3CKV9BK9PHT2NYJJ4HN"; 
$account_number = "7359889572631";

// 2. Điền chính xác link file nhận đơn trên host InfinityFree của bạn
$target_url = "https://conghtanhtoan.rf.gd/cron_sepay.php"; 

// --- KHÔNG CẦN SỬA ĐOẠN CODE PHÍA DƯỚI NÀY ---
$url = "https://sepay.vn" . $account_number . "&limit=5";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_token,
    "Content-Type: application/json"
]);
$response = curl_exec($ch);
curl_close($ch);
$result = json_decode($response, true);

if (isset($result['transactions']) && !empty($result['transactions'])) {
    foreach ($result['transactions'] as $tx) {
        $post_data = json_encode([
            "secret_key" => "CONG_THANH_TOAN_BAO_MAT",
            "id" => $tx['id'],
            "amount" => $tx['amount'],
            "content" => $tx['code']
        ]);
        $ch_target = curl_init($target_url);
        curl_setopt($ch_target, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_target, CURLOPT_POST, true);
        curl_setopt($ch_target, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch_target, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);
        curl_exec($ch_target);
        curl_close($ch_target);
    }
    echo json_encode(["success" => true, "message" => "Da dong bo giao dich"]);
} else {
    echo json_encode(["success" => false, "message" => "Khong co giao dich moi"]);
}

