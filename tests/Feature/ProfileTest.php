<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Review;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile_edit_page(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_edit_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'city' => 'Surabaya',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('Surabaya');
    }

    public function test_user_can_update_biodata_and_contact(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'phone' => '0811111111',
            'city' => 'Jakarta',
            'bio' => 'Bio lama',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'phone' => '0822222222',
            'city' => 'Bandung',
            'bio' => 'Bio baru yang diperbarui.',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Nama Baru', $user->name);
        $this->assertEquals('0822222222', $user->phone);
        $this->assertEquals('Bandung', $user->city);
        $this->assertEquals('Bio baru yang diperbarui.', $user->bio);
    }

    public function test_user_can_upload_and_replace_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // Gunakan fake create dengan mime jpeg tanpa membutuhkan ekstensi GD
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'password_lama123',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('password_baru123', $user->password));
    }

    public function test_password_update_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'salah_password',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('password_lama123', $user->password));
    }

    public function test_public_profile_can_be_viewed_by_anyone(): void
    {
        $provider = User::factory()->create([
            'name' => 'Teknisi Handal',
            'role' => 'provider',
            'city' => 'Yogyakarta',
            'bio' => 'Berpengalaman servis komputer.',
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $category = Category::factory()->create(['name' => 'Elektronik']);

        $offer = ServiceOffer::factory()->create([
            'provider_id' => $provider->id,
            'category_id' => $category->id,
            'title' => 'Servis Laptop dan PC',
            'price' => 150000,
            'status' => 'active',
        ]);

        $conv = Conversation::factory()->create([
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'service_offer_id' => $offer->id,
        ]);

        Review::factory()->create([
            'conversation_id' => $conv->id,
            'reviewer_id' => $customer->id,
            'reviewee_id' => $provider->id,
            'rating' => 5,
            'comment' => 'Pelayanan sangat memuaskan dan cepat!',
        ]);

        $response = $this->get(route('profile.show', $provider));

        $response->assertOk();
        $response->assertSee('Teknisi Handal');
        $response->assertSee('Penyedia Jasa');
        $response->assertSee('Yogyakarta');
        $response->assertSee('Servis Laptop dan PC');
        $response->assertSee('Pelayanan sangat memuaskan dan cepat!');
    }
}
