<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>register</title>
	</head>
	<body>
		<img class="header-img" src="f904be2e-8f1d-46a1-a0f8-9341af1c698f.png" alt="Header image" />
		<button type="button" onclick="window.location.href='resident_login.php'">Back to Login</button>

		<div class="container">
			<h1>REGISTRATION FORM</h1> <br>

			<form>
				<h3>Personal Information</h3>
				<div class="Names">
					<input type="text" name="lName" placeholder="Last Name"> 
					<input type="text" name="fName" placeholder="First Name">
					<input type="text" name="mName" placeholder="Middle Name">
					<select id="suffix" name="suffix">
				    	<option value="" disabled selected>Suffix</option>
				    	<option value="Jr.">Jr.</option>
				    	<option value="Sr.">Sr.</option>
				    	<option value="III">III</option>
				    	<option value="IV">IV</option>
				    	<option value="None">None</option>
				    </select>
				</div>
				<div class="Personal-info">
					<input type="date" id="dob" name="dob" required>
					<select id="sex" name="sex">
				    	<option value="" disabled selected>Sex</option>
				    	<option value="Jr.">Male</option>
				    	<option value="Sr.">Female</option>
				    </select>
				    <select id="civil-status" name="civil-status">
				    	<option value="" disabled selected>Civil Status</option>
				    	<option value="Jr.">Married</option>
				    	<option value="Sr.">Single</option>
				    </select>
				    <select id="role" name="role">
				    	<option value="" disabled selected>Role</option>
				    	<option value="Jr.">User</option>
				    	<option value="Sr.">Official</option>
				    </select>
				</div>

				<br>
				<h3>Contact Information</h3>
				<div class="contact-info">
					<span class="prefix">+63</span> 
					<input type="tel" name="fContact" placeholder="Primary Number" maxlength="10" required> <br>
					<span class="prefix">+63</span> 
					<input type="tel" name="sContact" placeholder="Secondary Number (Optional)" maxlength="10"> <br>
					<input type="tel" name="otp" placeholder="SENT OTP Code"> <button type="button">RESEND</button>
				</div>
				<br>
				<h3>Proof of Intent</h3>
				<div>
					<select id="package" name="package">
				    	<option value="" disabled selected>Type of Identification</option>
				    	<option value="Jr.">1</option>
				    	<option value="Sr.">6</option>
				    </select>

				    <button type="button">UPLOAD</button> <br>
				    <textarea></textarea>
				</div>


				<br>
				<button type="button" onclick="window.location.href='resident_document_request_submission.php'">Submit</button>
			</form>
		</div>
	</body>
</html>