<?php
// database/migrations/2025_08_20_000002_add_runtime_cols_to_benchmark_results.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('benchmark_results', function (Blueprint $t) {
            $t->unsignedBigInteger('remote_pid')->nullable()->after('status');
            $t->timestamp('started_at')->nullable()->after('remote_pid');
            $t->timestamp('stopped_at')->nullable()->after('started_at');
            $t->string('iface_used')->nullable()->after('stopped_at');
        });
    }
    public function down(): void {
        Schema::table('benchmark_results', function (Blueprint $t) {
            $t->dropColumn(['remote_pid','started_at','stopped_at','iface_used']);
        });
    }
};
