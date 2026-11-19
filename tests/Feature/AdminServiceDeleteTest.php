<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_melihat_daftar_jasa(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = ServiceRequest::factory()->create();
        $offer = ServiceOffer::factory()->create();

        $this->actingAs($admin)->get(route('admin.services.index'))
            ->assertOk()->assertSee($request->title);

        $this->actingAs($admin)->get(route('admin.services.index', ['tipe' => 'sedia']))
            ->assertOk()->assertSee($offer->title);
    }

    public function test_admin_menghapus_satu_permintaan_jasa_saja(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $target = ServiceRequest::factory()->create(['category_id' => $category->id]);
        $other = ServiceRequest::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->delete(route('admin.services.requests.destroy', $target))
            ->assertRedirect(route('admin.services.index', ['tipe' => 'butuh']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('service_requests', ['id' => $target->id]);
        $this->assertDatabaseHas('service_requests', ['id' => $other->id]);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_menghapus_satu_penawaran_jasa_saja(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $target = ServiceOffer::factory()->create(['category_id' => $category->id]);
        $other = ServiceOffer::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->delete(route('admin.services.offers.destroy', $target))
            ->assertRedirect(route('admin.services.index', ['tipe' => 'sedia']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('service_offers', ['id' => $target->id]);
        $this->assertDatabaseHas('service_offers', ['id' => $other->id]);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_non_admin_tidak_dapat_memakai_halaman_hapus_admin(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $request = ServiceRequest::factory()->create();

        $this->actingAs($customer)->get(route('admin.services.index'))->assertForbidden();
        $this->actingAs($customer)->delete(route('admin.services.requests.destroy', $request))->assertForbidden();
        $this->assertDatabaseHas('service_requests', ['id' => $request->id]);
    }
}
