<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    http_response_code(405);
    flash('error', 'Invalid delete request.');
    redirect('transactions/list.php');
}

$id = $_POST['id'] ?? '';
try {
    $objectId = new MongoDB\BSON\ObjectId($id);
    $result = $collection->deleteOne(['_id' => $objectId]);
    flash($result->getDeletedCount() > 0 ? 'success' : 'error',
        $result->getDeletedCount() > 0 ? 'Transaction deleted successfully.' : 'Transaction not found.');
} catch (Throwable $e) {
    flash('error', 'Invalid transaction ID.');
}
redirect('transactions/list.php');
