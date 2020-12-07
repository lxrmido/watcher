<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Model\Site;
use App\Model\TestBatch;
use App\Model\TestResult;
use App\Model\TestError;
use Exception;

class TestSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $batchId;
    private $siteId;
    private $retryLeft;
    private $batch;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batchId, $siteId, $retryLeft = 3)
    {
        $this->batchId = $batchId;
        $this->siteId = $siteId;
        $this->retryLeft = $retryLeft;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $site = Site::find($this->siteId);
        if (!$site) {
            $this->log('找不到站点:' . $this->siteId);
            return;
        }
        $batch = TestBatch::find($this->batchId);
        if (!$batch) {
            $this->log('找不到批次:' . $this->batchId);
            return;
        }
        if (time() - $batch->created_at->timestamp > 20 * 60) {
            $this->log('已失效:' . $batch->created_at);
            return;
        }
        $this->test($site);
    }

    public function test($site){
        $handler = new \GuzzleHttp\Handler\StreamHandler;
        $client = new Client([
            'verify' => false,
            // 'handler' => $handler,
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'Accept-Encoding' => 'gzip,deflate',
                'Accept-Language' => 'zh,en;q=0.9,zh-CN;q=0.8,en-US;q=0.7,zh-TW;q=0.6,ja;q=0.5',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'Host' => $site->getHost(),
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.67 Safari/537.36 Aoyou/Q1ptUU5FYmE3c2BGSXVOR1ifDcWjCFlHhlCL7yDP9zqZGkd7RySwFqvr'
            ],
            'timeout' => 20
        ]);
        $t0 = microtime(true);
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
        if ($statusCode == -1) {
            if ($this->retryLeft > 0) {
                dispatch(new static($this->batchId, $this->siteId, $this->retryLeft - 1));
                $this->log(sprintf('%s 标记重试，剩余次数: %d', $site->url, $this->retryLeft));
                return;
            }
        }
        $t2 = microtime(true);
        $timecost = intval(($t2 - $t1) * 1000);
        $timeall = intval(($t2 - $t0) * 1000);
        $testResult = new TestResult;
        $testResult->batch_id = $this->batchId;
        $testResult->site_id = $site->id;
        $testResult->status_code = $statusCode;
        $testResult->timecost = $timecost;
        $testResult->url = $site->url;
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
        $this->log(sprintf("%s: %dms [%d]", $site->url, $timecost, $statusCode));
    }

    private function log($message){
        $str = sprintf("[%d|%d]%s\n", $this->batchId, $this->siteId, $message);
        echo $str;
    }
}
