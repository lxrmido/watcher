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
use App\Jobs\TestApp;
use App\Jobs\TestSearch;
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
        // 搜索
        $total = 1;
        // 站点首页
        $sites = Site::all();
        $total += $sites->count();
        $withAppSites = Site::where('with_app', 1)->get();
        // 公开目录
        $total += $withAppSites->count();
        // 互动交流
        $total += $withAppSites->count();
        // 意见征集
        $total += $withAppSites->count();    
        $this->batch = new TestBatch;
        $this->batch->total = $total;
        $this->batch->save();
        // 搜索
        dispatch(new TestSearch($this->batch->id));
        // 站点首页
        foreach ($sites as $site) {
            dispatch(new TestSite($this->batch->id, $site->id));
        }        
        // 公开目录
        foreach ($sites as $site) {
            dispatch(new TestApp($this->batch->id, $site->id, 'gkml'));
        }        
        // 互动交流
        foreach ($sites as $site) {
            dispatch(new TestApp($this->batch->id, $site->id, 'hdjl'));
        }        
        // 意见征集
        foreach ($sites as $site) {
            dispatch(new TestApp($this->batch->id, $site->id, 'yjzj'));
        }        
        return 0;
    }
}
