<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth_helpers.php';

start_auth_session();

$page = 'profile';
$title = 'Admin Profile';

$dataFile = __DIR__ . '/admin-profile-data.json';

$profile = [
    'name'  => 'Admin',
    'email' => 'admin@pawnet.com',
    'phone' => '',
    'role'  => 'Administrator',
    'about' => ''
];

if (is_file($dataFile)) {
    $saved = json_decode(file_get_contents($dataFile), true);

    if (is_array($saved)) {
        $profile = array_merge($profile, $saved);
    }
}

$message = '';
$messageType = '';
$passwordMessage = '';
$passwordMessageType = '';

function profile_escape($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!auth_csrf_is_valid()) {
        if ($action === 'change_password') {
            $passwordMessage = 'Session expired. Refresh the page and try again.';
            $passwordMessageType = 'error';
        } else {
            $message = 'Session expired. Refresh the page and try again.';
            $messageType = 'error';
        }
    } elseif ($action === 'save_profile') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $about = trim((string) ($_POST['about'] ?? ''));

        if (
            $name === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $message = 'Please enter a valid name and email address.';
            $messageType = 'error';
        } else {
            $updatedProfile = [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
                'role'  => $profile['role'],
                'about' => $about
            ];

            $json = json_encode(
                $updatedProfile,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );

            if (
                $json !== false &&
                file_put_contents($dataFile, $json, LOCK_EX) !== false
            ) {
                $profile = $updatedProfile;
                $message = 'Profile updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Unable to save. Check folder permissions.';
                $messageType = 'error';
            }
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (
            !is_string($currentPassword) ||
            !is_string($newPassword) ||
            !is_string($confirmPassword)
        ) {
            $passwordMessage = 'Invalid form data.';
            $passwordMessageType = 'error';
        } elseif (strlen($newPassword) < 8) {
            $passwordMessage = 'New password must contain at least 8 characters.';
            $passwordMessageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $passwordMessage = 'The new passwords do not match.';
            $passwordMessageType = 'error';
        } elseif ($currentPassword === $newPassword) {
            $passwordMessage = 'Choose a password different from your current password.';
            $passwordMessageType = 'error';
        } else {
            $sessionUser = $_SESSION['user'] ?? [];
            $users = load_auth_users();
            $userIndex = null;

            // Find the logged-in administrator account.
            foreach ($users as $key => $account) {
                if (
                    is_array($account) &&
                    isset($sessionUser['email'], $account['email']) &&
                    strcasecmp(
                        (string) $account['email'],
                        (string) $sessionUser['email']
                    ) === 0 &&
                    ($account['role'] ?? 'user') === 'admin'
                ) {
                    $userIndex = $key;
                    break;
                }
            }

            if ($userIndex === null) {
                $passwordMessage = 'Admin account not found in accounts.php.';
                $passwordMessageType = 'error';
            } else {
                // Assumes the stored password hash is in the "password" field.
                $storedHash = $users[$userIndex]['password'] ?? '';

                if (
                    !is_string($storedHash) ||
                    $storedHash === '' ||
                    !password_verify($currentPassword, $storedHash)
                ) {
                    $passwordMessage = 'Your current password is incorrect.';
                    $passwordMessageType = 'error';
                } else {
                    $users[$userIndex]['password'] =
                        password_hash($newPassword, PASSWORD_DEFAULT);

                    if (save_auth_users($users)) {
                        $passwordMessage = 'Password changed successfully!';
                        $passwordMessageType = 'success';
                    } else {
                        $passwordMessage = 'Unable to save the new password. Check file permissions.';
                        $passwordMessageType = 'error';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= profile_escape($title) ?> | Paw Net</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin-profile.css">
</head>

<body>

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main">

        <?php include __DIR__ . '/header.php'; ?>

        <main class="content">

            <!-- Page heading -->
            <div class="welcome-row">
                <div>
                    <div class="eyebrow">ACCOUNT MANAGEMENT</div>
                    <h1>Admin Profile</h1>
                    <p>Manage your administrator information and security.</p>
                </div>
            </div>

            <!-- Profile notification -->
            <?php if ($message !== ''): ?>
                <div class="profile-notice <?= profile_escape($messageType) ?>">
                    <?= profile_escape($message) ?>
                </div>
            <?php endif; ?>

            <!-- Profile summary and personal information -->
            <section class="admin-profile-layout">

                <article class="panel admin-profile-summary">

                    <div class="admin-profile-avatar">
                        <?= profile_escape(
                            strtoupper(substr($profile['name'], 0, 1))
                        ) ?>
                    </div>

                    <h2><?= profile_escape($profile['name']) ?></h2>

                    <p class="admin-role">
                        <?= profile_escape($profile['role']) ?>
                    </p>

                    <span class="admin-active">● Active</span>

                    <div class="admin-profile-divider"></div>

                    <div class="admin-profile-detail">
                        <span>Account type</span>
                        <strong>Administrator</strong>
                    </div>

                    <div class="admin-profile-detail">
                        <span>Email</span>
                        <strong><?= profile_escape($profile['email']) ?></strong>
                    </div>

                    <?php if ($profile['phone'] !== ''): ?>
                        <div class="admin-profile-detail">
                            <span>Phone</span>
                            <strong><?= profile_escape($profile['phone']) ?></strong>
                        </div>
                    <?php endif; ?>

                </article>

                <article class="panel admin-profile-form">

                    <div class="panel-heading">
                        <div>
                            <h2>Personal Information</h2>
                            <p>Update your administrator details.</p>
                        </div>
                    </div>

                    <form method="POST" action="admin-profile.php">

                        <input type="hidden" name="action" value="save_profile">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= profile_escape(auth_csrf_token()) ?>">

                        <label for="name">Full Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="<?= profile_escape($profile['name']) ?>"
                            maxlength="120"
                            required>

                        <label for="email">Email Address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="<?= profile_escape($profile['email']) ?>"
                            required>

                        <label for="phone">Phone Number</label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="<?= profile_escape($profile['phone']) ?>"
                            maxlength="40">

                        <!-- Change Password fields inside Personal Information -->
                        <div class="inline-password-section">
                            <?php if ($passwordMessage !== ''): ?>
                                <div class="password-message <?= profile_escape($passwordMessageType) ?>">
                                    <?= profile_escape($passwordMessage) ?>
                                </div>
                            <?php endif; ?>

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= profile_escape(auth_csrf_token()) ?>">

                            <div class="inline-password-fields">
                                <div class="password-field">
                                    <label for="current_password">Current Password</label>
                                    <input
                                        type="password"
                                        id="current_password"
                                        name="current_password"
                                        autocomplete="current-password"
                                        required
                                        form="passwordForm">
                                </div>

                                <div class="password-field">
                                    <label for="new_password">New Password</label>
                                    <input
                                        type="password"
                                        id="new_password"
                                        name="new_password"
                                        minlength="8"
                                        autocomplete="new-password"
                                        required
                                        form="passwordForm">
                                </div>

                                <div class="password-field">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input
                                        type="password"
                                        id="confirm_password"
                                        name="confirm_password"
                                        minlength="8"
                                        autocomplete="new-password"
                                        required
                                        form="passwordForm">
                                </div>
                            </div>
                        </div>

                        <label for="about">About Me</label>
                        <textarea
                            id="about"
                            name="about"
                            rows="4"
                            maxlength="1000"><?= profile_escape($profile['about']) ?></textarea>

                        <button type="submit" class="primary-btn">
                            Save Changes
                        </button>

                    </form>

                </article>

            </section>

        </main>
    </div>

    <script src="script.js"></script>

</body>

</html>