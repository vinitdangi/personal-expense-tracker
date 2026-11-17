<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> · ExpenseTracker</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/sidebar.php'; ?>
<div class="main">
    <header class="topbar">
        <button class="menu-btn" id="menu-btn" aria-label="Toggle menu">☰</button>
        <div class="topbar-title"><h1><?= e($pageTitle ?? 'Dashboard') ?></h1></div>
        <div class="topbar-actions">
            <span class="topbar-date"><?= date('D, d M Y') ?></span>
            <a href="<?= BASE_URL ?>/transactions/add.php" class="btn btn-primary btn-sm">＋ Add Transaction</a>
        </div>
    </header>
    <main class="content">
        <?php if ($flash = get_flash()): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
