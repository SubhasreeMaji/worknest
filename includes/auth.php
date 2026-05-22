<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        flash('error', 'Please login to continue.');
        redirect('/index.php');
    }
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function current_user_id(): int
{
    return (int) $_SESSION['user_id'];
}

