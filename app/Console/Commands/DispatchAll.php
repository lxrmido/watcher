<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Model\Site;
use App\Model\TestBatch;
use App\Model\TestResult;
use App\Model\TestError;
use App\Jobs\TestSite;
use Exception;

class DispatchAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dispatch';
    private $batch = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all';

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
        $sites = Site::all();
        $total = $sites->count();
        $this->batch = new TestBatch;
        $this->batch->total = $total;
        $this->batch->save();
        foreach ($sites as $site) {
            dispatch(new TestSite($this->batch->id, $site->id));
        }        
        return 0;
    }
}
