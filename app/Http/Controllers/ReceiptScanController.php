<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Services\ReceiptScanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use RuntimeException;

class ReceiptScanController extends Controller
{
    public function __construct(
        private readonly ReceiptScanService $scanner,
    ) {}

    public function index(Request $request): View
    {
        $categories = Category::forUser($request->user())->orderBy('name')->get();

        $user = $request->user();

        return view('ai-scan.index', [
            'categories' => $categories,
            'aiConfigured' => $this->scanner->isConfigured(),
            'aiProvider' => $this->scanner->activeProvider(),
            'aiScanAllowed' => $user->hasAiScanAccess(),
            'aiScanReady' => $user->canUseAiScan(),
            'maxUploadKb' => config('ai.max_upload_kb', 5120),
            'acceptedMimes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'],
        ]);
    }

    public function analyze(Request $request): JsonResponse
    {
        if (! $request->user()->canUseAiScan()) {
            return response()->json([
                'message' => $request->user()->hasAiScanAccess()
                    ? 'AI scanning is not configured on the server. Contact your administrator.'
                    : 'AI Scan is disabled for your account.',
            ], 403);
        }

        $maxKb = config('ai.max_upload_kb', 5120);

        $request->validate([
            'image' => [
                'required',
                'file',
                'max:'.$maxKb,
                function (string $attribute, $value, \Closure $fail): void {
                    if (! $value instanceof \Illuminate\Http\UploadedFile) {
                        $fail('Please upload a valid image file.');

                        return;
                    }

                    $mime = strtolower($value->getMimeType() ?: '');
                    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    $byExtension = preg_match('/\.(jpe?g|png|webp)$/i', $value->getClientOriginalName());

                    if (! in_array($mime, $allowed, true) && ! str_starts_with($mime, 'image/') && ! $byExtension) {
                        $fail('The image must be a JPG, PNG, or WebP file.');
                    }
                },
            ],
        ]);

        try {
            $result = $this->scanner->analyze($request->file('image'), $request->user());

            return response()->json([
                'ok' => true,
                'data' => $result,
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Something went wrong while scanning. Please try again or enter the transaction manually.',
            ], 500);
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $maxKb = config('ai.max_upload_kb', 5120);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->whereNull('user_id')->orWhere('user_id', $request->user()->id);
                }),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
            'receipt_image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:'.$maxKb,
            ],
        ]);

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image_path'] = $request->file('receipt_image')
                ->store('receipts/'.$request->user()->id, 'public');
        }

        unset($validated['receipt_image']);

        $request->user()->transactions()->create($validated);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction saved from AI scan.');
    }
}
