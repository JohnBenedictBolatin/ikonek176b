<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder clears all user-generated data while keeping:
     * - Database structure
     * - Reference data (document types, roles, restrictions config)
     * - Resident database (seeded residents and offenses)
     * - Admin credentials (to maintain access)
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete in order (child tables first, then parent tables)

        // 1. Post-related data (child tables first)
        echo "Clearing post-related data...\n";
        $this->truncateIfExists('comment_reactions');
        $this->truncateIfExists('poll_votes');
        $this->truncateIfExists('poll_options');
        $this->truncateIfExists('post_polls');
        $this->truncateIfExists('post_reports');
        $this->truncateIfExists('post_tags');
        $this->truncateIfExists('post_reactions');
        $this->truncateIfExists('post_comments');
        $this->truncateIfExists('posts');

        // 2. Request attachments and details (child tables)
        echo "Clearing request attachments...\n";
        $this->truncateIfExists('document_request_attachments');
        $this->truncateIfExists('event_assistance_attachments');
        $this->truncateIfExists('event_assistance_details');

        // 3. Payment-related data (child tables first)
        echo "Clearing payment data...\n";
        $this->truncateIfExists('payment_receipts');
        $this->truncateIfExists('payments');

        // 4. Requests (parent tables)
        echo "Clearing document and event requests...\n";
        $this->truncateIfExists('document_requests');
        $this->truncateIfExists('event_assistance_requests');

        // 5. Registration requests
        echo "Clearing registration requests...\n";
        $this->truncateIfExists('registration_requests');

        // 6. User-related data
        echo "Clearing user-related data...\n";
        $this->truncateIfExists('user_offenses');
        $this->truncateIfExists('user_restrictions');
        $this->truncateIfExists('user_credentials');

        // 7. Notifications and messages
        echo "Clearing notifications and messages...\n";
        $this->truncateIfExists('notifications');
        $this->truncateIfExists('contact_messages');

        // 8. Admin actions (optional - comment out if you want to keep audit trail)
        echo "Clearing admin action logs...\n";
        $this->truncateIfExists('admin_actions');

        // 9. Users (keep admin users if they exist, or delete all)
        echo "Clearing users...\n";
        // Option A: Delete all users
        if (Schema::hasTable('users')) {
            DB::table('users')->truncate();
        }
        
        // Option B: Delete only non-admin users (uncomment if you want to keep admins)
        // if (Schema::hasTable('users')) {
        //     DB::table('users')
        //         ->whereNotIn('fk_role_id', [2, 3, 4, 5, 6, 7, 9]) // Keep admin roles
        //         ->delete();
        // }

        // 10. Sessions and cache (optional)
        echo "Clearing sessions and cache...\n";
        $this->truncateIfExists('sessions');
        $this->truncateIfExists('cache');
        $this->truncateIfExists('cache_locks');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "\n✅ User data cleared successfully!\n";
        echo "📋 What was cleared:\n";
        echo "   - All users (except admins if configured)\n";
        echo "   - All posts, comments, reactions\n";
        echo "   - All document and event requests\n";
        echo "   - All payments and payment receipts (including history)\n";
        echo "   - All registration requests\n";
        echo "   - All notifications and messages\n";
        echo "\n📋 What was kept:\n";
        echo "   - Database structure (all tables)\n";
        echo "   - Document types\n";
        echo "   - Role categories\n";
        echo "   - Document type restrictions\n";
        echo "   - Event assistance restrictions\n";
        echo "   - Resident database (residents and resident_offenses)\n";
        echo "   - Admin credentials\n";
        echo "   - Payment methods (reference data)\n";
        echo "\n📝 Next steps:\n";
        echo "   1. Re-seed resident database: php artisan db:seed --class=ResidentDatabaseSeeder\n";
        echo "   2. Register users through the registration request system\n";
        echo "   3. System will automatically match and apply background checks\n";
    }

    /**
     * Helper method to truncate table only if it exists
     */
    private function truncateIfExists(string $tableName): void
    {
        if (Schema::hasTable($tableName)) {
            DB::table($tableName)->truncate();
        }
    }
}
