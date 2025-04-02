<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'in_progress', 'ready_for_delivery', 'paid', 'completed', 'cancelled_by_user', 'cancelled_by_store'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'in_progress', 'ready_for_delivery', 'completed', 'cancelled_by_user', 'cancelled_by_store'])
                ->default('pending')
                ->change();
        });
    }
};
