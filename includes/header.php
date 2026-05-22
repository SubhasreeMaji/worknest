<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'To-Do List App';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/style.css')) ?>">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="<?= e(url('/dashboard.php')) ?>">WorkNest</a>
        <nav class="nav">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="<?= e(url('/dashboard.php')) ?>">Dashboard</a>
                <a href="<?= e(url('/tasks/create.php')) ?>">Add Task</a>
                <a href="<?= e(url('/logout.php')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= e(url('/index.php')) ?>">Login</a>
                <a href="<?= e(url('/register.php')) ?>">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?php if ($message = flash('success')): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if ($message = flash('error')): ?>
            <div class="alert error"><?= e($message) ?></div>
        <?php endif; ?>
