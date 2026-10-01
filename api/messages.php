<?php
require_once __DIR__ . '/common.php';
$sessionUser = api_require_user(['GET', 'POST']);
$userId = (string) $sessionUser['id'];
$dataFile = __DIR__ . '/../data/messages.json';
$messages = api_read_json_file($dataFile);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mine = array_values(array_filter($messages, static fn ($message) => ($message['userId'] ?? '') === $userId));
    api_respond(['success' => true, 'messages' => $mine]);
}

$input = api_read_input();
$text = is_string($input['message'] ?? null) ? trim($input['message']) : '';
if ($text === '' || strlen($text) > 2000) {
    api_respond(['success' => false, 'message' => 'Write a message of up to 2,000 characters.'], 400);
}
$message = [
    'id' => bin2hex(random_bytes(12)),
    'userId' => $userId,
    'sender' => 'adopter',
    'text' => $text,
    'sentAt' => date(DATE_ATOM),
];
$messages[] = $message;
if (!api_write_json_file($dataFile, $messages)) {
    api_respond(['success' => false, 'message' => 'Message could not be saved. Check data folder write permissions.'], 500);
}
api_respond(['success' => true, 'message' => $message]);
