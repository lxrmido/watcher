<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Site;
use App\Model\SiteSnapshot;
use App\Model\SiteSnapshotBatch;
use GuzzleHttp\Client;
use Log;
use OSS\OssClient;

class ShotAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shot:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $batch;
    private $startTime;
    private $ossClient;

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
        $sites = Site::where('service_area_id', '>', 0)->get();
        $total = $sites->count();
        $this->batch = new SiteSnapshotBatch;
        $this->batch->total = $total;
        $this->batch->save();
        $this->startTime = microtime(true);
        $this->ossClient = new OssClient(env('ALIYUN_ACCESS_KEY'), env('ALIYUN_ACCESS_TOKEN'), env('ALIYUN_ENDPOINT'));
        foreach ($sites as $site) {
            try {
                $this->log(sprintf('[begin]%d %s', $site->id, $site->url));
                $r = $this->handleEach($site);
                $this->log(sprintf('[done]%d %s: %d ms', $site->id, $site->url, $r->timecost));
            } catch (\Exception $e) {
                $this->batch->error ++;
                Log::error('ShotAll', [
                    'site' => $site,
                    'error' => $e->getMessage()
                ]);
                $this->log(sprintf('[error]%d %s: %s', $site->id, $site->url, $e->getMessage()));
            }
            $this->batch->finished ++;
            $this->batch->save();
        }
        $this->batch->timecost = intval((microtime(true) - $this->startTime) * 1000);
        $this->batch->save();
        return 0;
    }

    public function handleEach($site){
        $t1 = microtime(true);
        $url = env('SNAPSHOT_SERVICE_URL') . '?url=';
        $url .= urlencode($site->url);
        $url .= '&fullPage=1';

        $client  = new Client;
        $response = $client->request('GET', $url);
        if (in_array('image/jpeg', $response->getHeader('Content-Type'))) {
            $imageData = $response->getBody();
        } else {
            Log::error('TopicShareSnapshotCaptureError', [
                'url' => $url,
                'response-type' => $response->getHeader('Content-Type')
            ]);
            throw new \Exception('TopicShareSnapshotCaptureError');
        }
        $this->log('Shoted');
        $ossPath = 'snapshot/batch/' . $this->batch->id . '/' . $site->id . '.jpg';
        $imageOssUploadResult = $this->ossClient->putObject(
            env('ALIYUN_BUCKET_NAME'), 
            $ossPath, 
            $imageData
        );
        if (empty($imageOssUploadResult['info']['url'])) {
            throw new \Exception('AliYun image upload failed.');
        }
        $this->log('Uploaded');
        $this->ossClient->putObjectAcl(
            env('ALIYUN_BUCKET_NAME'), 
            $ossPath, 
            'public-read'
        );
        $this->log('ACL');
        var_dump($imageOssUploadResult);
        $t2 = microtime(true);
        $timecost = intval(($t2 - $t1) * 1000);
        $snapshot = new SiteSnapshot;
        $snapshot->site_id = $site->id;
        $snapshot->batch_id = $this->batch->id;
        $snapshot->url = $site->url;
        $snapshot->timecost = $timecost;
        $snapshot->oss_url = $imageOssUploadResult['info']['url'];
        $snapshot->save();
        return $snapshot;
    }

    private function log($message){
        $timePassed = intval((microtime(true) - $this->startTime) * 1000);
        echo sprintf("%d ms | %s\n", $timePassed, $message);
    }
}
