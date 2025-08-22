<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('benchmark_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_server_id')->constrained()->cascadeOnDelete();
            $table->string('target_ip');
            $table->enum('protocol', ['TCP','UDP','ICMP']);
            $table->unsignedInteger('port')->nullable(); // optional untuk ICMP
            $table->unsignedInteger('packet_count')->default(1000);
            $table->unsignedInteger('packet_size')->default(64); // bytes
            $table->unsignedInteger('interval_us')->default(1000); // microseconds antara paket
            $table->float('duration_sec')->nullable();
            $table->unsignedInteger('packets_sent')->nullable();
            $table->unsignedInteger('packets_received')->nullable();
            $table->float('pps')->nullable(); // packets per second
            $table->unsignedBigInteger('bitrate_bps')->nullable(); // perkiraan
            $table->text('raw_output')->nullable();
            $table->string('status')->default('finished'); // finished/failed
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('benchmark_results');
    }
};
