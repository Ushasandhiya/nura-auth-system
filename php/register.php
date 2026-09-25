<?php

header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mongo.php';

try {

    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);
        exit;
    }

    // Get and clean input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if ($username === '' || $email === '' || $password === '') {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    if (strlen($username) < 3) {
        echo json_encode([
            'success' => false,
            'message' => 'Username must contain at least 3 characters.'
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

    if (strlen($password) < 8) {
        echo json_encode([
            'success' => false,
            'message' => 'Password must contain at least 8 characters.'
        ]);
        exit;
    }

    // Check whether username or email already exists
    $check = $pdo->prepare(
        "SELECT id FROM users WHERE username = :username OR email = :email"
    );

    $check->execute([
        ':username' => $username,
        ':email' => $email
    ]);

    if ($check->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Username or email already exists.'
        ]);
        exit;
    }

    // Hash password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, email, password_hash)
         VALUES (:username, :email, :password)"
    );

    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);

    $userId = $pdo->lastInsertId();

$profiles = $mongoDb->selectCollection('profiles');

$profiles->insertOne([
    'user_id' => (int) $userId,
    'full_name' => '',
    'phone' => '',
    'bio' => '',
    'location' => '',
    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully!'
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred.'
    ]);
}