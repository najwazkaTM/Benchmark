<?php
// database/migrations/2025_08_20_000100_make_benchmark_metrics_nullable.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('benchmark_results', function (Blueprint $t) {
            // metrik bisa kosong saat START
            $t->unsignedBigInteger('packet_count')->nullable()->change();
            $t->unsignedInteger('interval_us')->nullable()->change();
            $t->decimal('duration_sec', 10, 3)->nullable()->change();
            $t->unsignedBigInteger('packets_sent')->nullable()->change();
            $t->unsignedBigInteger('packets_received')->nullable()->change();
            $t->double('pps')->nullable()->change();
            $t->unsignedBigInteger('bitrate_bps')->nullable()->change();

            // status bebas lowercase (running/finished/queued), default running boleh juga
            $t->string('status', 32)->default('running')->change();
        });
    }

    public function down(): void
    {
        Schema::table('benchmark_results', function (Blueprint $t) {
            // jika perlu rollback, kembalikan seperti semula (sesuaikan skema awalmu)
            $t->unsignedBigInteger('packet_count')->nullable(false)->change();
            $t->unsignedInteger('interval_us')->nullable(false)->change();
            $t->decimal('duration_sec', 10, 3)->nullable(false)->change();
            $t->unsignedBigInteger('packets_sent')->nullable(false)->change();
            $t->unsignedBigInteger('packets_received')->nullable(false)->change();
            $t->double('pps')->nullable(false)->change();
            $t->unsignedBigInteger('bitrate_bps')->nullable(false)->change();
            $t->string('status', 32)->default('finished')->change(); // contoh
        });
    }
};
