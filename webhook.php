<?php

// ==========================================
// 1. กำหนดข้อมูลสำหรับเชื่อมต่อ GitHub (ใส่ข้อมูลของคุณให้ครบถ้วนแล้ว)
// ==========================================
$username    = '17099-netizen'; 
$repo        = 'webhook';       
$workflow_id = 'relay.yml';     
$branch      = 'main';          

// ใส่ Token ที่คุณส่งมาให้ในระบบเรียบร้อยครับ
$token       = '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';   

// ==========================================
// 2. ดึงข้อมูล Payload จาก Webhook ที่ยิงเข้ามา
// ==========================================
$payload = file_get_contents('php://input'); 

// ==========================================
// 3. เตรียมข้อมูลและจัดการส่งผ่าน cURL
// ==========================================
$url = "https://api.github.com/repos/{$username}/{$repo}/actions/workflows/{$workflow_id}/dispatches";

$data = [
    'ref' => $branch,
    'inputs' => [
        'payload' => $payload 
    ]
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// ตั้งค่า Headers ส่งไปหา GitHub API
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/vnd.github+json',
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'User-Agent: PHP-Webhook-Relay'
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// ตรวจสอบ Error ของ cURL
if (curl_errno($ch)) {
    http_response_code(500);
    echo 'cURL Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

// ==========================================
// 4. แสดงผลลัพธ์การทำงาน
// ==========================================
if ($http_code === 204) {
    http_response_code(200);
    echo 'OK - สั่งรัน Workflow สำเร็จแล้ว!';
} else {
    // หากขึ้น Error 401 แปลว่า Token ตัวนี้ใช้งานไม่ได้แล้ว (ต้องเจนใหม่มาเปลี่ยนในตัวแปร $token ครับ)
    http_response_code($http_code);
    echo "GitHub API Error (Status {$http_code}): " . $response;
}
