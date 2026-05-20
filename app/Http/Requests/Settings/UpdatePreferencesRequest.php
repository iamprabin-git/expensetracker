<?php

namespace App\Http\Requests\Settings;

use App\Support\Currencies;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', Rule::in(Currencies::enabledCodes())],
            'timezone' => ['required', 'string', 'timezone:all'],
            'locale' => ['required', 'string', Rule::in(array_keys(config('locales')))],
            'notification_sound_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
