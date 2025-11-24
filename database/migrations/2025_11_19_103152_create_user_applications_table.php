<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application'); // 'commercial', 'gestion-dossier', 'administration'
            $table->string('role')->nullable(); // 'admin', 'user', 'viewer', etc.
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamps();
            
            $table->unique(['user_id', 'application']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_applications');
    }
};
