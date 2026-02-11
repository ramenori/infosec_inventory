<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contactperson', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number')->nullable(); // Added contact number
            $table->text('address')->nullable(); // Added address (text for longer content)
            $table->string('satellite_office')->nullable(); // Added satellite office
            $table->timestamps();
            $table->softDeletes(); // Added for soft deletion (actions)
        });
    }

    public function down()
    {
        Schema::dropIfExists('contactperson');
    }
};