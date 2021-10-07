<?php

namespace App\Console\Commands;

use App\Services\Transfer\Ledgers\CollectionLedgerTransfer as CollectionTransfers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\Ledger;

class CollectionLedgerTransfer extends Command
{
    protected $collectionLedgerTransfer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ledger-transfers:collection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all collections and transfer to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        CollectionTransfers $transfers
    ) {
        parent::__construct();
        $this->collectionLedgerTransfer = $transfers;
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
                    ->whereTransactionType('collection')
                    ->get();
                $this->collectionLedgerTransfer->transferCollection($ledgers);
            }
        );
    }
}
