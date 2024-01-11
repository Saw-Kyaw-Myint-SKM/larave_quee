<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Stmt\TryCatch;

class ProcessEmployees implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public $employeeData;
    /**
     * Create a new job instance.
     */
    public function __construct($employeeData)
    {
        $this->employeeData =$employeeData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            foreach($this->employeeData as $employeeData){
                $employee =new Employee();
                $employee->name=$employeeData['first_name'].' '.$employeeData['last_name'];
                $employee->save();
            }
    }
}