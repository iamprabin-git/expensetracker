<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CompanySettingSeeder::class);
        $this->call(SitePageSeeder::class);

        User::query()->updateOrCreate(
            ['email' => 'admin@expensetracker.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_approved' => true,
                'approved_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        $user = User::query()->updateOrCreate(
            ['email' => 'user@expensetracker.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'is_approved' => true,
                'approved_at' => now(),
                'membership_fee' => 9.00,
                'membership_expires_at' => now()->addYear(),
                'currency' => 'USD',
                'timezone' => 'UTC',
                'locale' => 'en',
                'email_verified_at' => now(),
            ],
        );

        $categories = [
            ['name' => 'Salary', 'type' => CategoryType::Income, 'icon' => 'banknotes'],
            ['name' => 'Freelance', 'type' => CategoryType::Income, 'icon' => 'computer-desktop'],
            ['name' => 'Food & Dining', 'type' => CategoryType::Expense, 'icon' => 'utensils'],
            ['name' => 'Transport', 'type' => CategoryType::Expense, 'icon' => 'truck'],
            ['name' => 'Shopping', 'type' => CategoryType::Expense, 'icon' => 'shopping-bag'],
            ['name' => 'Bills', 'type' => CategoryType::Expense, 'icon' => 'document-text'],
        ];

        foreach ($categories as $data) {
            Category::query()->firstOrCreate(
                ['name' => $data['name'], 'user_id' => null],
                $data,
            );
        }

        $salary = Category::query()->where('name', 'Salary')->first();
        $food = Category::query()->where('name', 'Food & Dining')->first();
        $transport = Category::query()->where('name', 'Transport')->first();

        Transaction::query()->updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Monthly Salary', 'transaction_date' => now()->startOfMonth()],
            [
                'category_id' => $salary?->id,
                'type' => TransactionType::Income,
                'amount' => 4500,
                'description' => 'May salary deposit',
            ],
        );

        Transaction::query()->updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Grocery Run', 'transaction_date' => now()->subDays(2)],
            [
                'category_id' => $food?->id,
                'type' => TransactionType::Expense,
                'amount' => 86.42,
                'description' => 'Weekly groceries',
            ],
        );

        ContactMessage::query()->firstOrCreate(
            ['email' => 'visitor@example.com', 'subject' => 'Membership question'],
            [
                'name' => 'Jane Visitor',
                'message' => 'How do I renew my membership after expiry?',
                'is_read' => false,
            ],
        );

        Review::query()->firstOrCreate(
            ['display_name' => 'Alex M.', 'content' => 'Clean dashboard and easy to track spending. Approval process was quick.'],
            [
                'user_id' => $user->id,
                'rating' => 5,
                'is_approved' => true,
                'approved_at' => now()->subDays(3),
            ],
        );

        Review::query()->firstOrCreate(
            ['display_name' => 'Sam K.', 'content' => 'Great for organizing monthly budgets without complexity.'],
            [
                'rating' => 4,
                'is_approved' => true,
                'approved_at' => now()->subDays(10),
            ],
        );
    }
}
