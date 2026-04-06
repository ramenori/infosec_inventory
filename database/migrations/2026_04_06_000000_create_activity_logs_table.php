<?php

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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action')->default('viewed'); // created, updated, deleted, viewed, exported
            $table->string('entity_type')->default('inventory'); // inventory, category, supplier, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('component')->nullable(); // component name
            $table->text('details')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('action');
            $table->index('entity_type');
            $table->index('created_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
