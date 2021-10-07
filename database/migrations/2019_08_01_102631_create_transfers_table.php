<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('schema.finance') . '.transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('from_wallet_id');
            $table->uuid('to_wallet_id');
            $table->enum(
                'type',
                [
                    'disbursement',
                    'sale',
                    'collection',
                    'settlement',
                    'return',
                    'production_cost',
                    'shipping',
                    'chargeback',
                    'revenue',
                    'charges'
                ]
            );
            $table
                ->enum(
                    'status',
                    [
                        'chargedback',
                        'refunded',
                        'partially_refunded'
                    ]
                )
                ->nullable();
            $table->decimal('amount', 14, 2);
            $table->uuid('party_id')->nullable()->index();
            $table->foreign('party_id')
                ->references('id')
                ->on(config('schema.core') . '.parties');
            $table->text('details');
            $table->jsonb('metadata')->nullable()->index();
            $table->string('order_id')->nullable()->index();
            $table->string('transaction_id')->index();
            $table->string('sku')->nullable()->index();
            $table->string('product_id')->nullable()->index();
            $table->string('campaign_id')->nullable()->index();
            $table->uuid('ledger_id')->nullable();
            $table->foreign('ledger_id')
                ->references('id')
                ->on(config('schema.finance') . '.ledgers');
            $table->foreign('from_wallet_id')
                ->references('id')
                ->on(config('schema.finance') . '.wallets');
            $table->foreign('to_wallet_id')
                ->references('id')
                ->on(config('schema.finance') . '.wallets');
            $table->timestamps();
            $table->timestamp('transaction_date')->nullable();
            $table->unique(['transaction_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('schema.finance') . '.transfers');
    }
}
