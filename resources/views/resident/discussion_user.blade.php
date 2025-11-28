<x-post-layout>
    
    <div class="tabs">
      <a href="{{ route('announcement_user') }}" class="active">ANNOUNCEMENTS</a>
      <a href="">DISCUSSIONS</a>
    </div>

    <div class="filter-bar">
      <select>
        <option disabled selected>NEWEST</option>
        <option>RELEVANT</option>
        <option>NEWEST</option>
        <option>OLDEST</option>
      </select>
      <select>
        <option disabled selected>ALL</option>
        <option>RELEVANT</option>
        <option>NEWEST</option>
        <option>OLDEST</option>
      </select>
      <input type="text" placeholder="🔍 Search...">
    </div>
    <br>
    <div class="post">
      
    </div>
    <br>
    <div>
      <button type="button" onclick="window.location.href='{{ route('discussion_add_post_user') }}'">
        ADD POST
      </button>
    </div>

</x-post-layout>