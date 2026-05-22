<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (is_logged_in()) {
    redirect('/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        flash('error', 'Email and password are required.');
    } else {
        $statement = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            redirect('/dashboard.php');
        }

        flash('error', 'Invalid email or password.');
    }
}

$pageTitle = 'Login - TaskTracker';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-layout">
    <div class="intro-panel">
        <p class="eyebrow">Welcome to WorkNest</p>
        <h1>Organize your tasks with a clean personal dashboard.</h1>
        <p>Register, login, and manage your own pending and completed work with priorities and due dates.</p>
    </div>

    <form class="form-card" method="post" action="<?= e(url('/index.php')) ?>">
        <h2>Login</h2>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button class="button primary" type="submit">Login</button>
        <!-- <p class="form-note">Default admin: admin@gmail.com / 1234@abcd</p>-->
        <p class="form-note">New here? <a href="<?= e(url('/register.php')) ?>">Create an account</a></p>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
