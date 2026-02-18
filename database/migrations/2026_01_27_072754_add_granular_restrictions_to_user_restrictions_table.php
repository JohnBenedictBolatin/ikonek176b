<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('user_restrictions')) {
            return;
        }
        $addRestrictedDoc = !Schema::hasColumn('user_restrictions', 'restricted_document_types');
        $addAllowedDoc = !Schema::hasColumn('user_restrictions', 'allowed_document_types');
        $addRestrictedEvent = !Schema::hasColumn('user_restrictions', 'restricted_event_types');
        $addAllowedEvent = !Schema::hasColumn('user_restrictions', 'allowed_event_types');
        $addReason = !Schema::hasColumn('user_restrictions', 'restriction_reason');
        if (!$addRestrictedDoc && !$addAllowedDoc && !$addRestrictedEvent && !$addAllowedEvent && !$addReason) {
            return;
        }
        Schema::table('user_restrictions', function (Blueprint $table) use ($addRestrictedDoc, $addAllowedDoc, $addRestrictedEvent, $addAllowedEvent, $addReason) {
            if ($addRestrictedDoc) $table->json('restricted_document_types')->nullable();
            if ($addAllowedDoc) $table->json('allowed_document_types')->nullable();
            if ($addRestrictedEvent) $table->json('restricted_event_types')->nullable();
            if ($addAllowedEvent) $table->json('allowed_event_types')->nullable();
            if ($addReason) $table->text('restriction_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('user_restrictions')) {
            return;
        }
        Schema::table('user_restrictions', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('user_restrictions', 'restricted_document_types')) $cols[] = 'restricted_document_types';
            if (Schema::hasColumn('user_restrictions', 'allowed_document_types')) $cols[] = 'allowed_document_types';
            if (Schema::hasColumn('user_restrictions', 'restricted_event_types')) $cols[] = 'restricted_event_types';
            if (Schema::hasColumn('user_restrictions', 'allowed_event_types')) $cols[] = 'allowed_event_types';
            if (Schema::hasColumn('user_restrictions', 'restriction_reason')) $cols[] = 'restriction_reason';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
