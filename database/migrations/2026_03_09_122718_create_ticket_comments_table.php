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
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();

            // Un commentaire appartient à un ticket. Si on supprime le ticket, les commentaires partent avec.
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            // Un commentaire est écrit par un utilisateur (employé ou tech).
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            $table->text('body');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
