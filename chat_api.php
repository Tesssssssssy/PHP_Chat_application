<?php
require __DIR__.'/vendor/autoload.php'; // Firebase PHP SDK 로드

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$serviceAccount = ServiceAccount::fromValue([
// accountKet.json
]);


$firebase = (new Factory)
    ->withServiceAccount($serviceAccount);

$database = $firebase->createDatabase();

// GET 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $chatsRef = $database->getReference('chats');
    $chats = $chatsRef->getValue();

    $messages = [];
    foreach ($chats as $chat) {
        $messages[] = $chat;
    }

    $count = count($messages);

    $response = [
        'messages' => $messages,
        'count' => $count
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender = $_POST['sender'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($sender !== '' && $message !== '') {
        $chatRef = $database->getReference('chats')->push();
        $newMessage = $chatRef->set([
            'sender' => $sender,
            'message' => $message,
            'timestamp' => time()
        ]);

        echo '메세지가 전송되었습니다.';
    } else {
        echo '송신자와 메세지가 필요합니다.';
    }
}

?>