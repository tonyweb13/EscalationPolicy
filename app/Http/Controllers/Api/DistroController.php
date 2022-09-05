<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\{
    DistroCategory,
    DistroEmail,
    User
};

class DistroController extends Controller
{
    // public function index()
    // {
    //     $distro = DistroCategory::with(['distro_categories.distro_user'])->get();
    //     return response()->json($distro);
    // }

    public function index()
    {
        $distro = DistroCategory::with(['distro_categories.distro_user'])
                ->paginate(10);
        return $distro;
    }

    public function create(Request $request)
    {
        $distro = new DistroCategory;
        $distro->distro_name = $request->distro_name;

        if ($distro->save()) {
            if ($request->user_id) {
                foreach ($request->user_id as $user_email) {
                    $getEmails = User::where('id', $user_email)->get(['email_address']);

                    foreach ($getEmails as $getEmail) {
                        $distroEmail = new DistroEmail;
                        $distroEmail->distro_category_id = $distro->id;
                        $distroEmail->user_id = $user_email;
                        $distroEmail->email_address = $getEmail['email_address'];
                        $distroEmail->save();
                    }
                }
            }
        }
        return $distro;
    }

    public function show(int $id, Request $request)
    {        
        DistroCategory::where('id', $id)->update(['distro_name' => $request->distro_name]);

        if ($request->user_id) {
            $exist_user_id = DistroEmail::where('distro_category_id', $id)
                ->get(['id', 'user_id']);

            $distroEmails_ids = [];
            $distroEmails_user = [];
            foreach ($exist_user_id as $exist_user) {
                $distroEmails_ids[] = $exist_user['id'];
                $distroEmails_user[] = $exist_user['user_id'];
            }

            $distro_email_request = [];
            foreach ($request->user_id as $request_user) {
                $distro_email_request[] = $request_user;
            }

            if (empty($distroEmails_user)) {
                foreach ($request->user_id as $getAddedDistroEmail) {
                    $getEmail = User::where('id', $getAddedDistroEmail)->get(['email_address']);

                    DistroEmail::create([
                        'distro_category_id ' => $id,
                        'email_address' => $getEmail[0]['email_address'],
                        'user_id' => $getAddedDistroEmail
                    ]);
                }
            } else {
                $distro_email_add = array_diff($distro_email_request, $distroEmails_user);
                $distro_email_delete = array_diff($distroEmails_user, $distro_email_request);

                /* newly added */
                if (!empty($distro_email_add)) {
                    foreach ($distro_email_add as $add_distro_user) {
                        $getEmail = User::where('id', $add_distro_user)->get(['email_address']);

                        $distroEmails = new DistroEmail;
                        $distroEmails->distro_category_id  = $id;
                        $distroEmails->email_address = $getEmail[0]['email_address'];
                        $distroEmails->user_id = $add_distro_user;
                        $distroEmails->save();
                    }
                }
                if (!empty($distro_email_delete)) {
                    foreach ($distro_email_delete as $delete_distro_user) {
                        DistroEmail::where('distro_category_id', $id)
                            ->where('user_id', (int)$delete_distro_user)
                            ->delete();
                    }
                }
            }
        } else {
            DistroEmail::where('distro_category_id', $id)
            ->where('user_id', (int)$request->user_id)
            ->delete();
        }
    }

    public function dropdown()
    {
        $distro = DistroCategory::select('id as value', DB::raw('distro_name AS text'))->get();
        return response()->json($distro);
    }

    public function searchQuery($searchKey)
    {
        $distro = DistroCategory::with(['distro_categories.distro_user'])
                ->where('distro_name', "LIKE", '%'.$searchKey.'%')
                ->get();

        return response()->json($distro);
    }
}
