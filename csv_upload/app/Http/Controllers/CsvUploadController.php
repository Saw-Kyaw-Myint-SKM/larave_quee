<?php

namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;
use App\Jobs\ProcessEmployees;
use Illuminate\Support\Facades\Bus;

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
                 $filWithPath=public_path('uploads').'/'.$fileName;
                 if(!file_exists($filWithPath)){
                    $request->file('csvFile')->move(public_path('uploads'), $fileName);
                 }
                 $header=null;
                 $dataFromcsv=array();
                 $records=array_map('str_getcsv',file($filWithPath));
                 

                 foreach($records as $record){
                    if(!$header){
                        $header =$record;
                    }else {
                        $dataFromcsv[]=$record;
                    }
                 }
                $dataFromcsv=array_chunk($dataFromcsv,300);
                $batch= Bus::batch([])->dispatch();
                 foreach($dataFromcsv as $index=>$dataCsv){
                    foreach($dataCsv as $data){
                        $employeeData[$index][]= array_combine($header,$data);
                    }
                    $batch->add(new ProcessEmployees($employeeData[$index]));
                    // ProcessEmployees::dispatch($employeeData[$index]);
                 }
                session()->put('lastBatchId',$batch->id);
                return redirect('/progress?id='. $batch->id);
            }
            //   dd($request->all());
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}