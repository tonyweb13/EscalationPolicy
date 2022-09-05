<?php
declare(strict_types=1);

namespace Tests;

use Exception;
use Facebook\WebDriver\Exception\InvalidElementStateException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\{WebDriverBy, WebDriverSelect};
use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use App\Models\{User, Rules, Role};
use RuntimeException;
use Illuminate\Support\Facades\Hash;
use DB;

class SharedBrowserTest extends DuskTestCase
{
    use InteractsWithDatabase;

    protected $connectionsToTransact = ['mysql-customdb1'];

    const DEFAULT_PASSWORD = '123456';
    const DEFAULT_USERNAME = '20189999';
    const ADMIN = "Super Admin Access";
    public static $dusk_running = false;

    public function deleteUser($employee_no = self::DEFAULT_USERNAME)
    {
        $user_to_delete = User::where('employee_no', $employee_no);
        Rules::where('employee_no', self::DEFAULT_USERNAME)->delete();
        $user_to_delete->delete();
    }

    public function createUser($employee_no = self::DEFAULT_USERNAME)
    {
        $user = factory(User::class)->create(['employee_no' => $employee_no]);
        $role = Role::where('name', self::ADMIN)->select('id')->first();
        $user->roles()->attach($role);
        DB::table('rules')->insert([
            'employee_no' => $user->employee_no,
            'rules' => json_encode(config('constants.Rules_Admin'))
        ]);

        return $user;
    }
}
