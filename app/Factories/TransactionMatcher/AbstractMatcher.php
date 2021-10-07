<?php

namespace App\Factories\TransactionMatcher;

use App\Models\Finance\Transfers;
use App\Services\Ledger\ShippingLedger;
use App\Services\Transfer\TransfersFetcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

abstract class AbstractMatcher
{
    /**
     * File Instrance
     *
     * @var UploadedFile
     */
    protected $file;

    protected $transfers;

    protected $misMatch = [];

    /**
     * AbstractMatcher
     *
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * CSV Data Handler
     *
     * @param string $file
     * @param callable $callback
     * 
     * @return void
     */
    protected function csvHandler(string $file, $callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Must be callable');
        }

        $handle = fopen($file, 'r');
        $counter = 0;
        fgetcsv($handle);
        while (!feof($handle)) {
            try {
                $data = fgetcsv($handle);
                if ($data === false) {
                    continue;
                }
                $data = array_map('trim', $data);
                call_user_func_array($callback, [$data, $counter]);
                ++$counter;
            } catch (\Throwable $th) {
                continue;
            }
        }
    }

    /**
     * Get Instance of Ledger Factory
     *
     * @param string $class
     * @param string $key
     * 
     * @return void
     */
    protected function builderInstance(string $class)
    {
        $transfers = new TransfersFetcher(new Transfers());
        $transfers->setCollection($this->transfers);
        return new $class(new Ledger(), $transfers);
    }

    /**
     * Process Transaction Matching
     *
     * @return Collection
     */
    public function process()
    {
        $this->transfers = new Collection();
        $this->csvHandler($this->file->getRealPath(), function ($row) {
            try {
                $filter =  static::FILTER;
                $index = static::INDEX;
                $transfer = Transfers::whereType(static::TRANSFER_TYPE)
                    ->whereNull('ledger_id')
                    ->where($filter, $row[$index[$filter]])
                    ->whereAmount($row[$index['amount']])
                    ->firstOrFail();
                $this->transfers->push($transfer);
            } catch (\Throwable $th) {
                $this->misMatch[] = $row;
            }
        });
        $ledger = $this->builderInstance(ShippingLedger::class);
        return $ledger->process();
    }

    /**
     * Get Mismatched rows
     *
     * @return Array
     */
    public function getMismatch()
    {
        return $this->misMatch;
    }
}
