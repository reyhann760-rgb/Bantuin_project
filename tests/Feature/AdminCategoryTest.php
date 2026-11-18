<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_categories_page(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_or_provider_cannot_access_admin_categories_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $provider = User::factory()->create(['role' => 'provider']);

        $this->actingAs($customer)
            ->get(route('admin.categories.index'))
            ->assertForbidden();

        $this->actingAs($provider)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_category_list_with_listing_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat = Category::factory()->create(['name' => 'Teknologi Informasi', 'slug' => 'teknologi-informasi']);

        $customer = User::factory()->create(['role' => 'customer']);
        ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
            'category_id' => $cat->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('Teknologi Informasi');
        $response->assertSee('teknologi-informasi');
        $response->assertSee('1 Butuh');
    }

    public function test_admin_can_create_new_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Desain Grafis & Kreatif',
            'slug' => 'desain-grafis',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Desain Grafis & Kreatif',
            'slug' => 'desain-grafis',
        ]);
    }

    public function test_category_creation_fails_on_duplicate_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Category::factory()->create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Elektronik',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat = Category::factory()->create(['name' => 'Kategori Awal', 'slug' => 'kategori-awal']);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $cat), [
            'name' => 'Kategori Baru',
            'slug' => 'kategori-baru',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $cat->refresh();
        $this->assertEquals('Kategori Baru', $cat->name);
        $this->assertEquals('kategori-baru', $cat->slug);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat = Category::factory()->create(['name' => 'Kategori Hapus', 'slug' => 'kategori-hapus']);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $cat));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $cat->id,
        ]);
    }
}

