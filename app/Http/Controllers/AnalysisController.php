<?php

namespace App\Http\Controllers;

use App\Services\TransactionAnalytics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    public function index(Request $request): View
    {
        $analytics = TransactionAnalytics::for($request->user());

        return view('analysis.index', [
            'summary' => $analytics->summary(),
            'chartData' => $analytics->chartPayload(),
        ]);
    }
}
