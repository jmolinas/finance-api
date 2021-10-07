<?php

namespace App\Transformers;

use Illuminate\Support\Collection;

class AccountsSerializer
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];


    /**
     * inArray Multidimentional
     *
     * @param string $needle
     * @param array $haystack
     * @param boolean $strict
     *
     * @return boolean
     */
    protected function inArrayMultidimentional(string $needle, array $haystack, bool $strict = false)
    {
        foreach ($haystack as $item) {
            if (
                ($strict ? $item === $needle : $item == $needle) ||
                (is_array($item) && $this->inArrayMultidimentional($needle, $item, $strict))
            ) {
                return true;
            }
        }
        return false;
    }

    protected function builder($type = 'settled', $key, array $value)
    {
        if ($this->inArrayMultidimentional($type, $value) === false) {
            return [
                'type' => $key,
                'transfer_status' => $type,
                'amount' => "0"
            ];
        }
        return $value;
    }

    /**
     * Transform
     *
     * @param Collection $collection
     *
     * @return static
     */
    public function transform(Collection $collection)
    {
        foreach ($collection as $key => $value) {
            $this->data[$key]['settled'] = $this->builder('settled', $key, $value->toArray());
            $this->data[$key]['unsettled'] = $this->builder('unsettled', $key, $value->toArray());
        }
        return $this;
    }


    /**
     * Get Data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
