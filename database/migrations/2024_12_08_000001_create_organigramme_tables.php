<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des départements/directions
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('color')->default('#3B82F6');
            $table->timestamps();
        });

        // Table des postes/positions
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_position_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->integer('level')->default(1); // Niveau hiérarchique
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Table des employés dans l'organigramme
        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name'); // Si pas de user_id
            $table->string('status')->default('ACTIVE'); // ACTIVE, VACANT, INTERIM
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
