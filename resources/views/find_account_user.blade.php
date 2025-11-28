<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Find account page</title>
	</head>
	<body>
		<img class="header-img" src="f904be2e-8f1d-46a1-a0f8-9341af1c698f.png" alt="Header image" />
		<button type="button" onclick="window.location.href='{{ route('login') }}'">Back to Login</button>

		<div class="container">
			<h1>FIND ACCOUNT</h1>
			<br>
			<h3>Account Details</h3>
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
		</div>
		<br>
		<div>
			<h3>PROOF OF OWNERSHIP</h3>
			<div>
				<select id="package" name="package">
			    	<option value="" disabled selected>Type of Identification</option>
			    	<option value="Jr.">1</option>
			    	<option value="Sr.">6</option>
			    </select>

			    <button type="button">UPLOAD</button>
			</div>			
		</div>
		<br>
		<h3>REGISTER NEW CONTACT NUMBER</h3>
		<div>
			<span class="prefix">+63</span> 
			<input type="tel" name="fContact" placeholder="Primary Number" maxlength="10" required> <br>
			<span class="prefix">+63</span> 
			<input type="tel" name="sContact" placeholder="Secondary Number (Optional)" maxlength="10"> <br>
		</div>

		<br>
		<button type="button">CONFIRM</button>
	</body>
</html>