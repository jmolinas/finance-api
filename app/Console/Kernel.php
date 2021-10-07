<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SalesLedgerCreation::class,
        Commands\CollectionLedgerCreation::class,
        Commands\AffiliateCsvImporter::class,
        Commands\SalesLedgerTransfer::class,
        Commands\CollectionLedgerTransfer::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        // Create Sales Ledger
        $schedule->command('transfer:sales')
            ->dailyAt('01:00')
            ->timezone('Asia/Manila');

        // Create Collection Ledger
        $schedule->command('transfer:collection')
            ->dailyAt('02:00')
            ->timezone('Asia/Manila');
    }
}
