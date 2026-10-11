<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth_helpers.php';

start_auth_session();
$page = 'pets';
$title = 'Add New Pet';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet | Paw Net</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .pet-form {
            max-width: 850px;
            margin: 25px auto;
            padding: 28px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(90, 60, 150, 0.08);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            color: #413653;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2d9f2;
            border-radius: 10px;
            font: inherit;
            background: #fff;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: 2px solid #b8a0ff;
            border-color: #8965e8;
        }

        .image-preview {
            width: 150px;
            height: 130px;
            object-fit: cover;
            border-radius: 12px;
            background: #f4efff;
            display: none;
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .save-btn,
        .cancel-btn {
            padding: 12px 22px;
            border-radius: 10px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        .save-btn {
            background: #7952d9;
            color: white;
        }

        .cancel-btn {
            background: #f0eafa;
            color: #6547a5;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .pet-form {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <?php include 'header.php'; ?>

        <main class="content">
            <div class="welcome-row">
                <div>
                    <div class="eyebrow">PET MANAGEMENT</div>
                    <h1>Add New Pet 🐾</h1>
                    <p>Enter the pet's details to list them for adoption.</p>
                </div>
            </div>

            <form class="pet-form" id="addPetForm">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="petName">Pet Name *</label>
                        <input type="text" id="petName"
                            placeholder="e.g. Bruno" required>
                    </div>

                    <div class="form-group">
                        <label for="petType">Pet Type *</label>
                        <select id="petType" required>
                            <option value="">Select type</option>
                            <option>Dog</option>
                            <option>Cat</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="breed">Breed *</label>
                        <input type="text" id="breed"
                            placeholder="e.g. Labrador" required>
                    </div>

                    <div class="form-group">
                        <label for="age">Age (years) *</label>
                        <input type="number" id="age"
                            min="0" max="100" step="0.5"
                            placeholder="e.g. 2" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" required>
                            <option value="">Select gender</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="health">Health Status *</label>
                        <select id="health" required>
                            <option value="">Select status</option>
                            <option>Healthy</option>
                            <option>Vaccinated</option>
                            <option>Under Treatment</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="imageUrl">Pet Image URL *</label>
                        <input type="url" id="imageUrl"
                            placeholder="Paste a direct image link"
                            required>
                        <img id="imagePreview" class="image-preview"
                            alt="Pet image preview">
                        <small>Paste an image URL from a website that permits embedding.</small>
                    </div>

                    <div class="form-group full">
                        <label for="location">Location *</label>
                        <input type="text" id="location"
                            placeholder="e.g. Bengaluru" required>
                    </div>

                    <div class="form-group full">
                        <label for="description">Description *</label>
                        <textarea id="description" rows="4"
                            placeholder="Tell adopters about this pet..."
                            required></textarea>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">
                        + Add Pet
                    </button>
                    <a href="pets.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </main>
    </div>

    <script src="script.js"></script>

    <script>
        const imageUrl = document.getElementById('imageUrl');
        const preview = document.getElementById('imagePreview');

        imageUrl.addEventListener('input', function() {
            preview.style.display = 'none';
            preview.removeAttribute('src');

            if (this.value.trim()) {
                preview.src = this.value.trim();
            }
        });

        preview.addEventListener('load', function() {
            preview.style.display = 'block';
        });

        preview.addEventListener('error', function() {
            preview.style.display = 'none';
        });

        document.getElementById('addPetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const pet = {
                id: Date.now(),
                name: document.getElementById('petName').value.trim(),
                type: document.getElementById('petType').value,
                breed: document.getElementById('breed').value.trim(),
                age: document.getElementById('age').value,
                gender: document.getElementById('gender').value,
                health: document.getElementById('health').value,
                image: imageUrl.value.trim(),
                location: document.getElementById('location').value.trim(),
                description: document.getElementById('description').value.trim()
            };

            // Frontend-only demo storage for this browser.
            let pets = [];

            try {
                pets = JSON.parse(localStorage.getItem('pawnetPets') || '[]');
                if (!Array.isArray(pets)) pets = [];
            } catch (error) {
                pets = [];
            }

            pets.push(pet);
            localStorage.setItem('pawnetPets', JSON.stringify(pets));

            alert('Pet added successfully! 🐾');
            window.location.href = 'pets.php';
        });
    </script>

</body>

</html>