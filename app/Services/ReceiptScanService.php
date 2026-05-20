<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ReceiptScanService
{
    public function isConfigured(): bool
    {
        return $this->resolveProvider() !== null;
    }

    public function activeProvider(): ?string
    {
        return $this->resolveProvider();
    }

    /**
     * @return array{
     *     type: string,
     *     title: string,
     *     amount: float,
     *     transaction_date: string,
     *     description: ?string,
     *     merchant: ?string,
     *     category_hint: ?string,
     *     category_id: ?int,
     *     confidence: string,
     *     currency: ?string
     * }
     */
    public function analyze(UploadedFile $image, User $user): array
    {
        $provider = $this->resolveProvider();

        if ($provider === null) {
            throw new RuntimeException('AI scanning is not configured. Add GEMINI_API_KEY or OPENAI_API_KEY to your .env file.');
        }

        $content = match ($provider) {
            'gemini' => $this->requestGemini($image),
            'openai' => $this->requestOpenAi($image),
            default => throw new RuntimeException('Unsupported AI provider.'),
        };

        $parsed = $this->parseJsonResponse($content);

        return $this->normalizeResult($parsed, $user);
    }

    private function resolveProvider(): ?string
    {
        $configured = strtolower((string) config('ai.provider', 'auto'));

        if ($configured === 'gemini') {
            return filled(config('ai.gemini_api_key')) ? 'gemini' : null;
        }

        if ($configured === 'openai') {
            return filled(config('ai.openai_api_key')) ? 'openai' : null;
        }

        if (filled(config('ai.gemini_api_key'))) {
            return 'gemini';
        }

        if (filled(config('ai.openai_api_key'))) {
            return 'openai';
        }

        return null;
    }

    private function requestGemini(UploadedFile $image): string
    {
        $apiKey = config('ai.gemini_api_key');
        $model = $this->modelForProvider('gemini');
        $mime = $this->normalizeMimeType($image);
        $base64 = base64_encode($image->get());

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent';

        $response = Http::timeout(90)
            ->withQueryParameters(['key' => $apiKey])
            ->post($url, [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $this->systemPrompt()],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => 'Extract all transaction fields from this receipt, bill, or invoice image. Return only valid JSON.',
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => $mime,
                                    'data' => $base64,
                                ],
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message')
                ?? $response->json('error.status')
                ?? Str::limit($response->body(), 300);

            throw new RuntimeException('AI scan failed: '.$message);
        }

        $parts = $response->json('candidates.0.content.parts', []);

        foreach ($parts as $part) {
            $text = $part['text'] ?? null;
            if (is_string($text) && trim($text) !== '') {
                return $text;
            }
        }

        $blockReason = $response->json('promptFeedback.blockReason')
            ?? $response->json('candidates.0.finishReason');

        throw new RuntimeException(
            $blockReason
                ? 'AI could not process this image: '.$blockReason
                : 'AI returned an empty response. Try a clearer photo.',
        );
    }

    private function requestOpenAi(UploadedFile $image): string
    {
        $mime = $this->normalizeMimeType($image);
        $base64 = base64_encode($image->get());
        $model = $this->modelForProvider('openai');

        $response = Http::withToken(config('ai.openai_api_key'))
            ->timeout(90)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Extract all transaction fields from this receipt, bill, or invoice image.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mime};base64,{$base64}",
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message') ?? Str::limit($response->body(), 300);

            throw new RuntimeException('AI scan failed: '.$message);
        }

        $content = $response->json('choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('AI returned an empty response. Try a clearer photo.');
        }

        return $content;
    }

    private function modelForProvider(string $provider): string
    {
        $model = (string) config('ai.scan_model');

        if ($provider === 'gemini') {
            if (str_starts_with($model, 'gpt-')) {
                return 'gemini-2.5-flash';
            }

            return str_starts_with($model, 'models/') ? Str::after($model, 'models/') : $model;
        }

        if (str_starts_with($model, 'gemini')) {
            return 'gpt-4o-mini';
        }

        return $model !== '' ? $model : 'gpt-4o-mini';
    }

    private function normalizeMimeType(UploadedFile $image): string
    {
        $mime = strtolower($image->getMimeType() ?: '');

        if (in_array($mime, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'], true)) {
            return $mime === 'image/jpg' ? 'image/jpeg' : $mime;
        }

        $extension = strtolower($image->getClientOriginalExtension());

        return match ($extension) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    private function systemPrompt(): string
    {
        $types = collect(TransactionType::cases())
            ->map(fn (TransactionType $t) => $t->value)
            ->implode(', ');

        return <<<PROMPT
You analyze photos of receipts, bills, and invoices for a personal finance app.

Return ONLY a JSON object with these keys:
- type: one of {$types} (use "expense" for purchases/bills, "income" for money received, "asset" for valuable purchases tracked as assets, "liability" for loans/credit owed)
- title: short title for the transaction (merchant or purpose)
- amount: positive number only (total paid or received, no currency symbols)
- transaction_date: date in Y-m-d format (best estimate from document; use today if missing)
- description: optional extra details (items summary, payment method)
- merchant: store or payee name if visible
- category_hint: suggested category label (e.g. Groceries, Salary, Transport, Bills)
- currency: ISO currency code if visible (e.g. USD, NPR), else null
- confidence: high, medium, or low

Rules:
- amount must be a number greater than 0
- Prefer expense for shopping receipts and utility bills
- Prefer income for payroll, refunds credited as income, or deposits
PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseJsonResponse(string $content): array
    {
        $content = trim($content);

        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $data = json_decode($content, true);

        if (! is_array($data)) {
            throw new RuntimeException('Could not read structured data from the AI response.');
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     type: string,
     *     title: string,
     *     amount: float,
     *     transaction_date: string,
     *     description: ?string,
     *     merchant: ?string,
     *     category_hint: ?string,
     *     category_id: ?int,
     *     confidence: string,
     *     currency: ?string
     * }
     */
    private function normalizeResult(array $data, User $user): array
    {
        $type = TransactionType::tryFrom((string) ($data['type'] ?? 'expense'))
            ?? TransactionType::Expense;

        $amount = (float) preg_replace('/[^\d.]/', '', (string) ($data['amount'] ?? 0));

        if ($amount <= 0) {
            throw new RuntimeException('Could not detect a valid amount on this bill. Enter it manually.');
        }

        $title = Str::limit(trim((string) ($data['title'] ?? $data['merchant'] ?? 'Scanned transaction')), 255, '');

        if ($title === '') {
            $title = 'Scanned transaction';
        }

        $date = (string) ($data['transaction_date'] ?? now()->toDateString());

        try {
            $date = \Illuminate\Support\Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            $date = now()->toDateString();
        }

        $categoryHint = filled($data['category_hint'] ?? null)
            ? trim((string) $data['category_hint'])
            : null;

        $description = filled($data['description'] ?? null)
            ? trim((string) $data['description'])
            : null;

        if (filled($data['merchant'] ?? null) && $description) {
            $merchant = trim((string) $data['merchant']);
            if (! str_contains(strtolower($description), strtolower($merchant))) {
                $description = $merchant.' — '.$description;
            }
        } elseif (filled($data['merchant'] ?? null)) {
            $description = trim((string) $data['merchant']);
        }

        $confidence = in_array($data['confidence'] ?? '', ['high', 'medium', 'low'], true)
            ? $data['confidence']
            : 'medium';

        return [
            'type' => $type->value,
            'title' => $title,
            'amount' => round($amount, 2),
            'transaction_date' => $date,
            'description' => $description,
            'merchant' => filled($data['merchant'] ?? null) ? trim((string) $data['merchant']) : null,
            'category_hint' => $categoryHint,
            'category_id' => $this->matchCategoryId($user, $categoryHint, $type),
            'confidence' => $confidence,
            'currency' => filled($data['currency'] ?? null) ? strtoupper((string) $data['currency']) : null,
        ];
    }

    private function matchCategoryId(User $user, ?string $hint, TransactionType $type): ?int
    {
        if (! $hint) {
            return null;
        }

        $categories = Category::forUser($user)->orderBy('name')->get();
        $hintLower = strtolower($hint);

        $exact = $categories->first(fn (Category $c) => strtolower($c->name) === $hintLower);

        if ($exact) {
            return $exact->id;
        }

        $partial = $categories->first(fn (Category $c) => str_contains(strtolower($c->name), $hintLower)
            || str_contains($hintLower, strtolower($c->name)));

        return $partial?->id;
    }
}
