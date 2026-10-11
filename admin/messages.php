<?php
require_once __DIR__ . '/../auth_helpers.php';
start_auth_session();
$page = 'messages';
$title = 'Messages';

$dataFile = __DIR__ . '/messages-data.json';

if (empty($_SESSION['messages_csrf'])) {
    $_SESSION['messages_csrf'] = bin2hex(random_bytes(32));
}

function messages_escape($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function messages_load(string $file): array
{
    if (!is_file($file)) {
        return [];
    }

    $contents = file_get_contents($file);

    if ($contents === false || trim($contents) === '') {
        return [];
    }

    $data = json_decode($contents, true);

    return is_array($data) ? $data : [];
}

function messages_save(string $file, array $data): bool
{
    $json = json_encode(
        array_values($data),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );

    if ($json === false) {
        return false;
    }

    return file_put_contents($file, $json, LOCK_EX) !== false;
}

function messages_find_index(array $messages, string $id): int
{
    foreach ($messages as $index => $message) {
        if ((string)($message['id'] ?? '') === $id) {
            return $index;
        }
    }

    return -1;
}

function messages_redirect(string $id = ''): void
{
    $url = 'messages.php';

    if ($id !== '') {
        $url .= '?conversation=' . rawurlencode($id);
    }

    header('Location: ' . $url);
    exit;
}

$messages = messages_load($dataFile);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['messages_csrf'], $token)) {
        http_response_code(403);
        exit('Invalid security token. Refresh the page and try again.');
    }

    $action = (string)($_POST['action'] ?? '');
    $id = trim((string)($_POST['conversation_id'] ?? ''));

    $index = messages_find_index($messages, $id);

    if ($index < 0) {
        $error = 'Conversation not found.';
    } elseif ($action === 'reply') {
        $reply = trim((string)($_POST['reply'] ?? ''));

        if ($reply === '') {
            $error = 'Please write a reply before sending.';
        } elseif (mb_strlen($reply) > 5000) {
            $error = 'Your reply must be 5000 characters or fewer.';
        } else {
            $messages[$index]['messages'] ??= [];

            $messages[$index]['messages'][] = [
                'sender' => 'admin',
                'body' => $reply,
                'time' => date('Y-m-d H:i:s')
            ];

            $messages[$index]['unread'] = false;
            $messages[$index]['updated_at'] = date('Y-m-d H:i:s');

            if (messages_save($dataFile, $messages)) {
                messages_redirect($id);
            }

            $error = 'Unable to save your reply. Check folder permissions.';
        }
    } elseif ($action === 'mark_read') {
        $messages[$index]['unread'] = false;

        if (messages_save($dataFile, $messages)) {
            messages_redirect($id);
        }

        $error = 'Unable to update the message status.';
    } else {
        $error = 'Unknown action.';
    }
}

$selectedId = trim((string)($_GET['conversation'] ?? ''));
$search = trim((string)($_GET['search'] ?? ''));
$status = (string)($_GET['status'] ?? 'all');

$selectedIndex = messages_find_index($messages, $selectedId);

if ($selectedIndex >= 0) {
    // Mark the selected conversation as read.
    if (!empty($messages[$selectedIndex]['unread'])) {
        $messages[$selectedIndex]['unread'] = false;
        messages_save($dataFile, $messages);
    }
} else {
    $selectedId = '';
}

$filtered = [];

foreach ($messages as $message) {
    $lastMessage = '';
    $conversationMessages = $message['messages'] ?? [];

    if (is_array($conversationMessages) && count($conversationMessages) > 0) {
        $last = end($conversationMessages);
        $lastMessage = (string)($last['body'] ?? '');
        reset($conversationMessages);
    }

    $haystack = strtolower(
        (string)($message['name'] ?? '') . ' ' .
            (string)($message['email'] ?? '') . ' ' .
            (string)($message['subject'] ?? '') . ' ' .
            $lastMessage
    );

    if ($search !== '' && strpos($haystack, strtolower($search)) === false) {
        continue;
    }

    $isUnread = !empty($message['unread']);

    if ($status === 'unread' && !$isUnread) {
        continue;
    }

    if ($status === 'read' && $isUnread) {
        continue;
    }

    $filtered[] = $message;
}

usort($filtered, function ($a, $b) {
    return strcmp(
        (string)($b['updated_at'] ?? ''),
        (string)($a['updated_at'] ?? '')
    );
});

if ($selectedId === '' && count($filtered) > 0) {
    $selectedId = (string)($filtered[0]['id'] ?? '');
    $selectedIndex = messages_find_index($messages, $selectedId);
}

$selected = $selectedIndex >= 0 ? $messages[$selectedIndex] : null;

$unreadCount = 0;

foreach ($messages as $message) {
    if (!empty($message['unread'])) {
        $unreadCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= messages_escape($title) ?> | Paw Net</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="messages.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <?php include 'header.php'; ?>

        <main class="content">

            <div class="welcome-row">
                <div>
                    <div class="eyebrow">COMMUNICATION</div>
                    <h1>Messages</h1>
                    <p>Read and reply to messages from Paw Net users.</p>
                </div>

                <div class="messages-count">
                    <?= (int)$unreadCount ?> unread
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="messages-alert">
                    <?= messages_escape($error) ?>
                </div>
            <?php endif; ?>

            <section class="messages-workspace">

                <aside class="messages-inbox panel">

                    <h2>Inbox</h2>

                    <form method="get" action="messages.php" class="messages-search">
                        <input
                            type="search"
                            name="search"
                            placeholder="Search messages..."
                            value="<?= messages_escape($search) ?>"
                            aria-label="Search messages">

                        <select name="status" aria-label="Filter messages">
                            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>
                                All messages
                            </option>
                            <option value="unread" <?= $status === 'unread' ? 'selected' : '' ?>>
                                Unread
                            </option>
                            <option value="read" <?= $status === 'read' ? 'selected' : '' ?>>
                                Read
                            </option>
                        </select>

                        <button type="submit" class="primary-btn">Search</button>
                    </form>

                    <div class="conversation-list">

                        <?php if (count($filtered) === 0): ?>
                            <div class="messages-empty">
                                <strong>No messages found</strong>
                                <p>Messages from users will appear here.</p>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($filtered as $item): ?>
                            <?php
                            $itemId = (string)($item['id'] ?? '');
                            $conversationItems = $item['messages'] ?? [];
                            $preview = '';

                            if (is_array($conversationItems) && count($conversationItems) > 0) {
                                $lastItem = end($conversationItems);
                                $preview = (string)($lastItem['body'] ?? '');
                                reset($conversationItems);
                            }

                            $active = $itemId === $selectedId;
                            ?>

                            <a
                                class="conversation-item <?= $active ? 'selected' : '' ?>"
                                href="messages.php?conversation=<?= rawurlencode($itemId) ?>">
                                <div class="conversation-item-top">
                                    <strong>
                                        <?= messages_escape($item['name'] ?? 'User') ?>
                                    </strong>

                                    <?php if (!empty($item['unread'])): ?>
                                        <span class="unread-dot" title="Unread"></span>
                                    <?php endif; ?>
                                </div>

                                <span class="conversation-subject">
                                    <?= messages_escape($item['subject'] ?? 'No subject') ?>
                                </span>

                                <span class="conversation-preview">
                                    <?= messages_escape($preview) ?>
                                </span>
                            </a>
                        <?php endforeach; ?>

                    </div>
                </aside>

                <section class="messages-conversation panel">

                    <?php if ($selected !== null): ?>

                        <header class="conversation-header">
                            <div>
                                <h2>
                                    <?= messages_escape($selected['name'] ?? 'User') ?>
                                </h2>

                                <p>
                                    <?= messages_escape($selected['email'] ?? '') ?>
                                </p>

                                <strong class="conversation-subject-heading">
                                    <?= messages_escape($selected['subject'] ?? 'No subject') ?>
                                </strong>
                            </div>

                            <?php if (!empty($selected['unread'])): ?>
                                <form method="post" action="messages.php?conversation=<?= rawurlencode($selectedId) ?>">
                                    <input type="hidden" name="csrf_token"
                                        value="<?= messages_escape($_SESSION['messages_csrf']) ?>">
                                    <input type="hidden" name="conversation_id"
                                        value="<?= messages_escape($selectedId) ?>">
                                    <input type="hidden" name="action" value="mark_read">

                                    <button type="submit" class="secondary-btn">
                                        Mark as read
                                    </button>
                                </form>
                            <?php endif; ?>
                        </header>

                        <div class="message-thread">

                            <?php foreach (($selected['messages'] ?? []) as $itemMessage): ?>
                                <?php
                                $isAdmin = ($itemMessage['sender'] ?? '') === 'admin';
                                ?>

                                <div class="message-bubble-wrap <?= $isAdmin ? 'sent' : 'received' ?>">
                                    <div class="message-bubble">
                                        <div class="message-sender">
                                            <?= $isAdmin ? 'You · Admin' : messages_escape($selected['name'] ?? 'User') ?>
                                        </div>

                                        <p><?= nl2br(messages_escape($itemMessage['body'] ?? '')) ?></p>

                                        <time>
                                            <?= messages_escape($itemMessage['time'] ?? '') ?>
                                        </time>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                        <form method="post"
                            action="messages.php?conversation=<?= rawurlencode($selectedId) ?>"
                            class="reply-form">

                            <input type="hidden" name="csrf_token"
                                value="<?= messages_escape($_SESSION['messages_csrf']) ?>">

                            <input type="hidden" name="conversation_id"
                                value="<?= messages_escape($selectedId) ?>">

                            <input type="hidden" name="action" value="reply">

                            <label for="reply">Write a reply</label>

                            <textarea
                                id="reply"
                                name="reply"
                                rows="4"
                                maxlength="5000"
                                placeholder="Type your reply here..."
                                required></textarea>

                            <div class="reply-footer">
                                <span>Replies are saved in the messages file.</span>
                                <button type="submit" class="primary-btn">
                                    Send Reply
                                </button>
                            </div>
                        </form>

                    <?php else: ?>

                        <div class="messages-empty conversation-empty">
                            <h2>Your inbox is empty</h2>
                            <p>When user messages are received, you can read and reply to them here.</p>
                        </div>

                    <?php endif; ?>

                </section>

            </section>

        </main>
    </div>

    <script src="script.js"></script>
</body>

</html>