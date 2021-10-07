<?php

namespace App\Console\Commands;

use App\Helpers\CsvFileHandler;
use Illuminate\Console\Command;
use App\Services\Affiliate\Importer;

class AffiliateCsvImporter extends Command
{
    protected $importer, $csv;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliates:csv {--path=} {--header=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new affiliates via csv upload.';

    /**
     * Create a new command instance.
     */
    public function __construct(Importer $importer, CsvFileHandler $fileHandler)
    {
        parent::__construct();

        $this->importer = $importer;
        $this->csv = $fileHandler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->option('path');
        $filePath = storage_path("app/{$file}");
        $this->csv->handle(
            $filePath,
            function ($data) {
                $sid = $data[0];
                $name = !empty($data[1]) ? $data[1] : $data[2];
                $email = $data[2];
                $affiliate = $this->importer->process($sid, $name, $email);
                print_r($affiliate->toArray());
            }
        );
    }
}
