<?php

header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
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

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required.'
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }

    // Find user
    $stmt = $pdo->prepare(
        "SELECT id, username, email, password_hash
         FROM users
         WHERE email = :email
         LIMIT 1"
    );

    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch();

    // Verify password
    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password.'
        ]);
        exit;
    }

    // Connect to Redis
    $redis = new Client([
        'scheme' => 'tcp',
        'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
        'port'   => $_ENV['REDIS_PORT'] ?? 6379
    ]);

    // Generate secure session token
    $sessionToken = bin2hex(random_bytes(32));

    // Store session in Redis for 1 hour
    $redis->setex(
        'session:' . $sessionToken,
        3600,
        json_encode([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ])
    );

    setcookie(
    'session_token',
    $sessionToken,
    [
        'expires' => time() + 3600,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]
);

echo json_encode([
    'success' => true,
    'message' => 'Login successful!'
]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong. Please try again.'
    ]);
}