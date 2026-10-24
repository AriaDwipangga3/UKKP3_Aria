<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test alur utama: register -> menunggu ACC -> admin menerima -> customer bisa login.
 * Jalankan dengan: php artisan test
 */
class AccApprovalTest extends TestCase
{
    use RefreshDatabase; // database di-reset otomatis tiap test

    /** Register membuat customer berstatus pending dan TIDAK langsung login. */
    public function test_register_creates_pending_customer(): void
    {
        $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/login');

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertSame('pending', $user->status);
        $this->assertGuest();
    }

    /** Akun pending tidak bisa login walau password benar. */
    public function test_pending_user_cannot_login(): void
    {
        $user = User::factory()->pending()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** Akun yang ditolak tidak bisa login. */
    public function test_rejected_user_cannot_login(): void
    {
        $user = User::factory()->rejected()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** Customer yang sudah di-ACC bisa login dan masuk ke dashboard customer. */
    public function test_approved_customer_can_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertRedirect('/customer/dashboard');
    }

    /** Customer tidak boleh membuka halaman admin. */
    public function test_customer_cannot_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/users')
            ->assertForbidden();
    }

    /** Admin menerima dan menolak akun lewat tombol di dashboard. */
    public function test_admin_can_approve_and_reject(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)->patch("/admin/users/{$pending->id}/approve")->assertRedirect();
        $this->assertTrue($pending->fresh()->isApproved());
        $this->assertNotNull($pending->fresh()->approved_at);

        $this->actingAs($admin)->patch("/admin/users/{$pending->id}/reject")->assertRedirect();
        $this->assertTrue($pending->fresh()->isRejected());
    }

    /** Dashboard admin menampilkan customer yang menunggu ACC beserta tombol Terima / Tolak. */
    public function test_dashboard_lists_pending_customers(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create(['name' => 'Calon Customer']);

        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Calon Customer')
            ->assertSee('Terima')
            ->assertSee('Tolak');
    }

    /** Admin bisa menghapus akun customer. */
    public function test_admin_can_delete_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->pending()->create();

        $this->actingAs($admin)->delete("/admin/users/{$customer->id}")->assertRedirect();
        $this->assertModelMissing($customer);
    }

    /** Akun admin tidak bisa diterima / ditolak / dihapus dari dashboard. */
    public function test_admin_account_cannot_be_processed(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertForbidden();
        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/reject")->assertForbidden();
    }
}
