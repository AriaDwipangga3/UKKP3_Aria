<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test CRUD user (menu "Kelola User" milik admin).
 * Jalankan dengan: php artisan test --filter=UserCrudTest
 */
class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    /** Daftar user bisa dibuka admin dan bisa dicari. */
    public function test_admin_can_list_and_search_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'Budi Santoso']);
        User::factory()->create(['name' => 'Siti Rahma']);

        $this->actingAs($admin)->get('/admin/users?q=Budi')
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Rahma');
    }

    /** Admin menambah user -> langsung approved dan password ter-hash. */
    public function test_admin_can_create_user_with_approved_status(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name'     => 'Dewi Lestari',
            'email'    => 'dewi@example.com',
            'password' => 'rahasia123',
            'role'     => 'customer',
        ])->assertRedirect('/admin/users');

        $user = User::where('email', 'dewi@example.com')->firstOrFail();
        $this->assertSame('approved', $user->status);
        $this->assertNotNull($user->approved_at);
        $this->assertNotSame('rahasia123', $user->password); // tersimpan sebagai hash, bukan teks asli
    }

    /** Email tidak boleh kembar dan password minimal 8 karakter. */
    public function test_create_user_validation(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'sama@example.com']);

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'X', 'email' => 'sama@example.com', 'password' => 'pendek', 'role' => 'customer',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    /** Admin mengubah data user; password kosong berarti tidak diganti. */
    public function test_admin_can_update_user_without_changing_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $passwordLama = $user->password;

        $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name'     => 'Nama Baru',
            'email'    => $user->email, // email sendiri boleh dipakai ulang
            'password' => '',
            'role'     => 'customer',
            'status'   => 'rejected',
        ])->assertRedirect('/admin/users');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('rejected', $user->status);
        $this->assertSame($passwordLama, $user->password);
    }

    /** Admin tidak bisa mengubah role/status akunnya sendiri. */
    public function test_admin_cannot_change_own_role_or_status(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put("/admin/users/{$admin->id}", [
            'name'   => $admin->name,
            'email'  => $admin->email,
            'role'   => 'customer',
            'status' => 'rejected',
        ]);

        $admin->refresh();
        $this->assertSame('admin', $admin->role);
        $this->assertSame('approved', $admin->status);
    }

    /** Customer yang dijadikan admin otomatis berstatus approved. */
    public function test_promoted_admin_is_always_approved(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->pending()->create();

        $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => 'admin',
            'status' => 'pending',
        ]);

        $this->assertSame('approved', $user->fresh()->status);
    }

    /** Admin bisa menghapus customer, tapi tidak bisa menghapus akun admin. */
    public function test_admin_can_delete_customer_but_not_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $lain = User::factory()->admin()->create();
        $customer = User::factory()->create();

        $this->actingAs($admin)->delete("/admin/users/{$customer->id}")->assertRedirect();
        $this->assertModelMissing($customer);

        $this->actingAs($admin)->delete("/admin/users/{$lain->id}")->assertForbidden();
        $this->assertModelExists($lain);
    }

    /** Customer tidak boleh membuka halaman Kelola User. */
    public function test_customer_cannot_access_user_crud(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/admin/users/create')->assertForbidden();
        $this->actingAs($customer)->post('/admin/users', [])->assertForbidden();
    }
}
