<script setup>
  import { Link, usePage } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref, computed } from 'vue';
  import { router } from '@inertiajs/vue3'
  // import { route } from 'ZiggyVue'
  import  LayoutResident  from '@/Layouts/LayoutResident.vue';

  // --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// map of role_id -> role_name (based on the table you provided)
const roleMap = {
  1: 'Resident',
  2: 'Barangay Captain',
  3: 'Barangay Secretary',
  4: 'Barangay Treasurer',
  5: 'Barangay Kagawad',
  6: 'Sangguniang Kabataan Chairman',
  7: 'Sangguniang Kabataan Kagawad',
  9: 'System Admin',
}

// computed display role (safe if user is null)
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  const role = id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
  return role.toUpperCase()
})
</script>


<template>
    <LayoutResident>
        <Head>
            <title>announce click post</title>
        </Head>

        <div class="tabs">
            <Link href="#" class="active">ANNOUNCEMENTS</Link>
            <Link :href="route('discussion_resident')">DISCUSSIONS</Link>
        </div>

        <Link :href="route('announcement_resident')">
          BACK TO POST
        </Link>

        <div class="bg-white p-4 rounded-lg shadow-md">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h2 class="font-semibold text-lg">Kap. Sammy Reyes</h2>
                    <p class="text-gray-500 text-sm">June 25, 2025</p>
                </div>
            </div>

            <p class="text-gray-700 mb-4">
            Every week, expect a clean-up drive (house to house).
            <br>
            <br>
            We would like to thank all our residents, especially Barangay Kagawad, for being involved and engaged in the community clean-up activity. Salamat sa pag sabay-sabay nating pagtulong!
            </p>

            <div class="grid grid-cols-3 gap-2 mb-4">
                <img src="/images/cleanup1.jpg" class="rounded-md w-full h-24 object-cover" />
                <img src="/images/cleanup2.jpg" class="rounded-md w-full h-24 object-cover" />
                <img src="/images/cleanup3.jpg" class="rounded-md w-full h-24 object-cover" />
            </div>

            <div class="flex gap-4 items-center text-sm text-gray-600 border-t pt-2">
                <span>🔍 120</span>
                <span>💬 23</span>
            </div>

            <div class="mt-4">
                <button class="bg-purple-500 text-white px-3 py-1 rounded-full text-xs mr-2">#CLEANLINESS</button>
                <button class="bg-green-500 text-white px-3 py-1 rounded-full text-xs">POST COMMENT</button>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-sm text-gray-700 mb-2">COMMENTS</h3>
            <div class="bg-white p-3 rounded-lg border">
                <input
                    type="text"
                    placeholder="Add a comment..."
                    class="w-full border border-gray-300 p-2 rounded-md text-sm"
                />
            </div>

            <div class="bg-green-100 p-4 rounded-lg mt-4">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold text-sm">John Rey Servilleg</span>
                    <span class="text-xs text-gray-500">June 27, 2025</span>
                </div>
                <p class="text-sm text-gray-800">
                    Malaking tulong talaga ito sa ating lahat, especially sa pag hulog ng basura. Ang pagsasama at disiplina ay susi ng magandang barangay!
                </p>
                <div class="text-xs text-gray-600 mt-2 flex gap-4">
                    <span>👍 76</span>
                    <span>👎 2</span>
                </div>
            </div>
        </div>
    </LayoutResident>
</template>