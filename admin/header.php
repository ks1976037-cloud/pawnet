<header class="topbar">

    <div class="global-search-wrap">
        <label class="search">
            <span>⌕</span>
            <input
                id="globalSearch"
                type="search"
                placeholder="Search pets, applications, users..."
                aria-label="Search dashboard"
                autocomplete="off">
        </label>

        <div id="globalSearchResults" class="global-search-results"></div>
    </div>

    <div class="top-actions">

        <div class="dropdown-wrap">
            <button
                class="icon-btn"
                id="locationButton"
                aria-label="Location"
                title="Location"
                aria-expanded="false">♢</button>

            <div class="dropdown location-dropdown" id="locationDropdown">
                <b>📍 Your Location</b>
                <p id="locationText">Location not shared</p>

                <button class="small-btn" id="locateMe" type="button">
                    Use my location
                </button>

                <a
                    href="#"
                    id="openGoogleMaps"
                    target="_blank"
                    rel="noopener noreferrer"
                    style="display:none; margin-top:12px;">Open in Google Maps ↗</a>

                <p id="locationError" role="status"></p>
            </div>
        </div>

        <div class="dropdown-wrap">
            <button
                class="icon-btn"
                id="notificationButton"
                aria-label="Notifications"
                title="Notifications"
                aria-expanded="false">
                ♧
                <i class="dot" id="notificationDot"></i>
            </button>

            <div class="dropdown notification-dropdown" id="notificationDropdown">
                <div class="notification-heading">
                    <b>Notifications</b>
                    <span id="notificationCount">2 new</span>
                </div>

                <div id="notificationList">
                    <a href="applications.php" class="notice" data-notice-id="application-1">
                        <span>📝</span>
                        <p>
                            <strong>New application</strong>
                            <small>Bruno has an adoption request.</small>
                        </p>
                        <i class="notice-unread"></i>
                    </a>

                    <a href="pets.php" class="notice" data-notice-id="pets-1">
                        <span>🐾</span>
                        <p>
                            <strong>Pet listing reminder</strong>
                            <small>Check your available pet profiles.</small>
                        </p>
                        <i class="notice-unread"></i>
                    </a>
                </div>

                <button class="small-btn" id="clearNotifications" type="button">
                    Mark all as read
                </button>
            </div>
        </div>

        <button
            class="profile-chip"
            onclick="demoMessage('Admin profile is a frontend demo.')"
            type="button">
            <span class="avatar">A</span>
            <span><b>Admin</b><small>Administrator</small></span>
            <span class="chevron">⌄</span>
        </button>


        <!-- Sign Out -->
        <form action="../logout.php" method="post" class="topbar-logout">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    auth_csrf_token(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <button type="submit" class="signout-btn">
                <span aria-hidden="true">↪</span>
                <span>Sign Out</span>
            </button>
        </form>

    </div>
</header>