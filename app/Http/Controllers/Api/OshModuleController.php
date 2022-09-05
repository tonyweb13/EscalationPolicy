<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\EmailOsh;
use Carbon\Carbon;
use App\Models\OshModule;
use Illuminate\Support\Facades\Auth;

class OshModuleController extends Controller
{
    public function index()
    {
        $list = OshModule::with(['employee_user.designation'])->get();
        return $list;
    }

    public function oshSaveFinish()
    {
        $osh = new OshModule;
        $osh->user_id = Auth()->user()->id;
        $osh->date_finish = Carbon::now()->setTimezone('Asia/Manila');
        $osh->save();
        if($osh->save()) {
            EmailOsh::dispatch(
                Auth()->user()->first_name. " " . Auth()->user()->last_name,
                Auth()->user()->email_address
            );
        }
    }

    public function oshEmployeeChecking()
    {
        $oshChecking = OshModule::where('user_id', Auth()->user()->id)->first();
        if ($oshChecking->user_id) {
            $oshChecking = "false";
        } else {
            $oshChecking = "true";
        }
        return $oshChecking;
    }
}
