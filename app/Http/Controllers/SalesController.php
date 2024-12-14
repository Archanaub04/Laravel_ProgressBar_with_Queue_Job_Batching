<?php

namespace App\Http\Controllers;

use App\Jobs\SalesCSVProcess;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class SalesController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }

    // store sales data
    public function upload()
    {
        if (request()->has('mycsv')) {
            $data = file(request()->mycsv);

            // Make chunks of the data
            $chunks = array_chunk($data, 1000);

            $header = [];

            // create batch for job batching in case of multiple jobs
            // empty batch
            $batch = Bus::batch([])->dispatch();

            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);

                if ($key === 0) {
                    $header = $data[0];
                    unset($data[0]);
                }

                // job batnching here
                // adding the jobs into the empty batch
                $batch->add(new SalesCSVProcess($data, $header));
            }

            return $batch;
        }
        return 'Please upload a file';
    }

    public function batch(string $batchid)
    {
        return Bus::findBatch($batchid);
    }
}
