<?php
declare(strict_types=1);

// PHP built-in server is started from the project root, so the app lives at "/".
define('BASE_URL', '');

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $client = new MongoDB\Client('mongodb://127.0.0.1:27017');
    $db = $client->selectDatabase('expense_tracker');
    $collection = $db->selectCollection('transactions');
} catch (Throwable $e) {
    http_response_code(500);
    die('Could not connect to MongoDB. Make sure the MongoDB server is running. '
        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
