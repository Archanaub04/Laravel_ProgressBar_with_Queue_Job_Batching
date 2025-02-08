<?php

namespace App\Http\Controllers;

use App\Jobs\SalesCSVProcess;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;


class SalesController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }

    public function upload()
    {
        $validator = Validator::make(request()->all(), [
            'mycsv' => 'required|file|mimes:csv,xlsx,xls', // Validates file type and size
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Return errors with 422 status
        }

        if (request()->has('mycsv')) {
            $file = request()->mycsv;
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'csv') {
                $reader = new Csv();
            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                $reader = new Xlsx();
            } else {
                return 'Unsupported file type';
            }

            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $data = [];
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            // Make chunks of the data
            $chunks = array_chunk($data, 1000);

            $header = [];

            // create batch for job batching in case of multiple jobs
            // empty batch
            $batch = Bus::batch([])->dispatch();

            foreach ($chunks as $key => $chunk) {
                if ($key === 0) {
                    $header = $chunk[0];
                    unset($chunk[0]);
                }

                // job batching here
                // adding the jobs into the empty batch
                $batch->add(new SalesCSVProcess($chunk, $header));
            }

            return $batch;
        }
        return 'Please upload a file';
    }

    public function batch(string $batchid)
    {
        return Bus::findBatch($batchid);
    }

    public function batchInProgress()
    {
        // Log the batches fetched from the database
        $batches = DB::table('job_batches')->where('pending_jobs', '>', 0)->get();
        Log::info('Fetched batches: ', $batches->toArray());

        if (count($batches) > 0) {
            $batchId = $batches[0]->id;
            Log::info('First batch ID: ' . $batchId);

            $batch = Bus::findBatch($batchId);
            if ($batch) {
                Log::info('Batch details: ', $batch->toArray());
                return response()->json($batch);
            } else {
                Log::warning('Batch not found for ID: ' . $batchId);
            }
        }

        // Log and return empty response if no batch is found
        Log::info('No batch in progress');
        return response()->json([]);
    }
}
