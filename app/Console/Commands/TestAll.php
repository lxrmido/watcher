<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Model\Site;
use App\Model\TestBatch;
use App\Model\TestResult;
use App\Model\TestError;
use Exception;


class TestAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:all';
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
        $index = 0;
        $client = new Client([
            'verify' => false,
            'timeout' => 10
        ]);
        $t0 = microtime(true);
        foreach ($sites as $site) {
            $statusCode = -1;
            $errorContent = '';
            $errorMessage = '';
            $t1 = microtime(true);
            try {
                $res = $client->get($site->url);
                $statusCode = $res->getStatusCode();
            } catch (RequestException $e) {
                $res = $e->getResponse();
                if ($res) {
                    $statusCode = $res->getStatusCode();
                    $errorContent = $res->getBody()->getContents();
                }
                $errorMessage = $e->getMessage();
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
            $t2 = microtime(true);
            $timecost = intval(($t2 - $t1) * 1000);
            $timeall = intval(($t2 - $t0) * 1000);
            $testResult = new TestResult;
            $testResult->batch_id = $this->batch->id;
            $testResult->site_id = $site->id;
            $testResult->status_code = $statusCode;
            $testResult->timecost = $timecost;
            $testResult->save();
            if ($statusCode !== 200) {
                $testError = new TestError;
                $testError->result_id = $testResult->id;
                $testError->site_id = $site->id;
                $testError->url = $site->url;
                $testError->timecost = $timecost;
                $testError->status_code = $statusCode;
                $testError->error_message = $errorMessage;
                $testError->error_content = $errorContent;
                $testError->save();
            }
            $index ++;
            echo sprintf("%d/%d %s: %dms/%dms [%d]\n", $index, $total, $site->url, $timecost, $timeall, $statusCode);
        }        
        return 0;
    }
}
