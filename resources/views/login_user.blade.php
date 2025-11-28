<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	  <meta charset="UTF-8" />
	  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	  <title>iKonek 176B Login</title>
	  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	  
	</head>
	<body>
	  <img class="header-img" src="f904be2e-8f1d-46a1-a0f8-9341af1c698f.png" alt="Header image" />

	  <div class="container">
	    <div class="logo">iKONEK<span>176B</span></div>
	    <h2>Magandang araw,<br><span>Ka-Barangay!</span></h2>

	    <form action="{{ route('login.check') }}" method="POST">
	      <div class="input-wrapper">
	        <span class="icon">📱</span>
	        <input type="tel" placeholder="+639" required>
	      </div>

	      <div class="input-wrapper">
	        <span class="lock-icon">🔒</span>
	        <input type="text" placeholder="OTP Code (Text Message)" required>
	      </div>

	      <a href="{{ route('find_account_user') }}" class="link">Lost your contact number?</a>

	      <button type="submit" class="login-btn" onclick="window.location.href='{{ route('discussion_user') }}'">LOGIN</button>

	      <div class="or-divider">OR</div>

	      <button type="button" class="register-btn" onclick="window.location.href='{{ route('registration1') }}'">REGISTER AS RESIDENT</button>
	      <button type="button" class="register-btn" onclick="window.location.href='{{ route('registration') }}'">REGISTER AS EMPLOYEE</button>
	    </form>

	    <p class="link" style="margin-top: 20px;">Continue without signing in?</p>

	    <a href="{{ route('announcement_guest') }}" class="link" style="margin-top: 20px;">Continue without signing in?</a>

	    <p class="disclaimer"><strong>Disclaimer</strong><br>This website is exclusive for Barangay 176B residents only.</p>
	  </div>
	</body>
</html>
