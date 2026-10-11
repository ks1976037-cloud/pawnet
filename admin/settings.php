<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth_helpers.php';

start_auth_session();
$page = 'settings';
$title = 'Settings';

$dataFile = __DIR__ . '/settings-data.json';

if (empty($_SESSION['settings_csrf'])) {
    $_SESSION['settings_csrf'] = bin2hex(random_bytes(32));
}

function settings_escape($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function settings_defaults(): array
{
    return [
        'site_name' => 'Paw Net',
        'site_email' => 'admin@pawnet.com',
        'contact_phone' => '',
        'site_description' => 'Connecting pets with loving homes.',
        'maintenance_mode' => false,
        'email_notifications' => true,
        'application_notifications' => true,
        'message_notifications' => true,
        'theme' => 'purple',
        'items_per_page' => 10
    ];
}

function settings_load(string $file): array
{
    $defaults = settings_defaults();

    if (!is_file($file)) {
        return $defaults;
    }

    $json = file_get_contents($file);

    if ($json === false || trim($json) === '') {
        return $defaults;
    }

    $saved = json_decode($json, true);

    return is_array($saved)
        ? array_merge($defaults, $saved)
        : $defaults;
}

function settings_save(string $file, array $settings): bool
{
    $json = json_encode(
        $settings,
        JSON_PRETTY_PRINT |
            JSON_UNESCAPED_SLASHES |
            JSON_INVALID_UTF8_SUBSTITUTE
    );

    if ($json === false) {
        return false;
    }

    return file_put_contents($file, $json, LOCK_EX) !== false;
}

$settings = settings_load($dataFile);
$notice = '';
$noticeType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['settings_csrf'], $token)) {
        http_response_code(403);
        exit('Invalid security token. Refresh the page and try again.');
    }

    $action = (string)($_POST['action'] ?? '');

    if ($action === 'reset') {
        $settings = settings_defaults();

        if (settings_save($dataFile, $settings)) {
            $notice = 'Settings restored to their defaults.';
        } else {
            $notice = 'Unable to save settings. Check folder permissions.';
            $noticeType = 'error';
        }
    } elseif ($action === 'save') {
        $siteName = trim((string)($_POST['site_name'] ?? ''));
        $siteEmail = trim((string)($_POST['site_email'] ?? ''));
        $phone = trim((string)($_POST['contact_phone'] ?? ''));
        $description = trim((string)($_POST['site_description'] ?? ''));
        $theme = (string)($_POST['theme'] ?? 'purple');
        $itemsPerPage = (int)($_POST['items_per_page'] ?? 10);

        if ($siteName === '' || mb_strlen($siteName) > 100) {
            $notice = 'Enter a site name of 1–100 characters.';
            $noticeType = 'error';
        } elseif (!filter_var($siteEmail, FILTER_VALIDATE_EMAIL)) {
            $notice = 'Please enter a valid contact email address.';
            $noticeType = 'error';
        } elseif (mb_strlen($phone) > 40 || mb_strlen($description) > 500) {
            $notice = 'The phone number or description is too long.';
            $noticeType = 'error';
        } elseif (!in_array($theme, ['purple', 'blue', 'green'], true)) {
            $notice = 'Please select a valid theme.';
            $noticeType = 'error';
        } elseif (!in_array($itemsPerPage, [5, 10, 20, 50], true)) {
            $notice = 'Please select a valid page size.';
            $noticeType = 'error';
        } else {
            $settings = [
                'site_name' => $siteName,
                'site_email' => $siteEmail,
                'contact_phone' => $phone,
                'site_description' => $description,
                'maintenance_mode' => isset($_POST['maintenance_mode']),
                'email_notifications' => isset($_POST['email_notifications']),
                'application_notifications' => isset($_POST['application_notifications']),
                'message_notifications' => isset($_POST['message_notifications']),
                'theme' => $theme,
                'items_per_page' => $itemsPerPage
            ];

            if (settings_save($dataFile, $settings)) {
                $notice = 'Settings saved successfully!';
            } else {
                $notice = 'Unable to save settings. Check folder permissions.';
                $noticeType = 'error';
            }
        }
    } else {
        $notice = 'Unknown settings action.';
        $noticeType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= settings_escape($title) ?> | Paw Net</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="settings.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <?php include 'header.php'; ?>

        <main class="content">

            <div class="welcome-row settings-welcome">
                <div>
                    <div class="eyebrow">ADMINISTRATION</div>
                    <h1>Settings</h1>
                    <p>Manage your Paw Net website preferences.</p>
                </div>
            </div>

            <?php if ($notice !== ''): ?>
                <div class="settings-notice <?= $noticeType === 'error' ? 'error' : '' ?>"
                    role="status">
                    <?= settings_escape($notice) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="settings.php" class="settings-form">
                <input type="hidden" name="csrf_token"
                    value="<?= settings_escape($_SESSION['settings_csrf']) ?>">
                <input type="hidden" name="action" value="save">

                <section class="panel settings-panel">
                    <div class="settings-section-heading">
                        <div class="settings-icon">⚙</div>
                        <div>
                            <h2>General Settings</h2>
                            <p>Basic information about your website.</p>
                        </div>
                    </div>

                    <div class="settings-grid">
                        <div class="settings-field">
                            <label for="site_name">Website Name</label>
                            <input id="site_name" name="site_name" type="text"
                                maxlength="100" required
                                value="<?= settings_escape($settings['site_name']) ?>">
                        </div>

                        <div class="settings-field">
                            <label for="site_email">Contact Email</label>
                            <input id="site_email" name="site_email" type="email"
                                required
                                value="<?= settings_escape($settings['site_email']) ?>">
                        </div>

                        <div class="settings-field">
                            <label for="contact_phone">Contact Phone</label>
                            <input id="contact_phone" name="contact_phone" type="tel"
                                maxlength="40"
                                value="<?= settings_escape($settings['contact_phone']) ?>">
                        </div>

                        <div class="settings-field">
                            <label for="site_description">Website Description</label>
                            <textarea id="site_description" name="site_description"
                                rows="3" maxlength="500"><?= settings_escape($settings['site_description']) ?></textarea>
                        </div>
                    </div>
                </section>

                <section class="panel settings-panel">
                    <div class="settings-section-heading">
                        <div class="settings-icon">🔔</div>
                        <div>
                            <h2>Notification Preferences</h2>
                            <p>Choose which notification preferences to save.</p>
                        </div>
                    </div>

                    <div class="settings-options">
                        <label class="settings-option">
                            <span>
                                <strong>Email Notifications</strong>
                                <small>Preference for general email notifications.</small>
                            </span>
                            <input type="checkbox" name="email_notifications"
                                <?= !empty($settings['email_notifications']) ? 'checked' : '' ?>>
                        </label>

                        <label class="settings-option">
                            <span>
                                <strong>Adoption Application Alerts</strong>
                                <small>Preference for application-related alerts.</small>
                            </span>
                            <input type="checkbox" name="application_notifications"
                                <?= !empty($settings['application_notifications']) ? 'checked' : '' ?>>
                        </label>

                        <label class="settings-option">
                            <span>
                                <strong>Message Alerts</strong>
                                <small>Preference for new-message alerts.</small>
                            </span>
                            <input type="checkbox" name="message_notifications"
                                <?= !empty($settings['message_notifications']) ? 'checked' : '' ?>>
                        </label>
                    </div>
                </section>

                <section class="panel settings-panel">
                    <div class="settings-section-heading">
                        <div class="settings-icon">🎨</div>
                        <div>
                            <h2>Display Preferences</h2>
                            <p>Choose your preferred theme and page size.</p>
                        </div>
                    </div>

                    <div class="settings-grid">
                        <div class="settings-field">
                            <label for="theme">Accent Theme</label>
                            <select id="theme" name="theme">
                                <option value="purple" <?= $settings['theme'] === 'purple' ? 'selected' : '' ?>>
                                    Purple (Default)
                                </option>
                                <option value="blue" <?= $settings['theme'] === 'blue' ? 'selected' : '' ?>>
                                    Blue
                                </option>
                                <option value="green" <?= $settings['theme'] === 'green' ? 'selected' : '' ?>>
                                    Green
                                </option>
                            </select>
                            <small>Saved as a preference. Applying the theme site-wide requires connecting it to your CSS.</small>
                        </div>

                        <div class="settings-field">
                            <label for="items_per_page">Items Per Page</label>
                            <select id="items_per_page" name="items_per_page">
                                <?php foreach ([5, 10, 20, 50] as $count): ?>
                                    <option value="<?= $count ?>"
                                        <?= (int)$settings['items_per_page'] === $count ? 'selected' : '' ?>>
                                        <?= $count ?> items
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small>Saved preference; other modules must read this setting to use it.</small>
                        </div>
                    </div>
                </section>

                <section class="panel settings-panel settings-danger">
                    <div class="settings-section-heading">
                        <div class="settings-icon">🛠</div>
                        <div>
                            <h2>Site Availability</h2>
                            <p>Save your intended maintenance-mode setting.</p>
                        </div>
                    </div>

                    <label class="settings-option">
                        <span>
                            <strong>Maintenance Mode</strong>
                            <small>This saves a preference only. It does not block public access until implemented on the user-facing site.</small>
                        </span>
                        <input type="checkbox" name="maintenance_mode"
                            <?= !empty($settings['maintenance_mode']) ? 'checked' : '' ?>>
                    </label>
                </section>

                <div class="settings-actions">
                    <button type="submit" class="primary-btn">Save Settings</button>
                </div>
            </form>

            <form method="post" action="settings.php" class="settings-reset"
                onsubmit="return confirm('Restore all settings to their defaults?');">
                <input type="hidden" name="csrf_token"
                    value="<?= settings_escape($_SESSION['settings_csrf']) ?>">
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="settings-reset-btn">Reset to Defaults</button>
            </form>

        </main>
    </div>

    <script src="script.js"></script>
</body>

</html>