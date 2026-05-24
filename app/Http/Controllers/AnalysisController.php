<?php

namespace App\Http\Controllers;

use App\Services\BudgetPlannerService;
use App\Services\TransactionAnalytics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    public function index(Request $request, BudgetPlannerService $budgets): View
    {
        $user = $request->user();
        $analytics = TransactionAnalytics::for($user);
        $budgetMonth = $budgets->resolveMonth($request->query('month'));

        return view('analysis.index', [
            'summary' => $analytics->summary(),
            'chartData' => $analytics->chartPayload(),
            'budget' => $budgets->analysisPayload($user, $budgetMonth),
            'budgetMonth' => $budgetMonth,
        ]);
    }
}
