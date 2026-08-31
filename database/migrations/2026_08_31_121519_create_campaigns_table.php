<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('subject');

            $table->string('template_original_name')->nullable();

            $table->boolean('duplicate_protection_enabled')->default(true);
            $table->unsignedInteger('duplicate_protection_days')->default(7);

            $table->unsignedInteger('delay_seconds')->default(5);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
