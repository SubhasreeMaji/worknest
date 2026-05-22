<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

require_login();

$userId = current_user_id();
$priorityFilter = $_GET['priority'] ?? '';

if ($priorityFilter !== '' && !validate_priority($priorityFilter)) {
    $priorityFilter = '';
}

$statement = $pdo->prepare(
    "SELECT
        COUNT(*) AS total_tasks,
        SUM(status = 'Completed') AS completed_tasks,
        SUM(status = 'Pending') AS pending_tasks
     FROM tasks
     WHERE user_id = ?"
);
$statement->execute([$userId]);
$stats = $statement->fetch();

$taskQuery = 'SELECT * FROM tasks WHERE user_id = ?';
$taskParams = [$userId];

if ($priorityFilter !== '') {
    $taskQuery .= ' AND priority = ?';
    $taskParams[] = $priorityFilter;
}

$taskQuery .= ' ORDER BY created_date DESC, id DESC';

$statement = $pdo->prepare($taskQuery);
$statement->execute($taskParams);
$tasks = $statement->fetchAll();

$pageTitle = 'Dashboard - TaskTracker';
require_once __DIR__ . '/includes/header.php';
?>

<section class="dashboard-heading">
    <div>
        <p class="eyebrow"><?= e($_SESSION['user_role'] ?? 'user') ?> account</p>
        <h1>Welcome, <?= e($_SESSION['user_name'] ?? 'User') ?></h1>
    </div>
    <a class="button primary" href="<?= e(url('/tasks/create.php')) ?>">Add Task</a>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <span>Total Tasks</span>
        <strong><?= (int) ($stats['total_tasks'] ?? 0) ?></strong>
    </article>
    <article class="stat-card">
        <span>Completed</span>
        <strong><?= (int) ($stats['completed_tasks'] ?? 0) ?></strong>
    </article>
    <article class="stat-card">
        <span>Pending</span>
        <strong><?= (int) ($stats['pending_tasks'] ?? 0) ?></strong>
    </article>
</section>

<section class="task-panel">
    <div class="section-heading">
        <h2>Your Tasks</h2>
        <span>
            <?= count($tasks) ?> item<?= count($tasks) === 1 ? '' : 's' ?>
            <?= $priorityFilter !== '' ? ' with ' . e($priorityFilter) . ' priority' : '' ?>
        </span>
    </div>

    <form class="filter-bar" method="get" action="<?= e(url('/dashboard.php')) ?>">
        <label for="priority">Filter by priority</label>
        <select id="priority" name="priority">
            <option value="">All priorities</option>
            <?php foreach (['High', 'Medium', 'Low'] as $priority): ?>
                <option value="<?= e($priority) ?>" <?= $priorityFilter === $priority ? 'selected' : '' ?>>
                    <?= e($priority) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="button primary" type="submit">Apply</button>
        <?php if ($priorityFilter !== ''): ?>
            <a class="button secondary" href="<?= e(url('/dashboard.php')) ?>">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (!$tasks): ?>
        <div class="empty-state">
            <?php if ($priorityFilter !== ''): ?>
                <h3>No <?= e(strtolower($priorityFilter)) ?> priority tasks</h3>
                <p>Try another priority or clear the filter.</p>
                <a class="button secondary" href="<?= e(url('/dashboard.php')) ?>">Clear Filter</a>
            <?php else: ?>
                <h3>No tasks yet</h3>
                <p>Add your first task and it will appear here.</p>
                <a class="button primary" href="<?= e(url('/tasks/create.php')) ?>">Add Task</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Estimate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td>
                                <strong><?= e($task['title']) ?></strong>
                                <?php if ($task['description']): ?>
                                    <small><?= e($task['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge priority-<?= strtolower(e($task['priority'])) ?>"><?= e($task['priority']) ?></span></td>
                            <td><span class="badge status-<?= strtolower(e($task['status'])) ?>"><?= e($task['status']) ?></span></td>
                            <td><?= e($task['created_date']) ?></td>
                            <td><?= e($task['estimated_date'] ?: 'Not set') ?></td>
                            <td class="actions">
                                <a class="button secondary small" href="<?= e(url('/tasks/edit.php')) ?>?id=<?= (int) $task['id'] ?>">Edit</a>
                                <form method="post" action="<?= e(url('/tasks/delete.php')) ?>" onsubmit="return confirm('Delete this task permanently?');">
                                    <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                    <button class="button danger small" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
