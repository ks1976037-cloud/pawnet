<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth_helpers.php';

start_auth_session();
$page = 'users';
$title = 'Manage Users';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Paw Net</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main">
        <?php include 'header.php'; ?>

        <main class="content">
            <div class="welcome-row">
                <div>
                    <div class="eyebrow">USER MANAGEMENT</div>
                    <h1>Manage Users 👥</h1>
                    <p>Manage registered pet adopters.</p>
                </div>
            </div>

            <section class="panel">
                <div class="panel-heading">
                    <div>
                        <h2>Registered Users</h2>
                        <p>Sample user accounts</p>
                    </div>
                    <input class="table-search" id="userSearch"
                        placeholder="Search users...">
                </div>

                <div class="table-wrap">
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Aarav Sharma</td>
                                <td>aarav@example.com</td>
                                <td>Aug 12, 2026</td>
                                <td><span class="status approved">Active</span></td>
                                <td><button class="text-btn toggle-user">Deactivate</button></td>
                            </tr>
                            <tr>
                                <td>Diya Patel</td>
                                <td>diya@example.com</td>
                                <td>Aug 18, 2026</td>
                                <td><span class="status approved">Active</span></td>
                                <td><button class="text-btn toggle-user">Deactivate</button></td>
                            </tr>
                            <tr>
                                <td>Rohan Das</td>
                                <td>rohan@example.com</td>
                                <td>Sep 02, 2026</td>
                                <td><span class="status rejected">Inactive</span></td>
                                <td><button class="text-btn toggle-user">Activate</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
        document.getElementById('userSearch').addEventListener('input', function() {
            const query = this.value.toLowerCase();

            document.querySelectorAll('#usersTable tbody tr').forEach(row => {
                row.style.display =
                    row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });

        document.querySelectorAll('.toggle-user').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const status = row.querySelector('.status');
                const active = status.textContent.trim() === 'Active';

                status.textContent = active ? 'Inactive' : 'Active';
                status.className = 'status ' +
                    (active ? 'rejected' : 'approved');

                this.textContent = active ? 'Activate' : 'Deactivate';
            });
        });
    </script>
</body>

</html>