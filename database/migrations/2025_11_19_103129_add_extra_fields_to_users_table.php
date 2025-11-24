<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricule')->unique()->nullable()->after('id');
            $table->string('prenom')->nullable()->after('name');
            $table->string('telephone')->nullable();
            $table->string('poste')->nullable();
            $table->string('departement')->nullable();
            $table->date('date_embauche')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'matricule', 'prenom', 'telephone', 'poste', 'departement',
                'date_embauche', 'photo', 'is_active', 'last_login_at'
            ]);
        });
    }
};
