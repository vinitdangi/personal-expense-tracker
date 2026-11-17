<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'All Transactions';
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$type = trim($_GET['type'] ?? '');
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');

$filter = [];
if ($q !== '') $filter['title'] = ['$regex' => preg_quote($q, '/'), '$options' => 'i'];
if ($category !== '' && in_array($category, CATEGORIES, true)) $filter['category'] = $category;
if ($type !== '' && in_array($type, ['income', 'expense'], true)) $filter['type'] = $type;
if ($dateFrom !== '') $filter['date']['$gte'] = $dateFrom;
if ($dateTo !== '') $filter['date']['$lte'] = $dateTo;

$transactions = $collection->find($filter, ['sort' => ['date' => -1, 'created_at' => -1], 'limit' => 500])->toArray();
$shownIncome = 0.0;
$shownExpense = 0.0;
foreach ($transactions as $t) {
    if ($t['type'] === 'income') $shownIncome += (float) $t['amount'];
    else $shownExpense += (float) $t['amount'];
}
$hasFilters = ($q !== '' || $category !== '' || $type !== '' || $dateFrom !== '' || $dateTo !== '');

require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="panel-head"><h2 class="panel-title">Filter Transactions</h2><?php if ($hasFilters): ?><a href="<?= BASE_URL ?>/transactions/list.php" id="clear-filters" class="btn btn-outline btn-sm">✕ Clear Filters</a><?php endif; ?></div>
    <form method="get" class="filter-grid">
        <div class="form-group"><label for="f-q">Search by title</label><input type="text" id="f-q" name="q" placeholder="Search…" value="<?= e($q) ?>"></div>
        <div class="form-group"><label for="f-category">Category</label><select id="f-category" name="category"><option value="">All</option><?php foreach (CATEGORIES as $cat): ?><option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="f-type">Type</label><select id="f-type" name="type"><option value="">All</option><option value="income" <?= $type === 'income' ? 'selected' : '' ?>>Income</option><option value="expense" <?= $type === 'expense' ? 'selected' : '' ?>>Expense</option></select></div>
        <div class="form-group"><label for="f-from">From date</label><input type="date" id="f-from" name="date_from" value="<?= e($dateFrom) ?>"></div>
        <div class="form-group"><label for="f-to">To date</label><input type="date" id="f-to" name="date_to" value="<?= e($dateTo) ?>"></div>
        <div class="form-group filter-actions"><button type="submit" class="btn btn-primary">🔍 Apply</button></div>
    </form>
</section>
<section class="cards-grid cards-grid-compact">
    <div class="mini-stat"><span>Results</span><strong><?= count($transactions) ?></strong></div>
    <div class="mini-stat text-success"><span>Income</span><strong><?= money($shownIncome) ?></strong></div>
    <div class="mini-stat text-danger"><span>Expenses</span><strong><?= money($shownExpense) ?></strong></div>
</section>
<section class="panel">
    <div class="panel-head"><h2 class="panel-title">Transactions</h2><a href="<?= BASE_URL ?>/transactions/add.php" class="btn btn-primary btn-sm">＋ Add</a></div>
    <?php if (!$transactions): ?>
        <div class="empty-state"><p><?= $hasFilters ? 'No transactions match your filters.' : 'No transactions yet.' ?></p><a href="<?= BASE_URL ?>/transactions/add.php" class="btn btn-primary">Add a transaction</a></div>
    <?php else: ?>
        <div class="table-responsive"><table class="table">
            <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Date</th><th class="text-right">Amount</th><th class="text-center">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($transactions as $t): $id = (string) $t['_id']; ?>
                <tr>
                    <td><strong><?= e($t['title']) ?></strong><?php if (!empty($t['description'])): ?><div class="text-muted text-small"><?= e($t['description']) ?></div><?php endif; ?></td>
                    <td><span class="cat-pill"><?= e($t['category']) ?></span></td>
                    <td><?= type_badge($t['type']) ?></td>
                    <td><?= e(date('d M Y', strtotime((string) $t['date']))) ?></td>
                    <td class="text-right <?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>"><?= $t['type'] === 'income' ? '+' : '−' ?><?= money($t['amount']) ?></td>
                    <td class="text-center actions-cell">
                        <a href="<?= BASE_URL ?>/transactions/edit.php?id=<?= e($id) ?>" class="btn btn-outline btn-sm" title="Edit">✏️ Edit</a>
                        <form method="post" action="<?= BASE_URL ?>/transactions/delete.php" class="inline-form" onsubmit="return confirm('Delete this transaction? This cannot be undone.');" style="display:inline">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($id) ?>">
                            <button type="submit" class="btn btn-danger-outline btn-sm" title="Delete">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
