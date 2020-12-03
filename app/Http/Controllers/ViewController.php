<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\TestBatch;

class ViewController extends Controller
{
    public function index(Request $request){
        return view('index', [
            'batches' => TestBatch::orderBy('id', 'desc')
                                    ->limit(300)
                                    ->get()
                                    ->toArray()
        ]);
    }
}
