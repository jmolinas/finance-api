<?php

namespace App\Console\Commands;

use App\Services\Ledger\CollectionLedger;
use App\Services\Transfer\Ledgers\CollectionLedgerTransfer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CollectionLedgerCreation extends Command
{
    protected $collectionLedger, $collectionLedgerTransfer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:collection';

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
        CollectionLedger $collectionLedger, 
        CollectionLedgerTransfer $transfers
    )
    {
        parent::__construct();
        $this->collectionLedger = $collectionLedger;
        $this->collectionLedgerTransfer = $transfers;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::transaction(function () {
            $ledger = $this->collectionLedger->process();
            $this->collectionLedgerTransfer->transferCollection($ledger);
        });
    }
}
