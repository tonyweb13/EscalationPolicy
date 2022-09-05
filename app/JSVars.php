<?php
declare(strict_types=1);

namespace App;

use App\Models\{User, Rules};
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class JSVars
{
    protected static function getUserRules()
    {
        $rules = Rules::where('employee_no', Auth::user()->employee_no)->first();

        return $rules->rules;
    }

    protected static function getUserRoleId()
    {
        $user = User::where('employee_no', Auth::user()->employee_no)
            ->with('roles')
            ->first();

        return $user->roles[0]->name;
    }

    public static function getEPSettingsJSON(): string
    {
        $settings = [];
        $user = User::where('id', Auth::id());
        $settings['user'] = $user->with(['department', 'designation', 'address'])->first();
        $settings['ep_version'] = config('app.version', '1.0.0.0');

        $settings['rule'] = self::getUserRules();
        $settings['role'] = self::getUserRoleId();

        $encoded = json_encode($settings);
        $encoded = addslashes($encoded);

        return $encoded;
    }

    public static function getChannelName()
    {
        return ChannelNames::getForUser(Auth::user()->employee_no);
    }
}
