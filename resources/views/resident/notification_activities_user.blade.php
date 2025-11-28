<x-post-layout>

	    <div class="tabs">
	      <a href="">ACITIVITIES</a>
	      <a href="{{ route('notification_request_user') }}" class="active">REQUESTS</a>
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
	    <div class="post">
	      
	    </div>

</x-post-layout>