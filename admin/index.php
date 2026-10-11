<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth_helpers.php';

start_auth_session();
$page = 'overview';
$title = 'Admin Dashboard';
$pets = 20;
$applications = 8;
$adoptions = 5;
$users = 32;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> | Paw Net</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php include 'sidebar.php'; ?>
  <div class="main">
    <?php include 'header.php'; ?>
    <main class="content">
      <div class="welcome-row">
        <div>
          <div class="eyebrow">ADMIN DASHBOARD</div>
          <h1>Welcome back, Admin! 👋</h1>
          <p>Here's what's happening with Paw Net today.</p>
        </div>
        <a class="primary-btn" href="pets.php">＋ Manage Pets</a>
      </div>
      <section class="stats">
        <article class="stat-card">
          <div class="stat-icon lilac">🐾</div>
          <div><span>Total Pets</span><strong id="petCount"><?= $pets ?></strong><small>Pets listed for adoption</small></div>
        </article>
        <article class="stat-card">
          <div class="stat-icon peach">▤</div>
          <div><span>Applications</span><strong id="appCount"><?= $applications ?></strong><small>Adoption requests received</small></div>
        </article>
        <article class="stat-card">
          <div class="stat-icon mint">✓</div>
          <div><span>Adoptions</span><strong><?= $adoptions ?></strong><small>Successful matches</small></div>
        </article>
        <article class="stat-card">
          <div class="stat-icon blue">♙</div>
          <div><span>Registered Users</span><strong><?= $users ?></strong><small>Adopter accounts</small></div>
        </article>
      </section>
      <section class="panel-grid">
        <article class="panel recent-panel">
          <div class="panel-heading">
            <div>
              <h2>Recent Applications</h2>
              <p>Latest adoption requests</p>
            </div><a href="applications.php">View all →</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Pet</th>
                  <th>Adopter</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="recentRows">
                <tr>
                  <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                        class="pet-image">
                      Bruno</span><small>Labrador</small></td>
                  <td>Aarav Sharma</td>
                  <td>Oct 8, 2026</td>
                  <td><span class="status review">Under Review</span></td>
                </tr>
                <tr>
                  <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                        class="pet-image">
                      Bruno</span><small>Cat</small></td>
                  <td>Diya Patel</td>
                  <td>Oct 7, 2026</td>
                  <td><span class="status approved">Approved</span></td>
                </tr>
                <tr>
                  <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                        class="pet-image">
                      Bruno</span><small>Indie</small></td>
                  <td>Rohan Das</td>
                  <td>Oct 6, 2026</td>
                  <td><span class="status pending">Pending</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>
        <article class="panel quick-panel">
          <div class="panel-heading">
            <div>
              <h2>Quick Actions</h2>
              <p>Manage your adoption platform</p>
            </div>
          </div>
          <a class="action-card" href="pets.php"><span class="action-icon lilac">🐾</span><span><b>Manage pets</b><small>Add or update pet listings</small></span><span class="arrow">→</span></a>
          <a class="action-card" href="applications.php"><span class="action-icon peach">▤</span><span><b>Review applications</b><small>Check adopter requests</small></span><span class="arrow">→</span></a>
          <div class="tip-card">
            <div class="eyebrow">PAW NET TIP</div>
            <h3>Every pet deserves a home.</h3>
            <p>Keep pet profiles and application statuses up to date.</p>
          </div>
        </article>
      </section>
    </main>
  </div>
  <script src="script.js"></script>
</body>

</html>