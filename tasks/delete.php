<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/dashboard.php');
}

$taskId = (int) ($_POST['id'] ?? 0);

$statement = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
$statement->execute([$taskId, current_user_id()]);

if ($statement->rowCount() > 0) {
    flash('success', 'Task deleted successfully.');
} else {
    flash('error', 'Task not found.');
}

redirect('/dashboard.php');

