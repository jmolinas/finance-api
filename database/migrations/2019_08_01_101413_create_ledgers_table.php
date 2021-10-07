<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('schema.finance') . '.ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('party_id')->index();
            $table->foreign('party_id')
                ->references('id')
                ->on(config('schema.core') . '.parties');
            $table->char('currency_code', 3)->index();
            $table->uuid('transfer_id')->nullable()->index();
            $table->string('transaction_type')->nullable()->index();
            $table->enum('type', ['payable', 'receivable']);
            $table->enum('status', ['settled', 'pending', 'cancelled'])->default('pending');
            $table->decimal('amount', 14, 2);
            $table->timestamp('settled_at')->nullable();
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
        Schema::dropIfExists(config('schema.finance') . '.ledgers');
    }
}
