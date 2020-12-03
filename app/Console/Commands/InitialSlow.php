<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\TestBatch;
use App\Model\TestResult;

class InitialSlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:slow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $batches = TestBatch::all();
        foreach ($batches as $batch) {
            $batch->slow = TestResult::where('batch_id', $batch->id)
                                        ->where('timecost', '>', 2000)
                                        ->count();      
            $batch->fast = TestResult::where('batch_id', $batch->id)
                                        ->where('timecost', '<', 50)
                                        ->count();            
            $batch->save();
        }
        return 0;
    }
}