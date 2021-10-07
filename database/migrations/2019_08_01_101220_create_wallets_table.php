<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('schema.finance') . '.wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('currency_code', 3);
            $table->enum(
                'type',
                [
                    'order',
                    'fund',
                    'sale',
                    'collection',
                    'settlement',
                    'returns',
                    'chargeback',
                    'charges',
                    'disbursal'
                ]
            );
            $table->uuid('party_id')->index();
            $table->foreign('party_id')
                ->references('id')
                ->on(config('schema.core') . '.parties');
            $table->boolean('status')->default(1);
            $table->decimal('amount', 14, 2);
            $table->timestamps();
            $table->unique(['currency_code', 'type', 'party_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('schema.finance') . '.wallets');
    }
}
