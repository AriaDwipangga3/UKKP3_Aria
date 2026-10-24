<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Test fitur pengaduan + kategori pengaduan.
 * Jalankan dengan: php artisan test --filter=PengaduanTest
 */
class PengaduanTest extends TestCase
{
    use RefreshDatabase; // database di-reset otomatis tiap test

    /** Customer mengirim pengaduan -> tersimpan dengan status 'baru' dan milik dirinya. */
    public function test_customer_can_create_pengaduan(): void
    {
        $customer = User::factory()->create();
        $kategori = Kategori::factory()->create();

        $this->actingAs($customer)->post('/customer/pengaduan', [
            'jenis'       => 'keluhan',
            'kategori_id' => $kategori->id,
            'judul'       => 'Kopi terlalu pahit',
            'isi'         => 'Kopinya terlalu pahit untuk selera saya.',
        ])->assertRedirect('/customer/pengaduan');

        $this->assertDatabaseHas('pengaduan', [
            'user_id'     => $customer->id,
            'kategori_id' => $kategori->id,
            'status'      => 'baru',
        ]);
    }

    /** Foto opsional: kalau diunggah, file tersimpan di disk 'public'. */
    public function test_pengaduan_can_include_photo(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create();
        $kategori = Kategori::factory()->create();

        $this->actingAs($customer)->post('/customer/pengaduan', [
            'jenis'       => 'saran',
            'kategori_id' => $kategori->id,
            'judul'       => 'Tambah colokan',
            'isi'         => 'Tolong tambah colokan listrik di meja.',
            'foto'        => UploadedFile::fake()->image('meja.jpg'),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertExists(Pengaduan::firstOrFail()->foto);
    }

    /** Kategori yang tidak ada di database ditolak. */
    public function test_pengaduan_requires_valid_kategori(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->post('/customer/pengaduan', [
            'jenis'       => 'keluhan',
            'kategori_id' => 9999,
            'judul'       => 'Judul',
            'isi'         => 'Isi pengaduan yang cukup panjang.',
        ])->assertSessionHasErrors('kategori_id');
    }

    /** Customer tidak boleh membuka pengaduan milik customer lain. */
    public function test_customer_cannot_view_others_pengaduan(): void
    {
        $pemilik = User::factory()->create();
        $lain = User::factory()->create();
        $pengaduan = Pengaduan::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($lain)->get("/customer/pengaduan/{$pengaduan->id}")->assertNotFound();
        $this->actingAs($pemilik)->get("/customer/pengaduan/{$pengaduan->id}")->assertOk();
    }

    /** Customer tidak boleh masuk halaman admin. */
    public function test_customer_cannot_access_admin_pages(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/admin/pengaduan')->assertForbidden();
        $this->actingAs($customer)->get('/admin/kategori')->assertForbidden();
    }

    /** Admin membuka pengaduan 'baru' -> status otomatis jadi 'diproses'. */
    public function test_admin_opening_baru_pengaduan_makes_it_diproses(): void
    {
        $admin = User::factory()->admin()->create();
        $pengaduan = Pengaduan::factory()->create();

        $this->actingAs($admin)->get("/admin/pengaduan/{$pengaduan->id}")->assertOk();

        $this->assertSame('diproses', $pengaduan->fresh()->status);
    }

    /** Admin menanggapi -> tanggapan & status tersimpan, dan customer bisa melihatnya. */
    public function test_admin_can_respond_and_customer_sees_it(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $pengaduan = Pengaduan::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($admin)->post("/admin/pengaduan/{$pengaduan->id}/respond", [
            'status'    => 'selesai',
            'tanggapan' => 'Terima kasih, sudah kami perbaiki.',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengaduan', ['id' => $pengaduan->id, 'status' => 'selesai']);

        $this->actingAs($customer)->get("/customer/pengaduan/{$pengaduan->id}")
            ->assertSee('Terima kasih, sudah kami perbaiki.');
    }

    /** CRUD kategori: tambah, ubah, hapus. */
    public function test_admin_can_manage_kategori(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/kategori', ['nama' => 'Parkir'])
            ->assertRedirect('/admin/kategori');
        $kategori = Kategori::where('nama', 'Parkir')->firstOrFail();

        $this->actingAs($admin)->put("/admin/kategori/{$kategori->id}", ['nama' => 'Area Parkir'])
            ->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('kategori_pengaduan', ['id' => $kategori->id, 'nama' => 'Area Parkir']);

        $this->actingAs($admin)->delete("/admin/kategori/{$kategori->id}");
        $this->assertDatabaseMissing('kategori_pengaduan', ['id' => $kategori->id]);
    }

    /** Nama kategori tidak boleh kembar. */
    public function test_kategori_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Kategori::factory()->create(['nama' => 'Pelayanan']);

        $this->actingAs($admin)->post('/admin/kategori', ['nama' => 'Pelayanan'])
            ->assertSessionHasErrors('nama');
    }

    /** Kategori yang masih dipakai pengaduan tidak bisa dihapus. */
    public function test_kategori_in_use_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $pengaduan = Pengaduan::factory()->create();

        $this->actingAs($admin)->delete("/admin/kategori/{$pengaduan->kategori_id}")
            ->assertSessionHasErrors('kategori');

        $this->assertDatabaseHas('kategori_pengaduan', ['id' => $pengaduan->kategori_id]);
    }
}
