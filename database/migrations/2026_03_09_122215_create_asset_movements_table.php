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
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();

            // Qui a fait le mouvement ? C'est un utilisateur du système (Admin/Inventoriste).
            $table->foreignId('moved_by_user_id')->constrained('users')->onDelete('restrict');

            $table->string('type');

            // et on veut garder l'historique de leurs anciens ID.
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('from_employee_id')->nullable()->constrained('employees')->nullOnDelete();


            // -- État APRÈS le mouvement --
            // On ne met pas de contrainte onDelete pour la même raison : garder l'historique.
            $table->foreignId('to_location_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('to_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->timestamp('moved_at');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
