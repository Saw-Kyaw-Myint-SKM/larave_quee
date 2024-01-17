<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEmployees;
use App\Models\JobBatch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class CsvUploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function progress()
    {
        return view('progress');
    }

    public function uploadFileAndStoreDatabase(Request $request)
    {
        try {
            if ($request->hasFile('csvFile')) {
                $fileName = $request->csvFile->getClientOriginalName();
                $filWithPath = public_path('uploads') . '/' . $fileName;
                if (!file_exists($filWithPath)) {
                    $request->file('csvFile')->move(public_path('uploads'), $fileName);
                }
                $header = null;
                $dataFromcsv = array();
                $records = array_map('str_getcsv', file($filWithPath));

                foreach ($records as $record) {
                    if (!$header) {
                        $header = $record;
                    } else {
                        $dataFromcsv[] = $record;
                    }
                }
                $dataFromcsv = array_chunk($dataFromcsv, 300);
                $batch = Bus::batch([])->dispatch();
                foreach ($dataFromcsv as $index => $dataCsv) {
                    foreach ($dataCsv as $data) {
                        $employeeData[$index][] = array_combine($header, $data);
                    }
                    $batch->add(new ProcessEmployees($employeeData[$index]));
                    // ProcessEmployees::dispatch($employeeData[$index]);
                }
                session()->put('lastBatchId', $batch->id);
                return redirect('/progress?id=' . $batch->id);
            }
            //   dd($request->all());
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function progressForCsvStoreProcess(Request $request)
    {
        try {
            $batchId = $request->id ?? session()->get('lastBatchId');
            if (JobBatch::where('id', $batchId)->count()) {
                $response = JobBatch::where('id', $batchId)->first();
                return response()->json($response);
            }
        } catch (Exception $e) {
            Log::error($e);
            dd($e);
        }
    }
}
