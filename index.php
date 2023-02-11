<?php

require_once 'static/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$data = $input['message'] ?? $input['callback_query'];

//
file_put_contents('input_logs.txt', print_r($data, 1) . PHP_EOL, FILE_APPEND);


$message = mb_strtolower($data['data'] ?? $data['text'], 'utf-8');
$chat_id = $data['from']['id'];


if (in_array_ex($message, ['узнать', 'id'])) {
    return tgApi_call('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'ID: ' . $chat_id,
    ]);
} else {
    return tgApi_call('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'Hello ' . $data['from']['first_name'] ?? '',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => 'Узнать свой ID', 'callback_data' => 'get_my_id'],
                ],
            ],
            'resize_keyboard' => true,
        ]),
    ]);
}

function tgApi_call($method, $params = array()) {
    $endpoint = 'https://api.telegram.org/bot' . API_TOKEN . '/' . $method;
    $res = file_get_contents($endpoint, false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => json_encode($params),
        ]
    ]));

    return json_decode($res, 1) ?? $res;
}

function in_array_ex($array, $words): int
{
    foreach ($words as $word) {
        if (strpos($array, $word) !== false) {
            return 1;
        }
    }
    return 0;
}