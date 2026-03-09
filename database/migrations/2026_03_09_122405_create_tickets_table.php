<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Le ticket concerne un matériel précis. Si l'asset est supprimé, ses tickets aussi.
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->onDelete('restrict');


            // Qui a créé le ticket ? C'est un employé. S'il est supprimé, on garde le ticket (nullOnDelete).
            $table->foreignId('requester_employee_id')->constrained('employees')->onDelete('restrict');
            // À qui est assigné le ticket ? C'est un utilisateur du système (tech/admin).
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('reference')->unique(); // Ex: TCK-2026-0001
            $table->string('priority')->default(TicketPriority::MEDIUM->value);
            $table->string('status')->default(TicketStatus::OUVERT->value);
            $table->text('description');

            // Suivi des dates clés
            $table->timestamp('due_at')->nullable(); // La deadline
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
