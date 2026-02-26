<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Storage::fake('public');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'pseudo' => 'testpseudo',
            'email' => 'test@example.com',
            // create a dummy file instead of image to avoid GD dependency
            'photo' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = \App\Models\User::first();
        Storage::disk('public')->assertExists($user->photo);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
