<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LoginTest extends TestCase
{
    const DEFAULT_PASSWORD = 123456;

    private $user;

    protected function setup()
    {
        parent::setup();

        $this->user = factory(User::class)->create();
    }

    public function testLoginFailed()
    {
        $this->get(route('login'))
        ->assertStatus(200);

        $this->post(route('login'), [
            'employee_no' => $this->user->employee_no,
            'password' => '654321',
        ])
        ->assertStatus(302)
        ->assertRedirect(route('login'));

        $this->get(route('login'))->assertSee('These credentials do not match our records.');
        $this->get(route('login'))->assertStatus(200);
    }

    public function testLoginPass()
    {
        $this->post(route('login'), [
            'employee_no' => $this->user->employee_no,
            'password' => self::DEFAULT_PASSWORD,
        ])
        ->assertRedirect(route('dashboard'));

        $this->get(route('logout'))->assertStatus(302);
        $this->get(route('login'))->assertStatus(200);
    }
}
