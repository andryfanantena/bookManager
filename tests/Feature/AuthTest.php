<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('BookManager')
            ->assertSee('Bon retour parmi nous')
            ->assertSee('Se connecter');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'jean.dupont@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'jean.dupont@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'jean.dupont@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'jean.dupont@example.com',
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk()
            ->assertSee('Créer un nouveau compte')
            ->assertSee('Photo de profil')
            ->assertSee('Créer mon compte');
    }

    public function test_user_can_register_with_profile_picture(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->post(route('register.post'), [
            'name' => 'Sophie Germain',
            'email' => 'sophie@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'avatar' => $avatar,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'sophie@example.com')->firstOrFail();
        $this->assertSame('Sophie Germain', $user->name);
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_update_profile_and_change_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Ancien Nom',
            'email' => 'ancien@example.com',
        ]);

        $newAvatar = UploadedFile::fake()->image('nouveau_profil.png', 300, 300);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nouveau Nom',
            'email' => 'nouveau@example.com',
            'avatar' => $newAvatar,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nouveau Nom', $user->name);
        $this->assertSame('nouveau@example.com', $user->email);
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_delete_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('photo.jpg');
        $path = $avatar->store('avatars', 'public');

        $user = User::factory()->create(['avatar' => $path]);

        $response = $this->actingAs($user)->delete(route('profile.avatar.destroy'));

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}

