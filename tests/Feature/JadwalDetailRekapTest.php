<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Matakuliah;
use App\Models\Peminatan;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JadwalDetailRekapTest extends TestCase
{
    use RefreshDatabase;

    protected $dosen;
    protected $mahasiswa;
    protected $admin;
    protected $ruangan;
    protected $matakuliah;
    protected $jadwal;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Users
        $this->dosen = Pengguna::create([
            'nama' => 'Dosen Test',
            'nomor_identitas' => 'DOSEN001',
            'email' => 'dosen@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'dosen',
        ]);

        $this->mahasiswa = Pengguna::create([
            'nama' => 'Mhs Test',
            'nomor_identitas' => 'MHS001',
            'email' => 'mhs@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'mahasiswa',
        ]);

        $this->admin = Pengguna::create([
            'nama' => 'Admin Test',
            'nomor_identitas' => 'ADMIN001',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'admin',
        ]);

        // Create Room
        $this->ruangan = Ruangan::create([
            'nama_ruangan' => 'Lab Test',
            'kapasitas' => 40,
            'latitude' => -7.4245,
            'longitude' => 109.2305,
            'radius_meter' => 50,
        ]);

        // Create Course
        $this->matakuliah = Matakuliah::create([
            'kode_matkul' => '#12',
            'nama_matkul' => 'Test Course',
            'sks' => 3,
            'semester' => 6,
        ]);

        // Create Schedule
        $this->jadwal = Jadwal::create([
            'matakuliah_id' => $this->matakuliah->id,
            'ruangan_id' => $this->ruangan->id,
            'dosen_id' => $this->dosen->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:30:00',
            'metode' => 'luring',
        ]);

        // Approve Course enrollment for Student
        Peminatan::create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'matakuliah_id' => $this->matakuliah->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_mahasiswa_can_get_jadwal_detail_with_16_sessions()
    {
        Sanctum::actingAs($this->mahasiswa, ['*']);

        $response = $this->getJson("/api/mahasiswa/jadwal/{$this->jadwal->id}/detail");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(16, 'data.pertemuan');

        // Check meeting labels
        $pertemuans = $response->json('data.pertemuan');
        $this->assertEquals('Pertemuan 1', $pertemuans[0]['label']);
        $this->assertEquals('UTS', $pertemuans[7]['label']);
        $this->assertEquals('Pertemuan 8', $pertemuans[8]['label']);
        $this->assertEquals('UAS', $pertemuans[15]['label']);
    }

    public function test_dosen_can_get_jadwal_detail_with_16_sessions()
    {
        Sanctum::actingAs($this->dosen, ['*']);

        $response = $this->getJson("/api/dosen/jadwal/{$this->jadwal->id}/detail");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(16, 'data.pertemuan');

        $pertemuans = $response->json('data.pertemuan');
        $this->assertEquals(1, $pertemuans[0]['total_mahasiswa']); // 1 approved student
        $this->assertEquals(0, $pertemuans[0]['jumlah_hadir']);
    }

    public function test_admin_can_get_jadwal_rekap_grid()
    {
        Sanctum::actingAs($this->admin, ['*']);

        // Record some dummy attendance
        Presensi::create([
            'jadwal_id' => $this->jadwal->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'pertemuan_ke' => 1,
            'tanggal' => '2026-02-23',
            'jam_masuk' => '08:05:00',
            'status' => 'hadir',
        ]);

        $response = $this->getJson("/api/admin/jadwal/{$this->jadwal->id}/rekap");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(16, 'data.pertemuan')
            ->assertJsonCount(1, 'data.rekap');

        $rekap = $response->json('data.rekap.0');
        $this->assertEquals('Mhs Test', $rekap['mahasiswa']['nama']);
        $this->assertEquals('hadir', $rekap['kehadiran'][0]['status']);
        $this->assertEquals(1, $rekap['ringkasan']['hadir']);
    }
}
