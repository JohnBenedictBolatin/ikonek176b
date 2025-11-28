<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>iKonek176B discussion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
  </head>
  <body>

    <div class="header">
      <span>📱</span> iKONEK176B
    </div>

    <!-- Three-dot menu -->
    <div class="menu-container">
      <div class="dots" onclick="toggleMenu()">⋮</div>
      <div class="dropdown" id="menuDropdown">
        <a href="resident_help_center.php">Help Center</a>
        <a href="resident_term_condition.php">Terms & Conditions</a>
        <button type="button" class="register-btn" onclick="window.location.href='{{ route('login') }}'">LOG OUT</button>
      </div>
    </div>


    <!-- js for the menu -->
    <script>
      function toggleMenu() {
        const menu = document.getElementById("menuDropdown");
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
      }

      // Optional: click outside to close
      window.onclick = function(e) {
        if (!e.target.matches('.dots')) {
          const menu = document.getElementById("menuDropdown");
          if (menu.style.display === "block") {
            menu.style.display = "none";
          }
        }
      }
    </script>


    <div class="navbar">
      <a href="{{ route('announcement_user') }}">📱Post</a>
      <a href="{{ route('document_request_select_user') }}">Document</a>
      <a href="{{ route('notification_activities_user') }}">Notification</a>
      <a href="{{ route('profile_user') }}">Profile</a>
    </div>

    <main class="container">
      {{ $slot }}
    </main>
    
  </body>
</html>
