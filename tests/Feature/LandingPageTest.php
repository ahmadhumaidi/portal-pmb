<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_publicly_accessible(): void
    {
        $this->get(route('landing'))->assertOk()->assertSee('Langkah pertamamu')->assertSee('Masuk Portal');
    }

    public function test_guest_cannot_access_landing_page_editor(): void
    {
        $this->get(route('landing.admin.edit'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_update_landing_page(): void
    {
        $user = User::factory()->create();
        $data = LandingPage::defaults();
        $data['headline'] = 'Kuliah impianmu dimulai hari ini.';
        $data['registration_deadline'] = '2026-09-30';

        $this->actingAs($user)->put(route('landing.admin.update'), $data)->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('landing_pages', ['headline' => 'Kuliah impianmu dimulai hari ini.']);
        $this->get(route('landing'))->assertOk()->assertSee('Kuliah impianmu dimulai hari ini.');
    }
}
