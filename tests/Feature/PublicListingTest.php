<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_actual_database_listings_and_categories(): void
    {
        $cat = Category::factory()->create(['name' => 'Desain Grafis']);
        $customer = User::factory()->create(['name' => 'Budi Customer', 'role' => 'customer']);
        $provider = User::factory()->create(['name' => 'Siti Provider', 'role' => 'provider']);

        $request = ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
            'category_id' => $cat->id,
            'title' => 'Permintaan Logo Unik',
            'status' => 'open',
        ]);

        $offer = ServiceOffer::factory()->create([
            'provider_id' => $provider->id,
            'category_id' => $cat->id,
            'title' => 'Jasa Pembuatan Website Modern',
            'status' => 'active',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Desain Grafis');
        $response->assertSee('Permintaan Logo Unik');
        $response->assertSee('Budi Customer');
        $response->assertSee('Jasa Pembuatan Website Modern');
        $response->assertSee('Siti Provider');
    }

    public function test_public_service_requests_index_shows_open_requests(): void
    {
        $customer = User::factory()->create(['name' => 'Andi Pelanggan']);

        $openRequest = ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
            'title' => 'Butuh Tukang Bangunan Cepat',
            'status' => 'open',
        ]);

        $closedRequest = ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
            'title' => 'Permintaan yang Sudah Selesai',
            'status' => 'completed',
        ]);

        $response = $this->get(route('butuh.index'));

        $response->assertOk();
        $response->assertSee('Butuh Tukang Bangunan Cepat');
        $response->assertDontSee('Permintaan yang Sudah Selesai');
    }

    public function test_public_service_offers_index_shows_active_offers(): void
    {
        $provider = User::factory()->create(['name' => 'Dina Freelancer']);

        $activeOffer = ServiceOffer::factory()->create([
            'provider_id' => $provider->id,
            'title' => 'Jasa Penerjemah Bahasa Inggris',
            'status' => 'active',
        ]);

        $inactiveOffer = ServiceOffer::factory()->create([
            'provider_id' => $provider->id,
            'title' => 'Jasa Servis Nonaktif',
            'status' => 'inactive',
        ]);

        $response = $this->get(route('sedia.index'));

        $response->assertOk();
        $response->assertSee('Jasa Penerjemah Bahasa Inggris');
        $response->assertDontSee('Jasa Servis Nonaktif');
    }

    public function test_public_can_view_service_request_and_offer_details(): void
    {
        $customer = User::factory()->create(['name' => 'Eko Customer']);
        $provider = User::factory()->create(['name' => 'Fajar Provider']);

        $request = ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
            'title' => 'Detail Kebutuhan Acara',
            'description' => 'Deskripsi lengkap kebutuhan acara pernikahan.',
        ]);

        $offer = ServiceOffer::factory()->create([
            'provider_id' => $provider->id,
            'title' => 'Detail Jasa Katering Sehat',
            'description' => 'Menyediakan katering harian sehat dan higienis.',
        ]);

        $this->get(route('butuh.show', $request))
            ->assertOk()
            ->assertSee('Detail Kebutuhan Acara')
            ->assertSee('Deskripsi lengkap kebutuhan acara pernikahan.');

        $this->get(route('sedia.show', $offer))
            ->assertOk()
            ->assertSee('Detail Jasa Katering Sehat')
            ->assertSee('Menyediakan katering harian sehat dan higienis.');
    }

    public function test_customer_can_create_request_and_appears_in_public_index(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post(route('service-requests.store'), [
            'title' => 'Permintaan Terbaru Dari Form',
            'description' => 'Deskripsi kebutuhan lebih dari dua puluh karakter yang valid.',
            'budget_min' => 100000,
            'budget_max' => 200000,
            'work_mode' => 'online',
        ])->assertRedirect();

        $this->get(route('butuh.index'))
            ->assertOk()
            ->assertSee('Permintaan Terbaru Dari Form');
    }

    public function test_provider_can_create_offer_and_appears_in_public_index(): void
    {
        $provider = User::factory()->create(['role' => 'provider']);

        $this->actingAs($provider)->post(route('service-offers.store'), [
            'title' => 'Penawaran Terbaru Dari Form',
            'description' => 'Deskripsi penawaran lebih dari dua puluh karakter yang valid.',
            'price' => 150000,
            'price_type' => 'fixed',
            'work_mode' => 'online',
        ])->assertRedirect();

        $this->get(route('sedia.index'))
            ->assertOk()
            ->assertSee('Penawaran Terbaru Dari Form');
    }
}

