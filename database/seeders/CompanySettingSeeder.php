<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::query()->updateOrCreate(
            ['id' => 1],
            CompanySetting::defaults(),
        );
    }
}
