<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use MongoDB\Client;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// MongoDB connection
$mongoClient = new Client($_ENV['MONGO_URI']);

$mongoDb = $mongoClient->selectDatabase($_ENV['MONGO_DB']);