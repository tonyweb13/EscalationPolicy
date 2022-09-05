<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Carbon\Carbon;
use App\Jobs\EmailEGreetings;
use App\Models\EGreeting;

class EGreetingsController extends Controller
{
    public function index(): JsonResponse
    {
        if (Str::contains(Auth()->user()->designation->name, 'Engagement') == 1) {
            $list = EGreeting::all();
            $this->refreshEmailGreetings();
        } else {
            $list = EGreeting::where('employee_number', Auth()->user()->employee_no)
            ->where('is_sent', 1)
            ->get();
        }
        return response()->json($list);
    }

    public function create(Request $request)
    {
        $egreetings = new EGreeting;
        $egreetings->template1 = $request->template1;
        $egreetings->template2 = $request->template2;
        // $egreetings->profile_pic = $request->profile_pic;
        $egreetings->employee_number = $request->employee_number;
        $egreetings->employee_name = $request->employee_name;
        $egreetings->email_address = $request->email_address;
        $egreetings->birthday = $request->birthday;
        $egreetings->save();
        return $egreetings;
    }

    public function show(int $id, Request $request)
    {
        $egreetings = EGreeting::updateOrCreate(['id' => $id], $request->all());
        return $egreetings;
    }

    public function edit(int $id)
    {
        $egreetings = EGreeting::where('id', $id)->first();
        return $egreetings;
    }

    public function destroy(int $id)
    {
        $egreetings = EGreeting::findOrFail($id)->delete();
        return response()->json($egreetings);
    }

    public function attachTemplate1(Request $request)
    {
        if ($request->file('template1')) {
            $storage = 'egreetings/template1/'
                        . $request->employee_number
                        . '/' . Carbon::today()->format('Y-m-d')
                        . '/';
            $uploaded = [];
            foreach ($request->file('template1') as $file) {
                $filename = $file->getClientOriginalName();
                Storage::disk('public_uploads')->putFileAs($storage, new File($file),$filename,'public');

                $uploaded[] = 'template1/'
                            . $request->employee_number . '/'
                            . Carbon::today()->format('Y-m-d')
                            . '/' . $filename;
            }
            Storage::disk('public_uploads')->files($storage);
            return response(['uploaded' => $uploaded]);
        }
    }

    public function attachTemplate2(Request $request)
    {
        if ($request->file('template2')) {
            $storage = 'egreetings/template2/'
                        . $request->employee_number
                        . '/' . Carbon::today()->format('Y-m-d')
                        . '/';
            $uploaded = [];
            foreach ($request->file('template2') as $file) {
                $filename = $file->getClientOriginalName();
                Storage::disk('public_uploads')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'template2/'
                            . $request->employee_number . '/'
                            . Carbon::today()->format('Y-m-d')
                            . '/' . $filename;
            }
            Storage::disk('public_uploads')->files($storage);
            return response(['uploaded' => $uploaded]);
        }
    }

    // public function attachProfilePic(Request $request)
    // {
    //     if ($request->file('profile_pic')) {
    //         $storage = 'egreetings/profile_pic/'
    //                     . $request->employee_number
    //                     . '/' . Carbon::today()->format('Y-m-d')
    //                     . '/';
    //         $uploaded = [];
    //         foreach ($request->file('profile_pic') as $file) {
    //             $filename = $file->getClientOriginalName();
    //             Storage::disk('public_uploads')->putFileAs($storage, new File($file), $filename, 'public');

    //             $uploaded[] = 'profile_pic/'
    //                         . $request->employee_number . '/'
    //                         . Carbon::today()->format('Y-m-d')
    //                         . '/' . $filename;
    //         }
    //         Storage::disk('public_uploads')->files($storage);
    //         return response(['uploaded' => $uploaded]);
    //     }
    // }

    public function refreshEmailGreetings() 
    {        
        $today = Carbon::today()->format('Y-m-d');
        $emailGreets = EGreeting::get();
        foreach ($emailGreets as $emailGreet) {
            if ($emailGreet->birthday <= $today && $emailGreet->is_sent == 0) {
                EmailEGreetings::dispatch(
                    $emailGreet->employee_name,
                    $emailGreet->email_address
                );
                EGreeting::where('employee_number', $emailGreet->employee_number)
                ->where('birthday', $emailGreet->birthday)
                ->update(['is_sent' => 1]);
            }
        }
    }
}
