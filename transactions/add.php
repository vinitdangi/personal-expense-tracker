<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Add Transaction';
$errors = [];
$old = ['title' => '', 'amount' => '', 'type' => 'expense', 'category' => 'Food', 'date' => date('Y-m-d'), 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['title'] = trim($_POST['title'] ?? '');
    $old['amount'] = trim($_POST['amount'] ?? '');
    $old['type'] = $_POST['type'] ?? 'expense';
    $old['category'] = $_POST['category'] ?? 'Food';
    $old['date'] = $_POST['date'] ?? '';
    $old['description'] = trim($_POST['description'] ?? '');

    if (!verify_csrf()) $errors[] = 'Invalid form token. Refresh the page and try again.';
    if ($old['title'] === '') $errors[] = 'Title is required.';
    if (!is_numeric($old['amount']) || (float) $old['amount'] <= 0) $errors[] = 'Amount must be greater than 0.';
    if (!in_array($old['type'], ['income', 'expense'], true)) $errors[] = 'Choose a valid type.';
    if (!in_array($old['category'], CATEGORIES, true)) $errors[] = 'Choose a valid category.';
    $dateObject = DateTime::createFromFormat('!Y-m-d', $old['date']);
    if (!$dateObject || $dateObject->format('Y-m-d') !== $old['date']) $errors[] = 'Enter a valid date.';

    if (!$errors) {
        $collection->insertOne([
            'title' => $old['title'],
            'amount' => round((float) $old['amount'], 2),
            'type' => $old['type'],
            'category' => $old['category'],
            'date' => $dateObject->format('Y-m-d'),
            'description' => $old['description'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Transaction added successfully.');
        redirect('transactions/list.php');
    }
}
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel form-panel">
    <h2 class="panel-title">New Transaction</h2>
    <p class="panel-subtitle">Record an income or expense entry.</p>
    <?php if ($errors): ?><div class="alert alert-error"><ul class="error-list"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <div class="form-group"><label for="title">Transaction Title <span class="req">*</span></label><input type="text" id="title" name="title" required maxlength="120" placeholder="e.g. Lunch at cafeteria" value="<?= e($old['title']) ?>"></div>
        <div class="form-group"><label for="amount">Amount (₹) <span class="req">*</span></label><input type="number" id="amount" name="amount" step="0.01" min="0.01" required placeholder="e.g. 250.00" value="<?= e($old['amount']) ?>"></div>
        <div class="form-group"><label for="type">Type <span class="req">*</span></label><select id="type" name="type" required><option value="expense" <?= $old['type'] === 'expense' ? 'selected' : '' ?>>Expense</option><option value="income" <?= $old['type'] === 'income' ? 'selected' : '' ?>>Income</option></select></div>
        <div class="form-group"><label for="category">Category <span class="req">*</span></label><select id="category" name="category" required><?php foreach (CATEGORIES as $cat): ?><option value="<?= e($cat) ?>" <?= $old['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="date">Date <span class="req">*</span></label><input type="date" id="date" name="date" required value="<?= e($old['date']) ?>"></div>
        <div class="form-group form-group-full"><label for="description">Description</label><textarea id="description" name="description" rows="3" maxlength="1000" placeholder="Optional notes about this transaction"><?= e($old['description']) ?></textarea></div>
        <div class="form-actions"><button type="submit" class="btn btn-primary">💾 Save Transaction</button><a href="<?= BASE_URL ?>/transactions/list.php" class="btn btn-outline">Cancel</a></div>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
