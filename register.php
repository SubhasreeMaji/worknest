<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (is_logged_in()) {
    redirect('/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
        flash('error', 'All fields are required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Enter a valid email address.');
    } elseif (strlen($password) < 6) {
        flash('error', 'Password must be at least 6 characters.');
    } elseif ($password !== $confirmPassword) {
        flash('error', 'Passwords do not match.');
    } else {
        $statement = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);

        if ($statement->fetch()) {
            flash('error', 'Email is already registered.');
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $statement = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $statement->execute([$name, $email, $hashedPassword]);

            flash('success', 'Account created. Please login.');
            redirect('/index.php');
        }
    }
}

$pageTitle = 'Register - TaskTracker';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-layout">
    <div class="intro-panel">
        <p class="eyebrow">Create your space</p>
        <h1>Start tracking tasks in a few seconds.</h1>
        <p>Each account gets a private task list with its own priorities, dates, and status updates.</p>
    </div>

    <form class="form-card" method="post" action="<?= e(url('/register.php')) ?>">
        <h2>Register</h2>

        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="<?= e($_POST['name'] ?? '') ?>" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" minlength="6" required>

        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" name="confirm_password" type="password" minlength="6" required>

        <button class="button primary" type="submit">Create Account</button>
        <p class="form-note">Already registered? <a href="<?= e(url('/index.php')) ?>">Login</a></p>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
