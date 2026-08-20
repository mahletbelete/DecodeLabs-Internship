<?php

require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

ini_set('session.cookie_path', '/');
session_start();

if (!empty($_SESSION['user_id'])) {
    jsonResponse(true, 'Logged in.', [
        'loggedIn' => true,
        'name'     => $_SESSION['user_name'],
        'role'     => $_SESSION['user_role']
    ]);
} else {
    jsonResponse(true, 'Not logged in.', [
        'loggedIn' => false,
        'name'     => null,
        'role'     => null
    ]);
}
