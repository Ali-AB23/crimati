<?php

use App\Enums\AssetStatus;
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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('inventory_code')->unique();

            $table->foreignId('asset_type_id')->constrained('asset_types')->onDelete('restrict');

            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();


            $table->json('specs')->nullable();

            // Notre Enum pour suivre l'état du matériel.
            $table->string('status')->default(AssetStatus::EN_STOCK->value);


            $table->foreignId('current_location_id')->constrained('locations')->onDelete('restrict');


            $table->foreignId('current_employee_id')->nullable()->constrained('employees')->onDelete('set null');

            $table->text('notes')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
