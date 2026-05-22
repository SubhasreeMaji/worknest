<?php
require_once __DIR__ . '/includes/auth.php';

$query = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '';
redirect('/tasks/edit.php' . $query);
