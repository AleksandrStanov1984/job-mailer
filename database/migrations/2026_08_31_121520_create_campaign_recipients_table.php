<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('company')->nullable();
            $table->string('email');
            $table->string('normalized_email')->index();

            $table->string('vacancy')->nullable();
            $table->string('contact_name')->nullable();

            $table->string('status')->default('pending')->index();

            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('skipped_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['normalized_email', 'status', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_recipients');
    }
};
