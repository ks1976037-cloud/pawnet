<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth_helpers.php';
start_auth_session();
$page = 'adoptions';
$title = 'Adoption Records';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Records | Paw Net</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main">
        <?php include 'header.php'; ?>

        <main class="content">
            <div class="welcome-row">
                <div>
                    <div class="eyebrow">ADOPTION MANAGEMENT</div>
                    <h1>Adoption Records 🏡</h1>
                    <p>Track pets that have found their forever homes.</p>
                </div>
            </div>

            <section class="panel">
                <div class="panel-heading">
                    <div>
                        <h2>Adoption History</h2>
                        <p>View completed and ongoing adoptions.</p>
                    </div>
                    <input class="table-search" id="recordSearch"
                        placeholder="Search records...">
                </div>

                <div class="table-wrap">
                    <table id="recordsTable">
                        <thead>
                            <tr>
                                <th>Pet</th>
                                <th>Adopter</th>
                                <th>Adoption Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                        class="pet-image">
                                    Bruno</td>
                                <td>Ananya Rao</td>
                                <td>Sep 28, 2026</td>
                                <td><span class="status approved">Completed</span></td>
                                <td><button class="text-btn view-record">View</button></td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                        class="pet-image">
                                    Bruno</td>
                                <td>Kabir Mehta</td>
                                <td>Sep 21, 2026</td>
                                <td><span class="status approved">Completed</span></td>
                                <td><button class="text-btn view-record">View</button></td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                        class="pet-image">
                                    Bruno</td>
                                <td>Sana Iyer</td>
                                <td>Sep 15, 2026</td>
                                <td><span class="status review">In Progress</span></td>
                                <td><button class="text-btn view-record">View</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
        const search = document.getElementById('recordSearch');

        search.addEventListener('input', function() {
            document.querySelectorAll('#recordsTable tbody tr')
                .forEach(row => {
                    row.style.display =
                        row.innerText.toLowerCase().includes(
                            search.value.toLowerCase()
                        ) ? '' : 'none';
                });
        });

        document.querySelectorAll('.view-record').forEach(button => {
            button.addEventListener('click', function() {
                alert(this.closest('tr').innerText);
            });
        });
    </script>
</body>

</html>