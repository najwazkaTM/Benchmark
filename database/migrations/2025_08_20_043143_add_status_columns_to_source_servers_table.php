<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('source_servers', function (Blueprint $table) {
            if (!Schema::hasColumn('source_servers', 'ssh_status')) {
                $table->string('ssh_status', 20)->nullable()->after('ssh_password'); // 'connected' | 'failed' | null
            }
            if (!Schema::hasColumn('source_servers', 'last_checked_at')) {
                $table->timestamp('last_checked_at')->nullable()->after('ssh_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('source_servers', function (Blueprint $table) {
            if (Schema::hasColumn('source_servers', 'ssh_status')) {
                $table->dropColumn('ssh_status');
            }
            if (Schema::hasColumn('source_servers', 'last_checked_at')) {
                $table->dropColumn('last_checked_at');
            }
        });
    }
};
