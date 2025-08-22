<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('source_servers', function (Blueprint $table) {
            // unsignedSmallInteger cukup untuk port (0..65535)
            if (!Schema::hasColumn('source_servers', 'ssh_port')) {
                $table->unsignedSmallInteger('ssh_port')->default(22)->after('ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('source_servers', function (Blueprint $table) {
            if (Schema::hasColumn('source_servers', 'ssh_port')) {
                $table->dropColumn('ssh_port');
            }
        });
    }
};
