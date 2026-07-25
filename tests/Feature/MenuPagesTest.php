<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_pages_are_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('staff.index'))->assertStatus(200);
        $this->get(route('mahasiswa.index'))->assertStatus(200);
        $this->get(route('berkas.index'))->assertStatus(200);
        $this->get(route('pembayaran.index'))->assertStatus(200);
        $this->get(route('hasil.index'))->assertStatus(200);
        $this->get(route('laporan.index'))->assertStatus(200);
        $this->get(route('pengaturan.index'))->assertStatus(200);
    }
}
