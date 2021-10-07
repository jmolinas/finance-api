<?php

namespace App\Console\Commands;

use App\Services\Ledger\SalesLedger;
use App\Services\Transfer\Ledgers\SalesLedgerTransfer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SalesLedgerCreation extends Command
{
    protected $salesLedgerTransfer, $salesTransfer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:sales';

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
        SalesLedger $salesLedger,
        SalesLedgerTransfer $transfers
    ) {
        parent::__construct();
        $this->salesTransfer = $salesLedger;
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
                $ledger = $this->salesTransfer->process();
                $this->salesLedgerTransfer->transfer($ledger);
            }
        );
    }
}
