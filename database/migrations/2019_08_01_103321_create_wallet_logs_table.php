<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('schema.finance') . '.wallet_logs', function (Blueprint $table) {
            $table->uuid('wallet_id')->index();
            $table->uuid('transfer_id')->index();
            $table->decimal('amount', 14, 2);
            $table->decimal('running_balance', 14, 2);
            $table->foreign('transfer_id')
                ->references('id')
                ->on(config('schema.finance') . '.transfers');
            $table->foreign('wallet_id')
                ->references('id')
                ->on(config('schema.finance') . '.wallets');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('schema.finance') . '.wallet_logs');
    }
}
