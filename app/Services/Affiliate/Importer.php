<?php

namespace App\Services\Affiliate;

use App\Models\Party;
use App\Models\Affiliates;
use DB;

class Importer
{
    protected $party;

    protected $wallets = [
        'fund',
        'sale'
    ];

    /**
     * Affiliate Importer.
     *
     * @param Party $party
     */
    public function __construct(Party $party)
    {
        $this->party = $party;
    }

    /**
     * Batch Process
     *
     * @param array $data
     * 
     * @return array
     */
    public function batchProcess(array $data)
    {
        return DB::transaction(
            function () use ($data) {
                $affiliates = [];
                foreach ($data as $row) {
                    $affiliates[] = $this->process($row['sid'], $row['name'], $row['email']);
                }
                return $affiliates;
            }
        );
    }

    /**
     * Process
     *
     * @param string $sid
     * @param string $name
     * @param string $email
     * 
     * @return Affiliates
     */
    public function process($sid, $name, $email)
    {
        return DB::transaction(
            function () use ($sid, $name, $email) {
                try {
                    $model = Affiliates::whereExternalId($sid)->first();

                    if ($model !== null) {
                        return $model;
                    }

                    $party = $this->party->create(['type' => 'affiliate']);

                    $affiliate = $party->affiliate()->create(
                        [
                            'external_id' => $sid,
                            'name' => $name,
                            'email' => $email
                        ]
                    );

                    foreach ($this->wallets as $type) {
                        $party->wallets()
                            ->create(
                                [
                                    'currency_code' => 'USD',
                                    'type' => $type,
                                    'amount' => 0
                                ]
                            );
                    }
                } catch (\Throwable $th) {
                    return $th->getMessage();
                }
                return $affiliate;
            }
        );
    }
}
