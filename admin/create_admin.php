<?php

declare(strict_types=1);

require_once __DIR__ . '/auth_helpers.php';

start_auth_session();

$dataDirectory = __DIR__ . '/data';
$accountsFile = auth_users_file();

// Ensure the data folder exists.
if (!is_dir($dataDirectory)) {
    if (!mkdir($dataDirectory, 0750, true)) {
        exit('Could not create the data directory.');
    }
}

// Do not overwrite an existing accounts file.
if (file_exists($accountsFile)) {
    $users = load_auth_users();
} else {
    $users = [];
}

// Prevent duplicate admin creation.
foreach ($users as $user) {
    if (($user['role'] ?? 'user') === 'admin') {
        exit('An admin account already exists. No changes made.');
    }
}

// Change these credentials before running this file.
$name = 'Paw Net Admin';
$email = 'admin@pawnet.com';
$password = 'ChangeThisPassword123!';

// Prevent an email collision with an existing account.
foreach ($users as $user) {
    if (strtolower($user['email'] ?? '') === strtolower($email)) {
        exit('This email already exists. Choose a different admin email.');
    }
}

$users[] = [
    'id' => bin2hex(random_bytes(8)),
    'name' => $name,
    'email' => strtolower($email),
    'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
    'phone' => '',
    'address' => '',
    'role' => 'admin'
];

if (!save_auth_users($users)) {
    exit('Could not save the admin account. Check folder permissions.');
}

echo 'Admin account created successfully. Delete create_admin.php now.';
