<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\TestBatch;
use App\Model\TestResult;
use App\Model\TestError;
use App\Model\Site;

class ViewController extends Controller
{
    public function index(Request $request){
        $batches = TestBatch::where('status', 1)
                                    ->orderBy('id', 'desc')
                                    ->limit(300)
                                    ->get();
        $first = $batches[0];
        $results1 = TestResult::where('batch_id', $first->id)
                        ->where('status_code', '!=', 200)
                        ->whereNotNull('sites.id')
                        ->leftJoin('sites', 'test_results.site_id', 'sites.id')
                        ->selectRaw('test_results.*, sites.service_area_id, sites.name')
                        ->orderBy('timecost', 'desc')
                        ->get();
        $results2 = TestResult::where('batch_id', $first->id)
                        ->where('status_code', 200)
                        ->whereNotNull('sites.id')
                        ->leftJoin('sites', 'test_results.site_id', 'sites.id')
                        ->selectRaw('test_results.*, sites.service_area_id, sites.name')
                        ->orderBy('timecost', 'desc')
                        ->get();
        $results = [];
        foreach ($results1 as $result){
            $result->health = 'error';
            $results[] = $result;
        }
        foreach ($results2 as $result){
            if ($result->timecost > env('TIMECOST_SLOW')) {
                $result->health = 'slow';
            } else if ($result->timecost < env('TIMECOST_FAST')) {
                $result->health = 'fast';
            }
            $results[] = $result;
        }
        return view('index', [
            'batches' => $batches,
            'results' => $results,
        ]);
    }

    public function site(Request $request, $siteId){
        $site = Site::find($siteId);
        if (!$site) {
            abort(404, '站点不存在');
        }
        $results = TestResult::where('site_id', $site->id)
                                ->orderBy('id', 'desc')
                                ->limit(500)
                                ->get();
        $urls = [
            'site' => $site->url
        ];
        if ($site->with_app == 1) {
            $urls['gkml'] = $site->getAppUrl('gkml');
            $urls['hdjl'] = $site->getAppUrl('hdjl');
            $urls['yjzj'] = $site->getAppUrl('yjzj');
        }
        return view('site', [
            'results' => $results,
            'site' => $site,
            'urls' => $urls
        ]);
    }

    public function batch(Request $request, $batchId){
        $batch = TestBatch::find($batchId);
        if (!$batch) {
            abort(404, '批次不存在');
        }
        $batches = TestBatch::where('status', 1)
                                    ->orderBy('id', 'desc')
                                    ->limit(300)
                                    ->get();
        $first = $batches[0];
        $results1 = TestResult::where('batch_id', $batch->id)
                        ->where('status_code', '!=', 200)
                        ->leftJoin('sites', 'test_results.site_id', 'sites.id')
                        ->selectRaw('test_results.*, sites.service_area_id, sites.name')
                        ->orderBy('timecost', 'desc')
                        ->get();
        $results2 = TestResult::where('batch_id', $batch->id)
                        ->where('status_code', 200)
                        ->leftJoin('sites', 'test_results.site_id', 'sites.id')
                        ->selectRaw('test_results.*, sites.service_area_id, sites.name')
                        ->orderBy('timecost', 'desc')
                        ->get();
        $results = [];
        foreach ($results1 as $result){
            $result->health = 'error';
            $results[] = $result;
        }
        foreach ($results2 as $result){
            if ($result->timecost > env('TIMECOST_SLOW')) {
                $result->health = 'slow';
            } else if ($result->timecost < env('TIMECOST_FAST')) {
                $result->health = 'fast';
            }
            $results[] = $result;
        }
        return view('batch', [
            'results' => $results,
            'batch' => $batch,
        ]);
    }
}
