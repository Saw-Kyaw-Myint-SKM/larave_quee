<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEmployees;
use Illuminate\Http\Request;
use PDO;

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
                 foreach($dataFromcsv as $index=>$dataCsv){
                    foreach($dataCsv as $data){
                        $employeeData[$index][]= array_combine($header,$data);
                    }
                    ProcessEmployees::dispatch($employeeData[$index]);
                 }
            }
            //   dd($request->all());
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}