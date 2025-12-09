<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('category'); // CATEGORY (e.g., Access Control, CCTV)
            $table->string('component'); // COMPONENT (e.g., Camera, Sensor)
            $table->string('serial_num')->nullable(); // SERIAL NUM (optional)
            $table->string('brand')->nullable(); // BRAND
            $table->integer('stock_qty')->default(0); // STOCK QTY
            $table->date('date_added'); // DATE ADDED
            $table->string('status')->default('Available'); // STATUS (e.g., Available, Deployed)
            $table->string('supplier')->nullable(); // SUPPLIER
            $table->timestamps(); // Created/Updated timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};