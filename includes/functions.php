<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function base_path(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = rtrim(dirname($scriptName), '/');

    if (substr($base, -6) === '/tasks') {
        $base = rtrim(dirname($base), '/');
    }

    return $base === '/' ? '' : $base;
}

function url(string $path): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    $location = strpos($path, 'http') === 0 ? $path : url($path);
    header("Location: {$location}");
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION[$key] = $message;
        return null;
    }

    if (!isset($_SESSION[$key])) {
        return null;
    }

    $storedMessage = $_SESSION[$key];
    unset($_SESSION[$key]);

    return $storedMessage;
}

function validate_priority(string $priority): bool
{
    return in_array($priority, ['High', 'Medium', 'Low'], true);
}

function validate_status(string $status): bool
{
    return in_array($status, ['Pending', 'Completed'], true);
}
