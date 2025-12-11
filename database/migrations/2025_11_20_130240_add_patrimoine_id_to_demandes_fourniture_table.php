<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('demandes_fourniture', function (Blueprint $table) {
            $table->foreignId('patrimoine_id')->nullable()->after('notifier_user_id')->constrained('patrimoines')->onDelete('set null')->comment('Patrimoine créé après livraison');
            $table->boolean('patrimoine_cree')->default(false)->after('patrimoine_id')->comment('Indique si un patrimoine a été créé');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_fourniture', function (Blueprint $table) {
            $table->dropForeign(['patrimoine_id']);
            $table->dropColumn(['patrimoine_id', 'patrimoine_cree']);
        });
    }
};
