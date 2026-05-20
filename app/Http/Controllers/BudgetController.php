<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Models\BudgetItem;
use App\Models\Category;
use App\Services\BudgetPlannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(
        private readonly BudgetPlannerService $planner,
    ) {}

    public function index(Request $request): View
    {
        $month = $this->planner->resolveMonth($request->string('month')->toString() ?: null);
        $data = $this->planner->build($request->user(), $month);

        $usedCategoryIds = $data['plan']->items->pluck('category_id');

        $data['available_categories'] = $data['categories']
            ->filter(fn (Category $category) => in_array($category->type, [
                CategoryType::Income,
                CategoryType::Expense,
                CategoryType::Both,
            ], true))
            ->reject(fn (Category $category) => $usedCategoryIds->contains($category->id));
        $data['can_copy_previous'] = $this->planner->previousPlanLabel($request->user(), $month) !== null;

        return view('budgets.index', $data);
    }

    public function updatePlan(Request $request): RedirectResponse
    {
        $month = $this->planner->resolveMonth($request->input('month'));
        $plan = $this->planner->planForMonth($request->user(), $month);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'income_goal' => ['nullable', 'numeric', 'min:0'],
            'expense_limit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $plan->update([
            'name' => $validated['name'] ?? $plan->name,
            'income_goal' => $validated['income_goal'] ?? 0,
            'expense_limit' => $validated['expense_limit'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('budgets.index', ['month' => $month->format('Y-m')])
            ->with('success', 'Budget plan updated.');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $month = $this->planner->resolveMonth($request->input('month'));
        $plan = $this->planner->planForMonth($request->user(), $month);

        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->whereNull('user_id')->orWhere('user_id', $request->user()->id);
                }),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($plan->items()->where('category_id', $validated['category_id'])->exists()) {
            return back()
                ->withInput()
                ->withErrors(['category_id' => 'This category already has a budget line for this month.']);
        }

        $plan->items()->create($validated);

        return redirect()
            ->route('budgets.index', ['month' => $month->format('Y-m')])
            ->with('success', 'Category budget added.');
    }

    public function updateItem(Request $request, BudgetItem $budgetItem): RedirectResponse
    {
        $plan = $this->authorizeItem($request, $budgetItem);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $budgetItem->update(['amount' => $validated['amount']]);

        $month = $this->planner->resolveMonth($validated['month'] ?? $plan->period_month->format('Y-m'));

        return redirect()
            ->route('budgets.index', ['month' => $month->format('Y-m')])
            ->with('success', 'Budget line updated.');
    }

    public function destroyItem(Request $request, BudgetItem $budgetItem): RedirectResponse
    {
        $plan = $this->authorizeItem($request, $budgetItem);
        $month = $plan->period_month->format('Y-m');

        $budgetItem->delete();

        return redirect()
            ->route('budgets.index', ['month' => $month])
            ->with('success', 'Budget line removed.');
    }

    public function duplicate(Request $request): RedirectResponse
    {
        $month = $this->planner->resolveMonth($request->input('month'));

        $user = $request->user();
        $sourceLabel = $this->planner->previousPlanLabel($user, $month);
        $copied = $this->planner->duplicateFromPreviousMonth($user, $month);

        if (! $copied || ! $sourceLabel) {
            return redirect()
                ->route('budgets.index', ['month' => $month->format('Y-m')])
                ->with('error', 'No earlier budget plan found to copy from.');
        }

        return redirect()
            ->route('budgets.index', ['month' => $month->format('Y-m')])
            ->with('success', 'Budget copied from '.$sourceLabel.'.');
    }

    protected function authorizeItem(Request $request, BudgetItem $budgetItem): BudgetPlan
    {
        $plan = $budgetItem->plan;

        abort_unless($plan && $plan->user_id === $request->user()->id, 403);

        return $plan;
    }

}
