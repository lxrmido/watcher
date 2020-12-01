<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\TestBatch;
use App\Model\TestResult;


class CheckBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check test batches';

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
        $batches = TestBatch::where('status', 0)->get();
        foreach ($batches as $batch) {
            $resultsCount = TestResult::where('batch_id', $batch->id)->count();
            if ($resultsCount >= $batch->total) {
                $batch->status = 1;
                $batch->timecost = TestResult::where('batch_id', $batch->id)->sum('timecost');
                $batch->error = TestResult::where('batch_id', $batch->id)
                                        ->where('status_code', '!=', 200)
                                        ->count();
            }
            $batch->finished = $resultsCount;
            $batch->save();
        }
    }
}
