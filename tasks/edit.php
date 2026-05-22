<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

require_login();

$taskId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$statement = $pdo->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ? LIMIT 1');
$statement->execute([$taskId, current_user_id()]);
$task = $statement->fetch();

if (!$task) {
    flash('error', 'Task not found.');
    redirect('/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? '';
    $estimatedDate = $_POST['estimated_date'] ?: null;

    if ($title === '') {
        $errors[] = 'Task title is required.';
    }

    if (!validate_priority($priority)) {
        $errors[] = 'Choose a valid priority.';
    }

    if (!validate_status($status)) {
        $errors[] = 'Choose a valid status.';
    }

    if (!$errors) {
        $statement = $pdo->prepare(
            'UPDATE tasks
             SET title = ?, description = ?, priority = ?, status = ?, estimated_date = ?
             WHERE id = ? AND user_id = ?'
        );
        $statement->execute([$title, $description, $priority, $status, $estimatedDate, $taskId, current_user_id()]);

        flash('success', 'Task updated successfully.');
        redirect('/dashboard.php');
    }

    $task = array_merge($task, [
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'status' => $status,
        'estimated_date' => $estimatedDate,
    ]);
}

$pageTitle = 'Edit Task - TaskTracker';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="form-shell">
    <div class="section-heading">
        <h1>Edit Task</h1>
        <a class="button secondary" href="<?= e(url('/dashboard.php')) ?>">Back</a>
    </div>

    <?php foreach ($errors as $error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form class="form-card wide" method="post" action="<?= e(url('/tasks/edit.php')) ?>">
        <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">

        <label for="title">Task Title</label>
        <input id="title" name="title" type="text" value="<?= e($task['title']) ?>" required>

        <label for="description">Task Description</label>
        <textarea id="description" name="description" rows="5"><?= e($task['description'] ?? '') ?></textarea>

        <div class="form-grid">
            <div>
                <label for="priority">Priority</label>
                <select id="priority" name="priority" required>
                    <?php foreach (['High', 'Medium', 'Low'] as $priority): ?>
                        <option value="<?= $priority ?>" <?= $task['priority'] === $priority ? 'selected' : '' ?>>
                            <?= $priority ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <?php foreach (['Pending', 'Completed'] as $status): ?>
                        <option value="<?= $status ?>" <?= $task['status'] === $status ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="estimated_date">Estimated Completion Date</label>
                <input id="estimated_date" name="estimated_date" type="date" value="<?= e($task['estimated_date'] ?? '') ?>">
            </div>
        </div>

        <button class="button primary" type="submit">Update Task</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
