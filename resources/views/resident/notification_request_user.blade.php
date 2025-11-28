<x-post-layout>

        <div class="tabs">
	      <a href="resident_notification_activities	.php" class="active">ACITIVITIES</a>
	      <a href="">REQUESTS</a>
	    </div>

	    <div class="filter-bar">
	      <select>
	        <option disabled selected>NEWEST</option>
	        <option>RELEVANT</option>
	        <option>NEWEST</option>
	        <option>OLDEST</option>
	      </select>
	      <input type="text" placeholder="🔍 Search...">
	    </div>
	    <br>
	    
	    <!-- Approved Card (clickable) -->
		  <div class="request-card approved" onclick="window.location.href='resident_notification_pickup_information.php'">
		    <div class="request-number">Request Number #00235</div>
		    <h3 class="request-title">Request for Barangay Certificate</h3>
		    <div class="request-info">
		      <div class="request-time">Jun 01, 2025 &nbsp; 10:09 AM</div>
		      <div class="status approved">Approved</div>
		    </div>
		  </div>

</x-post-layout>