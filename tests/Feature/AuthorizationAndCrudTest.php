<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationAndCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_crud_own_service_request(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create(['name' => 'IT & Komputer']);

        // 1. Create
        $response = $this->actingAs($customer)->post(route('service-requests.store'), [
            'title'       => 'Perbaikan Laptop Mati Total',
            'description' => 'Laptop tidak mau menyala sama sekali setelah terkena air hujan.',
            'category_id' => $category->id,
            'budget_min'  => 200000,
            'budget_max'  => 400000,
            'work_mode'   => 'onsite',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_requests', [
            'customer_id' => $customer->id,
            'title'       => 'Perbaikan Laptop Mati Total',
        ]);

        $request = ServiceRequest::first();

        // 2. Read (detail)
        $this->actingAs($customer)
            ->get(route('service-requests.show', $request))
            ->assertOk()
            ->assertSee('Perbaikan Laptop Mati Total');

        // 3. Update
        $this->actingAs($customer)
            ->put(route('service-requests.update', $request), [
                'title'       => 'Perbaikan Laptop Update',
                'description' => 'Laptop tidak mau menyala sama sekali setelah terkena air hujan.',
                'status'      => 'in_progress',
            ])
            ->assertRedirect();

        $request->refresh();
        $this->assertEquals('Perbaikan Laptop Update', $request->title);
        $this->assertEquals('in_progress', $request->status);

        // 4. Delete
        $this->actingAs($customer)
            ->delete(route('service-requests.destroy', $request))
            ->assertRedirect();

        $this->assertDatabaseMissing('service_requests', ['id' => $request->id]);
    }

    public function test_provider_cannot_create_or_modify_other_service_request(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $provider = User::factory()->create(['role' => 'provider']);

        // Provider tidak boleh create service request
        $this->actingAs($provider)
            ->post(route('service-requests.store'), [
                'title'       => 'Mencoba buat request',
                'description' => 'Deskripsi valid lebih dari dua puluh karakter.',
            ])
            ->assertForbidden();

        $request = ServiceRequest::factory()->create(['customer_id' => $customer->id]);

        // Provider tidak boleh update atau delete request milik customer
        $this->actingAs($provider)
            ->put(route('service-requests.update', $request), [
                'title'       => 'Mencoba update request orang lain',
                'description' => 'Deskripsi valid lebih dari dua puluh karakter.',
            ])
            ->assertForbidden();

        $this->actingAs($provider)
            ->delete(route('service-requests.destroy', $request))
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_service_request(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $request = ServiceRequest::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($admin)
            ->delete(route('service-requests.destroy', $request))
            ->assertRedirect();

        $this->assertDatabaseMissing('service_requests', ['id' => $request->id]);
    }

    public function test_provider_can_crud_own_service_offer(): void
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create(['name' => 'Desain']);

        // 1. Create
        $response = $this->actingAs($provider)->post(route('service-offers.store'), [
            'title'         => 'Jasa Desain Logo Startup',
            'description'   => 'Menyediakan desain logo profesional dengan revisi tak terbatas.',
            'category_id'   => $category->id,
            'price'         => 350000,
            'price_type'    => 'fixed',
            'work_mode'     => 'online',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_offers', [
            'provider_id' => $provider->id,
            'title'       => 'Jasa Desain Logo Startup',
        ]);

        $offer = ServiceOffer::first();

        // 2. Read
        $this->get(route('service-offers.show', $offer))
            ->assertOk()
            ->assertSee('Jasa Desain Logo Startup');

        // 3. Update
        $this->actingAs($provider)
            ->put(route('service-offers.update', $offer), [
                'title'       => 'Jasa Desain Logo Update',
                'description' => 'Menyediakan desain logo profesional dengan revisi tak terbatas.',
                'status'      => 'inactive',
            ])
            ->assertRedirect();

        $offer->refresh();
        $this->assertEquals('Jasa Desain Logo Update', $offer->title);
        $this->assertEquals('inactive', $offer->status);

        // 4. Delete
        $this->actingAs($provider)
            ->delete(route('service-offers.destroy', $offer))
            ->assertRedirect();

        $this->assertDatabaseMissing('service_offers', ['id' => $offer->id]);
    }

    public function test_customer_cannot_create_or_modify_service_offer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $provider = User::factory()->create(['role' => 'provider']);

        // Customer tidak boleh buat service offer
        $this->actingAs($customer)
            ->post(route('service-offers.store'), [
                'title'       => 'Mencoba buat offer',
                'description' => 'Deskripsi valid lebih dari dua puluh karakter.',
            ])
            ->assertForbidden();

        $offer = ServiceOffer::factory()->create(['provider_id' => $provider->id]);

        // Customer tidak boleh ubah atau hapus offer milik provider
        $this->actingAs($customer)
            ->put(route('service-offers.update', $offer), [
                'title'       => 'Mencoba ubah offer orang lain',
                'description' => 'Deskripsi valid lebih dari dua puluh karakter.',
            ])
            ->assertForbidden();

        $this->actingAs($customer)
            ->delete(route('service-offers.destroy', $offer))
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_service_offer(): void
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $admin = User::factory()->create(['role' => 'admin']);

        $offer = ServiceOffer::factory()->create(['provider_id' => $provider->id]);

        $this->actingAs($admin)
            ->delete(route('service-offers.destroy', $offer))
            ->assertRedirect();

        $this->assertDatabaseMissing('service_offers', ['id' => $offer->id]);
    }
}

