<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deployment_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deployment_id')->constrained('deployments')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
            
            // Prevent duplicate items in same deployment
            $table->unique(['deployment_id', 'inventory_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('deployment_carts');
    }
};