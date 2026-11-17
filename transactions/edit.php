<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Edit Transaction';
$id = $_GET['id'] ?? '';
try {
    $objectId = new MongoDB\BSON\ObjectId($id);
    $txn = $collection->findOne(['_id' => $objectId]);
} catch (Throwable $e) {
    $txn = null;
}
if (!$txn) {
    flash('error', 'Transaction not found.');
    redirect('transactions/list.php');
}

$errors = [];
$form = [
    'title' => (string) $txn['title'],
    'amount' => (string) $txn['amount'],
    'type' => (string) $txn['type'],
    'category' => (string) $txn['category'],
    'date' => (string) $txn['date'],
    'description' => (string) ($txn['description'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['title'] = trim($_POST['title'] ?? '');
    $form['amount'] = trim($_POST['amount'] ?? '');
    $form['type'] = $_POST['type'] ?? '';
    $form['category'] = $_POST['category'] ?? '';
    $form['date'] = $_POST['date'] ?? '';
    $form['description'] = trim($_POST['description'] ?? '');

    if (!verify_csrf()) $errors[] = 'Invalid form token. Refresh the page and try again.';
    if ($form['title'] === '') $errors[] = 'Title is required.';
    if (!is_numeric($form['amount']) || (float) $form['amount'] <= 0) $errors[] = 'Amount must be greater than 0.';
    if (!in_array($form['type'], ['income', 'expense'], true)) $errors[] = 'Choose a valid type.';
    if (!in_array($form['category'], CATEGORIES, true)) $errors[] = 'Choose a valid category.';
    $dateObject = DateTime::createFromFormat('!Y-m-d', $form['date']);
    if (!$dateObject || $dateObject->format('Y-m-d') !== $form['date']) $errors[] = 'Enter a valid date.';

    if (!$errors) {
        $collection->updateOne(['_id' => $objectId], ['$set' => [
            'title' => $form['title'],
            'amount' => round((float) $form['amount'], 2),
            'type' => $form['type'],
            'category' => $form['category'],
            'date' => $dateObject->format('Y-m-d'),
            'description' => $form['description'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]]);
        flash('success', 'Transaction updated successfully.');
        redirect('transactions/list.php');
    }
}
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel form-panel">
    <h2 class="panel-title">Edit Transaction</h2>
    <p class="panel-subtitle">Update the details and save your changes.</p>
    <?php if ($errors): ?><div class="alert alert-error"><ul class="error-list"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <div class="form-group"><label for="title">Transaction Title <span class="req">*</span></label><input type="text" id="title" name="title" required maxlength="120" value="<?= e($form['title']) ?>"></div>
        <div class="form-group"><label for="amount">Amount (₹) <span class="req">*</span></label><input type="number" id="amount" name="amount" step="0.01" min="0.01" required value="<?= e($form['amount']) ?>"></div>
        <div class="form-group"><label for="type">Type <span class="req">*</span></label><select id="type" name="type" required><option value="expense" <?= $form['type'] === 'expense' ? 'selected' : '' ?>>Expense</option><option value="income" <?= $form['type'] === 'income' ? 'selected' : '' ?>>Income</option></select></div>
        <div class="form-group"><label for="category">Category <span class="req">*</span></label><select id="category" name="category" required><?php foreach (CATEGORIES as $cat): ?><option value="<?= e($cat) ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="date">Date <span class="req">*</span></label><input type="date" id="date" name="date" required value="<?= e($form['date']) ?>"></div>
        <div class="form-group form-group-full"><label for="description">Description</label><textarea id="description" name="description" rows="3" maxlength="1000"><?= e($form['description']) ?></textarea></div>
        <div class="form-actions"><button type="submit" class="btn btn-primary">💾 Update Transaction</button><a href="<?= BASE_URL ?>/transactions/list.php" class="btn btn-outline">Cancel</a></div>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
