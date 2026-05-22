<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? 'Pending';
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
            'INSERT INTO tasks (user_id, title, description, priority, status, created_date, estimated_date)
             VALUES (?, ?, ?, ?, ?, CURDATE(), ?)'
        );
        $statement->execute([current_user_id(), $title, $description, $priority, $status, $estimatedDate]);

        flash('success', 'Task created successfully.');
        redirect('/dashboard.php');
    }
}

$pageTitle = 'Create Task - TaskTracker';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="form-shell">
    <div class="section-heading">
        <h1>Create Task</h1>
        <a class="button secondary" href="<?= e(url('/dashboard.php')) ?>">Back</a>
    </div>

    <?php foreach ($errors as $error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form class="form-card wide" method="post" action="<?= e(url('/tasks/create.php')) ?>">
        <label for="title">Task Title</label>
        <input id="title" name="title" type="text" value="<?= e($_POST['title'] ?? '') ?>" required>

        <label for="description">Task Description</label>
        <textarea id="description" name="description" rows="5"><?= e($_POST['description'] ?? '') ?></textarea>

        <div class="form-grid">
            <div>
                <label for="priority">Priority</label>
                <select id="priority" name="priority" required>
                    <?php foreach (['High', 'Medium', 'Low'] as $priority): ?>
                        <option value="<?= $priority ?>" <?= ($_POST['priority'] ?? 'Medium') === $priority ? 'selected' : '' ?>>
                            <?= $priority ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <?php foreach (['Pending', 'Completed'] as $status): ?>
                        <option value="<?= $status ?>" <?= ($_POST['status'] ?? 'Pending') === $status ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="estimated_date">Estimated Completion Date</label>
                <input id="estimated_date" name="estimated_date" type="date" value="<?= e($_POST['estimated_date'] ?? '') ?>">
            </div>
        </div>

        <button class="button primary" type="submit">Save Task</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
