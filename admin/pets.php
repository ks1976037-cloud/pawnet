<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth_helpers.php';
start_auth_session();
$page = 'pets';
$title = 'Manage Pets';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | Paw Net</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .pet-name {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 180px;
            font-weight: 600;
            color: #211832;
        }

        .pet-image {
            width: 55px;
            height: 55px;
            min-width: 55px;
            object-fit: cover;
            border-radius: 14px;
        }

        #petsTable {
            width: 100%;
            border-collapse: collapse;
        }

        #petsTable th,
        #petsTable td {
            padding: 15px;
            text-align: left;
            vertical-align: middle;
        }

        #petsTable tbody tr {
            border-bottom: 1px solid #eee8f7;
        }

        #petsTable tbody tr:hover {
            background: #fcfaff;
        }

        .pet-description {
            font-size: 12px;
            color: #796e8a;
            font-weight: normal;
            margin-top: 4px;
        }

        .pet-status {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            background: #e3f8f0;
            color: #00a889;
            font-size: 13px;
            font-weight: 600;
        }

        .remove-pet {
            border: none;
            background: none;
            color: #8052ff;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }

        .remove-pet:hover {
            color: #603bb5;
        }

        @media (max-width: 700px) {
            .table-wrap {
                overflow-x: auto;
            }

            #petsTable {
                min-width: 650px;
            }
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main">
        <?php include __DIR__ . '/header.php'; ?>
        <main class="content">

            <div class="welcome-row">
                <div>
                    <div class="eyebrow">PET MANAGEMENT</div>
                    <h1>Manage Pets 🐾</h1>
                    <p>View and manage pets listed for adoption.</p>
                </div>

                <a href="add_pet.php"
                    class="save-btn"
                    style="display:inline-block;text-decoration:none;">
                    + Add Pet
                </a>
            </div>

            <section class="panel">

                <div class="panel-heading">
                    <div>
                        <h2>Pet Listings</h2>
                        <p>Manage pets available for adoption.</p>
                    </div>

                    <input
                        class="table-search"
                        id="petSearch"
                        placeholder="Search pets...">
                </div>

                <div class="table-wrap">

                    <table id="petsTable">

                        <thead>
                            <tr>
                                <th>Pet</th>
                                <th>Breed</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="petsTableBody">

                            <!-- Existing demo pet 1 -->
                            <tr>
                                <td>
                                    <span class="pet-name">
                                        <img
                                            src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=200"
                                            alt="Bruno"
                                            class="pet-image">
                                        Bruno
                                    </span>
                                </td>
                                <td>Labrador</td>
                                <td>2 years</td>
                                <td><span class="pet-status">Available</span></td>
                                <td>
                                    <button class="remove-pet remove-row">
                                        Remove
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing demo pet 2 -->
                            <tr>
                                <td>
                                    <span class="pet-name">
                                        <img
                                            src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=200"
                                            alt="Misty"
                                            class="pet-image">
                                        Misty
                                    </span>
                                </td>
                                <td>Persian Cat</td>
                                <td>1 year</td>
                                <td><span class="pet-status">Available</span></td>
                                <td>
                                    <button class="remove-pet remove-row">
                                        Remove
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing demo pet 3 -->
                            <tr>
                                <td>
                                    <span class="pet-name">
                                        <img
                                            src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=200"
                                            alt="Rocky"
                                            class="pet-image">
                                        Rocky
                                    </span>
                                </td>
                                <td>Indie</td>
                                <td>3 years</td>
                                <td>
                                    <span class="status review">On hold</span>
                                </td>
                                <td>
                                    <button class="remove-pet remove-row">
                                        Remove
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing demo pet 4 -->
                            <tr>
                                <td>
                                    <span class="pet-name">
                                        <img
                                            src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=200"
                                            alt="Peanut"
                                            class="pet-image">
                                        Peanut
                                    </span>
                                </td>
                                <td>Hamster</td>
                                <td>6 months</td>
                                <td><span class="pet-status">Available</span></td>
                                <td>
                                    <button class="remove-pet remove-row">
                                        Remove
                                    </button>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <p class="hint">
                    Demo only: pet additions and removals are stored in this
                    browser and are not saved to a database.
                </p>

            </section>

        </main>
    </div>

    <script src="script.js"></script>

    <script>
        const tbody = document.getElementById('petsTableBody');
        const searchInput = document.getElementById('petSearch');

        let addedPets = [];

        try {
            addedPets = JSON.parse(
                localStorage.getItem('pawnetPets') || '[]'
            );

            if (!Array.isArray(addedPets)) {
                addedPets = [];
            }
        } catch (error) {
            addedPets = [];
        }

        // Render pets from the Add Pet form in the existing table.
        function renderAddedPets() {
            tbody.querySelectorAll('.added-pet-row').forEach(row => {
                row.remove();
            });

            addedPets.forEach(pet => {
                const row = document.createElement('tr');
                row.className = 'added-pet-row';

                // Pet image and name
                const petCell = document.createElement('td');
                const petInfo = document.createElement('span');
                petInfo.className = 'pet-name';

                const img = document.createElement('img');
                img.src = pet.image;
                img.alt = pet.name;
                img.className = 'pet-image';

                img.onerror = function() {
                    this.style.visibility = 'hidden';
                };

                const name = document.createElement('span');
                name.textContent = pet.name;

                petInfo.append(img, name);
                petCell.appendChild(petInfo);

                // Breed
                const breedCell = document.createElement('td');
                breedCell.textContent = pet.breed;

                // Age
                const ageCell = document.createElement('td');
                ageCell.textContent = pet.age + ' years';

                // Status
                const statusCell = document.createElement('td');
                const status = document.createElement('span');
                status.className = 'pet-status';
                status.textContent = 'Available';
                statusCell.appendChild(status);

                // Remove action
                const actionCell = document.createElement('td');
                const removeButton = document.createElement('button');
                removeButton.className = 'remove-pet';
                removeButton.textContent = 'Remove';

                removeButton.addEventListener('click', function() {
                    addedPets = addedPets.filter(item => item.id !== pet.id);

                    localStorage.setItem(
                        'pawnetPets',
                        JSON.stringify(addedPets)
                    );

                    renderAddedPets();
                    filterPets();
                });

                actionCell.appendChild(removeButton);

                row.append(
                    petCell,
                    breedCell,
                    ageCell,
                    statusCell,
                    actionCell
                );

                tbody.appendChild(row);
            });

            filterPets();
        }

        // Search across both existing and newly added pets.
        function filterPets() {
            const query = searchInput.value.toLowerCase().trim();

            tbody.querySelectorAll('tr').forEach(row => {
                row.style.display = row.textContent
                    .toLowerCase()
                    .includes(query) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterPets);

        // Remove existing demo rows from the table.
        document.querySelectorAll('.remove-row').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });

        renderAddedPets();
    </script>

</body>

</html>