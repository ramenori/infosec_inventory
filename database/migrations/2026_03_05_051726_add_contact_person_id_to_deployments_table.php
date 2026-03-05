<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->foreignId('contact_person_id')
                ->nullable()
                ->after('user_id')
                ->constrained('contactperson')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropForeignIdFor('contactperson');
        });
    }
};
