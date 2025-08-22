<?php
// database/migrations/2025_08_20_000001_add_iface_to_source_servers.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('source_servers', function (Blueprint $t) {
            $t->string('iface')->nullable()->after('ip'); // contoh: eth0, ens18
        });
    }
    public function down(): void {
        Schema::table('source_servers', function (Blueprint $t) {
            $t->dropColumn('iface');
        });
    }
};
