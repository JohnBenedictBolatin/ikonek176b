# Database Reset Plan

## Overview
Clear all user-generated data while keeping:
- Database structure (all tables)
- Reference/configuration data (document types, roles, restrictions config)
- Resident database (the 20 seeded residents with offenses)
- Admin accounts (to maintain access)

## Tables to CLEAR (User-Generated Data)

### User Data
- `users` - All registered users (except admins if you want to keep them)
- `user_credentials` - User login credentials
- `user_restrictions` - User restriction records
- `user_offenses` - User offense records (from background checks)

### Posts & Social
- `posts` - All posts
- `post_comments` - All comments
- `post_reactions` - All reactions
- `post_tags` - Post tags
- `post_tag_relations` - Tag relationships
- `post_polls` - Polls
- `poll_options` - Poll options
- `poll_votes` - Poll votes
- `comment_reactions` - Comment reactions
- `post_reports` - Post reports

### Requests & Services
- `document_requests` - All document requests
- `document_request_attachments` - Document attachments
- `event_assistance_requests` - All event assistance requests
- `event_assistance_attachments` - Event attachments
- `event_assistance_details` - Event details

### Registration
- `registration_requests` - All registration requests (or keep pending ones)

### Other
- `notifications` - All notifications
- `contact_messages` - Contact messages
- `admin_actions` - Admin action logs (optional - keep for audit trail)

## Tables to KEEP (Reference/Configuration Data)

### System Configuration
- `role_categories` - Role definitions
- `document_types` - Document type definitions
- `document_type_restrictions` - Restriction rules for documents
- `event_assistance_restrictions` - Restriction rules for events
- `admin_credentials` - Admin accounts (keep to maintain access)

### Resident Database (Seeded Data)
- `residents` - The 20 seeded residents
- `resident_offenses` - Their offenses (for background checks)

### System Tables
- `sessions` - Can clear or keep
- `cache` - Can clear or keep

## Implementation Options

### Option 1: Fresh Migration (Recommended for Development)
**Pros:** Cleanest start, ensures structure is correct
**Cons:** Need to re-seed reference data

**Steps:**
1. Drop all tables: `php artisan migrate:fresh`
2. Run all migrations: `php artisan migrate`
3. Seed reference data:
   - `php artisan db:seed --class=RoleCategorySeeder` (if exists)
   - `php artisan db:seed --class=DocumentTypeSeeder` (if exists)
   - `php artisan db:seed --class=ResidentDatabaseSeeder`
   - `php artisan db:seed --class=DocumentTypeRestrictionSeeder`
   - `php artisan db:seed --class=EventAssistanceRestrictionSeeder`
4. Re-create admin account manually or via seeder

### Option 2: Selective Deletion (Keep Structure)
**Pros:** Faster, keeps admin accounts, keeps reference data
**Cons:** Need to handle foreign keys carefully

**Steps:**
1. Create a seeder/command to delete data in correct order
2. Delete in dependency order (child tables first)
3. Re-seed resident database
4. Keep admin accounts

### Option 3: Database Reset Command (Custom)
**Pros:** Reusable, can be automated
**Cons:** Need to create custom command

**Steps:**
1. Create artisan command: `php artisan make:command ResetUserData`
2. Implement deletion logic
3. Run: `php artisan db:reset-user-data`

## Recommended Approach: Option 2 (Selective Deletion)

This keeps your structure and reference data intact while clearing user data.

## Next Steps After Reset

1. **Register the 20 users** from the resident database:
   - Use the registration request system
   - System will automatically match them against resident database
   - Background checks will apply restrictions automatically

2. **Verify:**
   - Admin can still login
   - Document types are available
   - Event types are available
   - Resident database is intact
   - Restrictions are configured

## Important Notes

⚠️ **Backup First!** Always backup your database before resetting:
```bash
php artisan db:backup  # If you have backup package
# OR manually export via phpMyAdmin/MySQL
```

⚠️ **Foreign Key Constraints:** Delete in order to avoid constraint violations:
1. Child tables first (comments, reactions, attachments)
2. Parent tables last (users, posts, requests)

⚠️ **Admin Accounts:** Decide if you want to keep admin accounts or reset them too.

