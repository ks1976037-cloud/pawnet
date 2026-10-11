<aside class="sidebar">
  <a class="brand" href="index.php"><span class="brand-mark">🐾</span><span><b>Paw Net</b><small>Pet Adoption</small></span></a>
  <div class="nav-label">MAIN MENU</div>
  <nav>
    <a class="<?= $page === 'overview' ? 'active' : '' ?>" href="index.php"><span>▦</span> Overview</a>
    <a class="<?= $page === 'pets' ? 'active' : '' ?>" href="pets.php"><span>🐕</span> Manage Pets</a>
    <a class="<?= $page === 'applications' ? 'active' : '' ?>" href="applications.php"><span>▤</span> Applications <i class="nav-badge">3</i></a>
    <a class="<?= $page === 'adoptions' ? 'active' : '' ?>" href="adoptions.php"><span>⌂</span> Adoption Records</a>
    <a class="<?= $page === 'users' ? 'active' : '' ?>" href="users.php"><span>♙</span> Manage Users</a>
  </nav>
  <div class="nav-label account-label">ACCOUNT</div>
  <nav><a href="admin-profile.php"
      class="<?= ($page ?? '') === 'profile' ? 'active' : '' ?>">
      ◎ <span>Profile</span>
    </a><a href="messages.php"
      class="<?= ($page ?? '') === 'messages' ? 'active' : '' ?>">
      <span class="menu-icon">✉</span>
      <span>Messages</span>
    </a>

    <a href="settings.php"
      class="<?= ($page ?? '') === 'settings' ? 'active' : '' ?>">
      <span class="menu-icon">⚙</span>
      <span>Settings</span>
    </a>
  </nav>
</aside>