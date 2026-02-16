<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactFieldsToDeploymentsTable extends Migration
{
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('waybill_number')->nullable()->after('reference_number');
            $table->string('contact_number')->nullable()->after('deployed_to');
            $table->text('address')->nullable()->after('contact_number');
            $table->string('satellite_office')->nullable()->after('address');
        });
    }

    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn(['waybill_number', 'contact_number', 'address', 'satellite_office']);
        });
    }
}