
<?php
$current = basename($_SERVER['SCRIPT_NAME']);

function nav_active(string $page, string $current): string
{
    return $page === $current ? ' class="active"' : '';
}
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">💰</span>
        <span class="brand-text">ExpenseTracker</span>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section">Menu</span>

        <a href="<?= BASE_URL ?>/dashboard.php"<?= nav_active('dashboard.php', $current) ?>>
            <span>📊</span> Dashboard
        </a>

        <a href="<?= BASE_URL ?>/transactions/add.php"<?= nav_active('add.php', $current) ?>>
            <span>➕</span> Add Transaction
        </a>

        <a href="<?= BASE_URL ?>/transactions/list.php"<?= nav_active('list.php', $current) ?>>
            <span>🧾</span> All Transactions
        </a>

        <span class="nav-section">Categories</span>

        <div class="category-chips">
            <?php foreach (CATEGORIES as $cat): ?>
                <a href="<?= BASE_URL ?>/transactions/list.php?category=<?= urlencode($cat) ?>">
                    <?= e($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/transactions/add.php" class="btn btn-primary btn-block">
            ＋ New Transaction
        </a>
    </div>
</aside>

<div class="overlay" id="sidebar-overlay"></div>
