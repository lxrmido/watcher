<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\TestBatch;
use App\Model\TestResult;

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
                        ->leftJoin('sites', 'test_results.site_id', 'sites.id')
                        ->selectRaw('test_results.*, sites.service_area_id, sites.name')
                        ->orderBy('timecost', 'desc')
                        ->get();
        $results2 = TestResult::where('batch_id', $first->id)
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
        return view('index', [
            'batches' => $batches,
            'results' => $results,
        ]);
    }
}
