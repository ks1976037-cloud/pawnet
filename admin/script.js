document.addEventListener("DOMContentLoaded", function () {
  window.demoMessage = function (message) {
    alert(message);
  };

  const locationButton = document.getElementById("locationButton");
  const locationDropdown = document.getElementById("locationDropdown");
  const locationText = document.getElementById("locationText");
  const locateMeButton = document.getElementById("locateMe");
  const locationError = document.getElementById("locationError");
  const openGoogleMaps = document.getElementById("openGoogleMaps");

  const notificationButton = document.getElementById("notificationButton");
  const notificationDropdown = document.getElementById("notificationDropdown");
  const notificationList = document.getElementById("notificationList");
  const notificationCount = document.getElementById("notificationCount");
  const notificationDot = document.getElementById("notificationDot");
  const clearNotificationsButton =
    document.getElementById("clearNotifications");

  function closeLocationDropdown() {
    if (locationDropdown) {
      locationDropdown.classList.remove("show");
    }

    if (locationButton) {
      locationButton.setAttribute("aria-expanded", "false");
    }
  }

  function closeNotificationDropdown() {
    if (notificationDropdown) {
      notificationDropdown.classList.remove("show");
    }

    if (notificationButton) {
      notificationButton.setAttribute("aria-expanded", "false");
    }
  }

  if (locationButton && locationDropdown) {
    locationButton.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const willOpen = !locationDropdown.classList.contains("show");

      // Close notifications before opening location.
      closeNotificationDropdown();

      locationDropdown.classList.toggle("show", willOpen);

      locationButton.setAttribute("aria-expanded", String(willOpen));
    });
  }

  if (locateMeButton) {
    locateMeButton.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      if (locationError) {
        locationError.textContent = "";
      }

      if (locationText) {
        locationText.textContent = "Finding your location...";
      }

      if (openGoogleMaps) {
        openGoogleMaps.style.display = "none";
        openGoogleMaps.removeAttribute("href");
      }

      if (!navigator.geolocation) {
        if (locationText) {
          locationText.textContent =
            "Location is not supported by this browser.";
        }

        return;
      }

      navigator.geolocation.getCurrentPosition(
        function (position) {
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;

          if (locationText) {
            locationText.textContent =
              "Latitude: " +
              latitude.toFixed(5) +
              "\nLongitude: " +
              longitude.toFixed(5);
          }

          if (openGoogleMaps) {
            openGoogleMaps.href =
              "https://www.google.com/maps?q=" + latitude + "," + longitude;

            openGoogleMaps.style.display = "inline-block";
          }

          if (locationError) {
            locationError.textContent = "";
          }
        },

        function (error) {
          let message = "Unable to get your location.";

          if (error.code === 1) {
            message =
              "Location permission denied. Allow access in your browser settings.";
          } else if (error.code === 2) {
            message =
              "Your location is unavailable. Check your device location settings.";
          } else if (error.code === 3) {
            message = "Location request timed out. Please try again.";
          }

          if (locationText) {
            locationText.textContent = "Location unavailable.";
          }

          if (locationError) {
            locationError.textContent = message;
          }
        },

        {
          enableHighAccuracy: false,
          timeout: 15000,
          maximumAge: 0,
        },
      );
    });
  }

  let readNotificationIds = [];

  try {
    const savedIds = JSON.parse(
      localStorage.getItem("pawnetReadNotifications") || "[]",
    );

    if (Array.isArray(savedIds)) {
      readNotificationIds = savedIds.map(String);
    }
  } catch (error) {
    readNotificationIds = [];
  }

  function saveReadNotifications() {
    try {
      localStorage.setItem(
        "pawnetReadNotifications",
        JSON.stringify(readNotificationIds),
      );
    } catch (error) {
      console.warn("Could not save notification status.");
    }
  }

  function updateNotifications() {
    if (!notificationList) {
      return;
    }

    const notices = notificationList.querySelectorAll("[data-notice-id]");

    let unreadCount = 0;

    notices.forEach(function (notice) {
      const id = notice.dataset.noticeId;
      const isRead = readNotificationIds.includes(id);

      notice.classList.toggle("read", isRead);

      if (!isRead) {
        unreadCount++;
      }
    });

    if (notificationCount) {
      notificationCount.textContent = unreadCount + " new";

      notificationCount.style.display = unreadCount > 0 ? "" : "none";
    }

    if (notificationDot) {
      notificationDot.style.display = unreadCount > 0 ? "" : "none";
    }

    if (clearNotificationsButton) {
      clearNotificationsButton.textContent =
        unreadCount > 0 ? "Mark all as read" : "All caught up ✓";

      clearNotificationsButton.disabled = unreadCount === 0;
    }
  }

  /* ==========================================
       7. OPEN AND CLOSE NOTIFICATIONS
       ========================================== */

  if (notificationButton && notificationDropdown) {
    notificationButton.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const willOpen = !notificationDropdown.classList.contains("show");

      closeLocationDropdown();

      notificationDropdown.classList.toggle("show", willOpen);

      notificationButton.setAttribute("aria-expanded", String(willOpen));
    });
  }

  if (notificationList) {
    notificationList.addEventListener("click", function (event) {
      const notice = event.target.closest("[data-notice-id]");

      if (!notice) {
        return;
      }

      const id = notice.dataset.noticeId;

      if (!readNotificationIds.includes(id)) {
        readNotificationIds.push(id);
        saveReadNotifications();
      }

      updateNotifications();

      // The notification link can still open its target page.
    });
  }

  if (clearNotificationsButton && notificationList) {
    clearNotificationsButton.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      notificationList
        .querySelectorAll("[data-notice-id]")
        .forEach(function (notice) {
          const id = notice.dataset.noticeId;

          if (!readNotificationIds.includes(id)) {
            readNotificationIds.push(id);
          }
        });

      saveReadNotifications();
      updateNotifications();
    });
  }

  window.addPawNetNotification = function (id, title, message, url) {
    if (!notificationList || !id) {
      return;
    }

    id = String(id);

    const existing = Array.from(
      notificationList.querySelectorAll("[data-notice-id]"),
    ).some(function (notice) {
      return notice.dataset.noticeId === id;
    });

    if (existing) {
      return;
    }

    const link = document.createElement("a");
    link.className = "notice";
    link.href = url || "index.php";
    link.dataset.noticeId = id;

    const icon = document.createElement("span");
    icon.textContent = "🔔";

    const content = document.createElement("p");

    const heading = document.createElement("strong");
    heading.textContent = title;

    const description = document.createElement("small");
    description.textContent = message;

    const unreadDot = document.createElement("i");
    unreadDot.className = "notice-unread";

    content.appendChild(heading);
    content.appendChild(description);

    link.appendChild(icon);
    link.appendChild(content);
    link.appendChild(unreadDot);

    notificationList.prepend(link);

    readNotificationIds = readNotificationIds.filter(function (item) {
      return item !== id;
    });

    saveReadNotifications();
    updateNotifications();
  };

  updateNotifications();

  document.addEventListener("click", function (event) {
    if (event.target.closest(".dropdown-wrap")) {
      return;
    }

    closeLocationDropdown();
    closeNotificationDropdown();
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeLocationDropdown();
      closeNotificationDropdown();
    }
  });

  const globalSearch = document.getElementById("globalSearch");
  const globalSearchResults = document.getElementById("globalSearchResults");

  const searchablePages = [
    {
      name: "Dashboard",
      keywords: "home overview statistics",
      url: "index.php",
    },
    {
      name: "Manage Pets",
      keywords: "dogs cats animals pet listings",
      url: "pets.php",
    },
    {
      name: "Add Pet",
      keywords: "new pet create listing upload image",
      url: "add_pet.php",
    },
    {
      name: "Applications",
      keywords: "adoption requests pending approved",
      url: "applications.php",
    },
    {
      name: "Adoption Records",
      keywords: "adopted pets adoption history records",
      url: "adoptions.php",
    },
    {
      name: "Manage Users",
      keywords: "users members accounts adopters",
      url: "users.php",
    },
  ];

  function hideSearchResults() {
    if (globalSearchResults) {
      globalSearchResults.classList.remove("show");
      globalSearchResults.innerHTML = "";
    }
  }

  if (globalSearch && globalSearchResults) {
    globalSearch.addEventListener("input", function () {
      const query = globalSearch.value.trim().toLowerCase();

      globalSearchResults.innerHTML = "";

      if (!query) {
        hideSearchResults();
        return;
      }

      const matches = searchablePages.filter(function (page) {
        return (
          page.name.toLowerCase().includes(query) ||
          page.keywords.toLowerCase().includes(query)
        );
      });

      if (matches.length === 0) {
        const empty = document.createElement("div");
        empty.className = "search-result-empty";
        empty.textContent = "No matching pages found.";

        globalSearchResults.appendChild(empty);
      } else {
        matches.forEach(function (page) {
          const link = document.createElement("a");

          link.href = page.url;
          link.className = "search-result-item";
          link.textContent = page.name;

          globalSearchResults.appendChild(link);
        });
      }

      globalSearchResults.classList.add("show");
    });

    globalSearch.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        hideSearchResults();
        globalSearch.blur();
      }

      if (event.key === "Enter") {
        const firstResult = globalSearchResults.querySelector("a");

        if (globalSearchResults.classList.contains("show") && firstResult) {
          window.location.href = firstResult.href;
        }
      }
    });

    document.addEventListener("click", function (event) {
      if (!event.target.closest(".global-search-wrap")) {
        hideSearchResults();
      }
    });
  }

  if (!document.getElementById("pawnetScriptStyles")) {
    const style = document.createElement("style");

    style.id = "pawnetScriptStyles";

    style.textContent = `
            .dropdown-wrap {
                position: relative;
            }

            .location-dropdown,
            .notification-dropdown {
                display: none;
                position: absolute;
                top: calc(100% + 10px);
                right: 0;
                width: 320px;
                max-width: calc(100vw - 40px);
                box-sizing: border-box;
                padding: 16px;
                background: #fff;
                color: #333;
                border-radius: 14px;
                box-shadow: 0 8px 30px rgba(40, 20, 70, 0.15);
                z-index: 99999;
            }

            .location-dropdown.show,
            .notification-dropdown.show {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .location-dropdown p {
                white-space: pre-line;
                overflow-wrap: anywhere;
            }

            #locationError {
                color: #c0392b;
                font-size: 12px;
            }

            .notification-heading {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding-bottom: 12px;
            }

            #notificationCount {
                color: #7952d9;
                background: #f0eaff;
                padding: 4px 9px;
                border-radius: 20px;
                font-size: 12px;
            }

            .notification-dropdown .notice {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                padding: 13px 8px;
                border-bottom: 1px solid #f0ebf7;
                text-decoration: none;
                color: inherit;
            }

            .notification-dropdown .notice:hover {
                background: #f8f5ff;
            }

            .notification-dropdown .notice p {
                flex: 1;
                margin: 0;
            }

            .notification-dropdown .notice small {
                display: block;
                margin-top: 5px;
                color: #8b819a;
            }

            .notice-unread {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #7952d9;
                flex-shrink: 0;
                margin-top: 5px;
            }

            .notice.read .notice-unread {
                visibility: hidden;
            }

            .notice.read {
                opacity: 0.7;
            }

            #clearNotifications {
                width: 100%;
                margin-top: 12px;
                cursor: pointer;
            }

            #clearNotifications:disabled {
                opacity: 0.6;
                cursor: default;
            }

            .global-search-wrap {
                position: relative;
            }

            .global-search-results {
                display: none;
                position: absolute;
                top: calc(100% + 8px);
                left: 0;
                width: 100%;
                min-width: 240px;
                max-height: 300px;
                overflow-y: auto;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(40, 20, 70, 0.15);
                z-index: 99998;
            }

            .global-search-results.show {
                display: block;
            }

            .search-result-item,
            .search-result-empty {
                display: block;
                padding: 12px 15px;
                color: #333;
                text-decoration: none;
                border-bottom: 1px solid #f0ebf7;
            }

            .search-result-item:hover {
                background: #f5f0ff;
            }

            .search-result-empty {
                color: #8b819a;
            }
        `;

    document.head.appendChild(style);
  }
});
