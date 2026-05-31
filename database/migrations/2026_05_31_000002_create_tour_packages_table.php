<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('destination');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedSmallInteger('max_guests')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('includes')->nullable();
            $table->json('itinerary')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
