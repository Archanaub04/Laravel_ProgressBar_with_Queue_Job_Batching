<?php

namespace App\Jobs;

use App\Models\Sales;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SalesCSVProcess implements ShouldQueue
{
    use Queueable,Batchable;

    public $data;
    public $header;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $header)
    {
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $sale) {
            $saleData = array_combine($this->header, $sale);
            Sales::create($saleData);
        }
    }

    public function failed(Throwable $exception)
    {
        // send user notification of failure, etc...

    }
}
