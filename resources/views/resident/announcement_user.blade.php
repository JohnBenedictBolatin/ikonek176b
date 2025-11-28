<x-post-layout>
    <div class="tabs">
      <a href="">ANNOUNCEMENTS</a>
      <a href="{{ route('discussion_user') }}" class="active">DISCUSSIONS</a>
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
</x-post-layout>