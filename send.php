<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
// ... باقي الكود بتاعك
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['message'])) {
        echo json_encode(['status' => 'error', 'message' => 'No message provided']);
        exit;
    }

    // تأكد إن التوكن ده هو الجديد اللي أخدته من BotFather
    $botToken = '9058820161:AAHtWLs4B80Mjs3IgVYRs1_emMAxUWiEwto'; 
    $chatId = '1216054281'; 

    $message = $data['message'];
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $post_fields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // لتجنب مشاكل شهادات الأمان في الاستضافة
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // لو السيرفر اتعلق، هيرجع بعد 10 ثواني
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        echo json_encode(['status' => 'error', 'step' => 'curl', 'message' => $curl_error]);
    } else if ($http_code != 200) {
        // لو تليجرام رفض الطلب، هنطبع سبب الرفض
        echo json_encode(['status' => 'error', 'step' => 'telegram', 'response' => json_decode($response)]);
    } else {
        echo json_encode(['status' => 'success']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
}
?>
