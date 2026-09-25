<?php

header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mongo.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

try {

    // Get Authorization header
    $token = $_COOKIE['session_token'] ?? '';

if ($token === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required.'
    ]);
    exit;
}

    // Connect to Redis
    $redis = new Client([
        'scheme' => 'tcp',
        'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
        'port'   => $_ENV['REDIS_PORT'] ?? 6379
    ]);

    // Get session
    $session = $redis->get('session:' . $token);

    if (!$session) {
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Please login again.'
        ]);
        exit;
    }

    $sessionData = json_decode($session, true);

    if (!isset($sessionData['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid session.'
        ]);
        exit;
    }

    // Get latest user information from MySQL
    $stmt = $pdo->prepare(
        "SELECT id, username, email
         FROM users
         WHERE id = :id
         LIMIT 1"
    );

    $stmt->execute([
        ':id' => $sessionData['user_id']
    ]);

    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found.'
        ]);
        exit;
    }

    // Get additional profile information from MongoDB
$profiles = $mongoDb->selectCollection('profiles');

$profile = $profiles->findOne([
    'user_id' => (int) $user['id']
]);

$additionalProfile = [
    'full_name' => $profile['full_name'] ?? '',
    'phone' => $profile['phone'] ?? '',
    'bio' => $profile['bio'] ?? '',
    'location' => $profile['location'] ?? ''
];

   echo json_encode([
    'success' => true,
    'user' => $user,
    'profile' => $additionalProfile
]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong.'
    ]);
}