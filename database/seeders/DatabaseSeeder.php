<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Review;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'jasa-rumah-tangga'],
            ['name' => 'Jasa Rumah Tangga'],
        );

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@bantuin.test'],
            ['name' => 'Admin Bantuin', 'role' => 'admin', 'password' => Hash::make('password')],
        );

        $customer = User::query()->firstOrCreate(
            ['email' => 'customer@bantuin.test'],
            ['name' => 'Demo Customer', 'role' => 'customer', 'password' => Hash::make('password')],
        );

        $provider = User::query()->firstOrCreate(
            ['email' => 'provider@bantuin.test'],
            ['name' => 'Demo Provider', 'role' => 'provider', 'password' => Hash::make('password')],
        );

        $serviceRequest = ServiceRequest::query()->firstOrCreate(
            ['customer_id' => $customer->id, 'title' => 'Bantuan bersih-bersih rumah'],
            [
                'category_id' => $category->id,
                'description' => 'Membutuhkan bantuan membersihkan rumah setelah renovasi.',
                'budget_min' => 150000,
                'budget_max' => 350000,
                'work_mode' => 'onsite',
                'location' => 'Pekanbaru',
                'status' => 'open',
            ],
        );

        ServiceOffer::query()->firstOrCreate(
            ['provider_id' => $provider->id, 'title' => 'Jasa bersih-bersih rumah'],
            [
                'category_id' => $category->id,
                'description' => 'Layanan kebersihan rumah dan ruang kerja.',
                'price' => 200000,
                'price_type' => 'fixed',
                'work_mode' => 'onsite',
                'location' => 'Pekanbaru',
                'status' => 'active',
            ],
        );

        $conversation = Conversation::query()->firstOrCreate(
            [
                'customer_id' => $customer->id,
                'provider_id' => $provider->id,
                'service_request_id' => $serviceRequest->id,
            ],
            ['service_offer_id' => null, 'status' => 'active', 'completed_at' => now()],
        );

        Message::query()->firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $provider->id,
                'body' => 'Saya tersedia membantu kebutuhan ini.',
            ],
        );

        Review::query()->firstOrCreate(
            ['conversation_id' => $conversation->id, 'reviewer_id' => $customer->id],
            ['reviewee_id' => $provider->id, 'rating' => 5, 'comment' => 'Komunikasi baik dan pekerjaan rapi.'],
        );

        unset($admin);
    }
}
