# Database Reset Instructions

## Quick Start

### Step 1: Backup Your Database (IMPORTANT!)
```bash
# Option A: Using Laravel backup (if installed)
php artisan backup:run

# Option B: Manual MySQL backup
mysqldump -u your_username -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Clear All User Data
```bash
php artisan db:seed --class=ClearUserDataSeeder
```

This will:
- ✅ Clear all users, posts, comments, requests, notifications
- ✅ Keep database structure intact
- ✅ Keep reference data (document types, roles, restrictions)
- ✅ Keep resident database (the 20 seeded residents)
- ✅ Keep admin credentials (so you can still login)

### Step 3: Re-seed Resident Database (if needed)
```bash
# Only if you also cleared the residents table
php artisan db:seed --class=ResidentDatabaseSeeder
php artisan db:seed --class=DocumentTypeRestrictionSeeder
php artisan db:seed --class=EventAssistanceRestrictionSeeder
```

### Step 4: Register the 20 Users

Now you can register the 20 users from the resident database:

1. **Go to Registration Requests page** in admin panel
2. **Create registration requests** for each of the 20 users:
   - Use their exact names and birthdates from the resident database
   - System will automatically match them during approval
   - Background checks will apply restrictions automatically

3. **Or use bulk import** (if you create a seeder/command for it)

## What Gets Cleared

### User Data
- ✅ All users (or non-admin users if you modify the seeder)
- ✅ User credentials
- ✅ User restrictions
- ✅ User offenses

### Posts & Social
- ✅ All posts
- ✅ All comments
- ✅ All reactions
- ✅ All polls and votes
- ✅ All reports

### Requests
- ✅ All document requests
- ✅ All event assistance requests
- ✅ All registration requests

### Other
- ✅ All notifications
- ✅ All contact messages
- ✅ Admin action logs (optional)

## What Gets Kept

### Structure
- ✅ All database tables
- ✅ All migrations remain applied

### Reference Data
- ✅ Document types
- ✅ Role categories
- ✅ Document type restrictions
- ✅ Event assistance restrictions

### Resident Database
- ✅ All 20 residents
- ✅ All resident offenses (for background checks)

### Admin Access
- ✅ Admin credentials (you can still login)

## Alternative: Fresh Migration

If you want a completely fresh start:

```bash
# WARNING: This will drop ALL tables and data
php artisan migrate:fresh

# Then run all seeders
php artisan db:seed --class=ResidentDatabaseSeeder
php artisan db:seed --class=DocumentTypeRestrictionSeeder
php artisan db:seed --class=EventAssistanceRestrictionSeeder

# Re-create admin account manually
```

## Troubleshooting

### Foreign Key Errors
If you get foreign key constraint errors, the seeder should handle this with `SET FOREIGN_KEY_CHECKS=0`, but if issues persist:

1. Check the order of deletions in `ClearUserDataSeeder.php`
2. Make sure child tables are deleted before parent tables

### Admin Account Lost
If you accidentally deleted admin accounts:

1. Create a new admin via registration request
2. Or manually insert into `admin_credentials` table
3. Or create a seeder for admin accounts

### Resident Database Missing
If residents were cleared:

```bash
php artisan db:seed --class=ResidentDatabaseSeeder
```

## Verification Checklist

After reset, verify:

- [ ] Can login as admin
- [ ] Document types are visible
- [ ] Event types are available
- [ ] Resident database has 20 residents
- [ ] Resident offenses are present
- [ ] Restrictions are configured
- [ ] Can create registration requests
- [ ] Background check works on approval

