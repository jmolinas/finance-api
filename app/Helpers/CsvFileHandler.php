<?php

namespace App\Helpers;

class CsvFileHandler
{
    protected $data = [];

    /**
     * Get Data from CSV
     *
     * @return array
     */
    public function get() : array
    {
        return $this->data;
    }

    /**
     * Validate file
     *
     * @param string $file file path
     * 
     * @return void|RuntimeException
     */
    public function validate($file)
    {
        if (!file_exists($file)) {
            throw new \RuntimeException('File not found');
        }

        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
        if ($fileExt != 'csv') {
            throw new \RuntimeException('Invalid file type, should be cvs');
        }
    }

    /**
     * Large Csv Data Handler.
     *
     * @param string   $file     File Path
     * @param callable $callback Callback
     */
    public function handle(string $file, $callback)
    {
        $this->validate($file);

        $handle = fopen($file, 'r');
        while (!feof($handle)) {
            try {
                $data = fgetcsv($handle);
                call_user_func_array($callback, [$data, &$handle]);
            } catch (\Exception $e) {
                continue;
            }
        }

        fclose($handle);
    }
}
