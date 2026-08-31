<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_recipients', function (Blueprint $table) {
            $table->string('contact_salutation')
                ->nullable()
                ->after('contact_name');

            $table->string('subject_rendered')
                ->nullable()
                ->after('contact_salutation');

            $table->text('message_rendered')
                ->nullable()
                ->after('subject_rendered');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_recipients', function (Blueprint $table) {
            $table->dropColumn([
                'contact_salutation',
                'subject_rendered',
                'message_rendered',
            ]);
        });
    }
};
