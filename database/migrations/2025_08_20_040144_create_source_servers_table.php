<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('source_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip');
            $table->unsignedSmallInteger('ssh_port')->default(22);
            $table->string('ssh_user');
            $table->string('ssh_password'); // untuk demo; produksi: prefer keyfile/secret manager
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('source_servers');
    }
};
