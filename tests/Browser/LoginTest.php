<?php
declare(strict_types=1);

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\SharedBrowserTest;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Facebook\WebDriver\WebDriverBy;
use App\Models\User;

class LoginTest extends SharedBrowserTest
{
    const DEFAULT_PASSWORD = 123456;
    const EMPLOYEE_NO = '20189999';

    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteUser();
    }

    public function testSuccess()
    {
        $this->browse(function ($browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/')
                ->type('employee_no', self::EMPLOYEE_NO)
                ->type('password', self::DEFAULT_PASSWORD)
                ->press('Login')
                ->waitForText('Dashboard', 15)
                ->visit('/')
                ->assertSee('Dashboard')
                ->logout();
        });
    }

    public function testFailed()
    {
        $this->browse(function ($browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/')
                ->type('employee_no', '20000000')
                ->type('password', '123456')
                ->press('Login')
                ->waitForText('These credentials do not match our records', 10)
                ->assertSee('These credentials do not match our records.');
        });
    }
}
