<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Predis\Client;

// Load .env locally if it exists.
// Railway environment variables are used in production.
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

try {

    $token = $_COOKIE['session_token'] ?? '';

    if ($token !== '') {

        // Connect to Redis
        $redis = new Client([
            'scheme' => 'tcp',
            'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['REDIS_PORT'] ?? 6379,
            'password' => $_ENV['REDIS_PASSWORD'] ?? null
        ]);

        // Delete Redis session
        $redis->del(['session:' . $token]);

        // Remove browser cookie
        setcookie(
            'session_token',
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }

    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully.'
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Logout failed.'
    ]);
}