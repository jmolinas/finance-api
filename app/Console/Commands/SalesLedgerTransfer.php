<?php

namespace App\Console\Commands;

use App\Models\Finance\Ledger;
use App\Services\Ledger\SalesLedger;
use App\Services\Transfer\Ledgers\SalesLedgerTransfer as SalesTransfers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SalesLedgerTransfer extends Command
{
    protected $salesLedgerTransfer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ledger-transfers:sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all sales and transfer to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        SalesTransfers $transfers
    ) {
        parent::__construct();
        $this->salesLedgerTransfer = $transfers;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::transaction(
            function () {
                $ledgers = Ledger::whereNull('transfer_id')
                    ->whereTransactionType('sale')
                    ->get();
                $this->salesLedgerTransfer->transferCollection($ledgers);
            }
        );
    }
}
