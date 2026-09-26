<?php

header('Content-Type: application/json');

require_once __DIR__ . '/mongo.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);
        exit;
    }

    // Get session token
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
    'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
    'port' => $_ENV['REDIS_PORT'] ?? 6379,
    'password' => $_ENV['REDIS_PASSWORD'] ?? null
]);
    $session = $redis->get('session:' . $token);

    if (!$session) {
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Please login again.'
        ]);
        exit;
    }

    $sessionData = json_decode($session, true);
    $userId = (int) ($sessionData['user_id'] ?? 0);

    if ($userId <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid session.'
        ]);
        exit;
    }

    // Get profile data
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');

    // Update MongoDB
    $profiles = $mongoDb->selectCollection('profiles');

    $profiles->updateOne(
        ['user_id' => $userId],
        [
            '$set' => [
                'full_name' => $fullName,
                'phone' => $phone,
                'bio' => $bio,
                'location' => $location,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]
        ]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!'
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Unable to update profile.'
    ]);
}