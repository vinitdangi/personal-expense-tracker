<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Dashboard';

$income = 0.0;
$expense = 0.0;
foreach ($collection->aggregate([
    ['$group' => ['_id' => '$type', 'total' => ['$sum' => '$amount']]]
]) as $row) {
    if ($row->_id === 'income') {
        $income += (float) $row->total;
    } elseif ($row->_id === 'expense') {
        $expense += (float) $row->total;
    }
}
$balance = $income - $expense;

$recent = $collection->find([], [
    'sort' => ['date' => -1, 'created_at' => -1],
    'limit' => 5,
])->toArray();

$monthLabels = [];
$monthIncome = [];
$monthExpense = [];
for ($i = 5; $i >= 0; $i--) {
    $ym = date('Y-m', strtotime("-{$i} months"));
    $monthLabels[] = date('M Y', strtotime($ym . '-01'));
    $monthIncome[$ym] = 0.0;
    $monthExpense[$ym] = 0.0;
}
$startDate = date('Y-m-01', strtotime('-5 months'));
foreach ($collection->aggregate([
    ['$match' => ['date' => ['$gte' => $startDate]]],
    ['$group' => [
        '_id' => ['ym' => ['$substrBytes' => ['$date', 0, 7]], 'type' => '$type'],
        'total' => ['$sum' => '$amount'],
    ]],
]) as $row) {
    $ym = $row->_id->ym;
    if (!array_key_exists($ym, $monthIncome)) {
        continue;
    }
    if ($row->_id->type === 'income') {
        $monthIncome[$ym] = (float) $row->total;
    } elseif ($row->_id->type === 'expense') {
        $monthExpense[$ym] = (float) $row->total;
    }
}

$catLabels = [];
$catTotals = [];
foreach ($collection->aggregate([
    ['$match' => ['type' => 'expense']],
    ['$group' => ['_id' => '$category', 'total' => ['$sum' => '$amount']]],
    ['$sort' => ['total' => -1]],
]) as $row) {
    $catLabels[] = (string) $row->_id;
    $catTotals[] = round((float) $row->total, 2);
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="cards-grid">
    <div class="card card-total"><div class="card-icon">💼</div><div class="card-info"><span class="card-label">Total Balance</span><span class="card-value"><?= money($balance) ?></span></div></div>
    <div class="card card-income"><div class="card-icon">📈</div><div class="card-info"><span class="card-label">Total Income</span><span class="card-value"><?= money($income) ?></span></div></div>
    <div class="card card-expense"><div class="card-icon">📉</div><div class="card-info"><span class="card-label">Total Expenses</span><span class="card-value"><?= money($expense) ?></span></div></div>
</section>

<section class="charts-grid">
    <div class="panel">
        <h2 class="panel-title">Monthly Expense Summary</h2>
        <p class="panel-subtitle">Income vs Expenses · last 6 months</p>
        <div class="chart-box"><canvas id="monthlyChart"></canvas></div>
    </div>
    <div class="panel">
        <h2 class="panel-title">Expenses by Category</h2>
        <p class="panel-subtitle">All-time category-wise spending</p>
        <div class="chart-box"><canvas id="categoryChart"></canvas></div>
    </div>
</section>

<section class="panel">
    <div class="panel-head">
        <h2 class="panel-title">Recent Transactions</h2>
        <a href="<?= BASE_URL ?>/transactions/list.php" class="btn btn-outline btn-sm">View All →</a>
    </div>
    <?php if (count($recent) === 0): ?>
        <div class="empty-state"><p>No transactions yet.</p><a href="<?= BASE_URL ?>/transactions/add.php" class="btn btn-primary">Add your first transaction</a></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Date</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $t): ?>
                    <tr>
                        <td><strong><?= e($t['title']) ?></strong><?php if (!empty($t['description'])): ?><div class="text-muted text-small"><?= e($t['description']) ?></div><?php endif; ?></td>
                        <td><span class="cat-pill"><?= e($t['category']) ?></span></td>
                        <td><?= type_badge($t['type']) ?></td>
                        <td><?= e(date('d M Y', strtotime((string) $t['date']))) ?></td>
                        <td class="text-right <?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>"><?= $t['type'] === 'income' ? '+' : '−' ?><?= money($t['amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const MONTH_LABELS = <?= json_encode($monthLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const MONTH_INCOME = <?= json_encode(array_values($monthIncome)) ?>;
const MONTH_EXPENSE = <?= json_encode(array_values($monthExpense)) ?>;
const CAT_LABELS = <?= json_encode($catLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const CAT_TOTALS = <?= json_encode($catTotals) ?>;
if (typeof Chart !== 'undefined') {
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: { labels: MONTH_LABELS, datasets: [
            { label: 'Income', data: MONTH_INCOME, backgroundColor: '#16a34a', borderRadius: 6 },
            { label: 'Expense', data: MONTH_EXPENSE, backgroundColor: '#ef4444', borderRadius: 6 }
        ]},
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
    });
    if (CAT_LABELS.length > 0) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: { labels: CAT_LABELS, datasets: [{ data: CAT_TOTALS, backgroundColor: ['#4f46e5','#f97316','#0ea5e9','#16a34a','#eab308','#ec4899','#8b5cf6','#64748b'], borderWidth: 2, borderColor: '#ffffff' }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '58%', plugins: { legend: { position: 'bottom' } } }
        });
    } else {
        document.getElementById('categoryChart').parentElement.innerHTML = '<p class="empty-state">No expense data yet.</p>';
    }
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
