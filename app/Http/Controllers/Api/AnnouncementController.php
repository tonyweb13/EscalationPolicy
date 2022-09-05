<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Carbon\Carbon;
use App\Events\AnnouncementCreated;
use App\Models\{
    Announcement,
    AnnouncementRecipient,
    AnnouncementAttachment,
    DistroEmail
};


class AnnouncementController extends Controller
{
    public function index() : JsonResponse
    {
        $userid = Auth()->user()->id;
        $announcement = AnnouncementRecipient::with(['announce.sender', 'announce_attached'])
        ->where('user_id', $userid)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($announcement);
    }

    public function create(Request $request)
    {
        $announcement = new Announcement;
        $announcement->sender_id = $request->sender_id;
        $announcement->subject = $request->subject;
        $announcement->content = $request->content;
        $announcement->effective_date = $request->effective_date;

        if ($announcement->save()) {
            if ($request->to) {
                foreach ($request->to as $tos) {
                    $recipient = new AnnouncementRecipient;
                    $recipient->announcement_id = $announcement->id;
                    $recipient->distro_category_id = '';
                    $recipient->user_id = $tos;
                    $recipient->save();
                }
            }

            if ($request->distro) {
                foreach ($request->distro as $distros) {
                    $distro_users = DistroEmail::where('distro_category_id', $distros)
                    ->get(['user_id', 'distro_category_id']);

                    foreach ($distro_users as $distroUsers) {
                        $recipient = new AnnouncementRecipient;
                        $recipient->announcement_id = $announcement->id;
                        $recipient->distro_category_id = $distroUsers['distro_category_id'];
                        $recipient->user_id = $distroUsers['user_id'];
                        $recipient->save();
                    }
                }
            }

            /* Remove Duplicates */
            $duplicateRecords = AnnouncementRecipient::select('user_id')
            ->selectRaw('count(`user_id`) as `cntuserid`')
            ->groupBy('user_id')
            ->having('cntuserid', '>', 1)
            ->where('announcement_id', $announcement->id)
            ->first();

            AnnouncementRecipient::where('announcement_id', $announcement->id)
            ->where('user_id', $duplicateRecords->user_id)
            ->take(1)
            ->delete();

            if ($request->attachments) {
                foreach ($request->attachments as $attachment) {
                    $attach = new AnnouncementAttachment;
                    $attach->announcement_id = $announcement->id;
                    $attach->attachments = json_encode($attachment);
                    $attach->save();
                }
            }
        }

        broadcast(new AnnouncementCreated($announcement));

        return $announcement;
    }

    public function attachFile(Request $request)
    {
        if ($request->file('pics')) {
            $storage = 'public/attachments/announcement/'
                        . auth()->user()->employee_no
                        . '/' . Carbon::today()->format('Y-m-d')
                        . '/';

            $uploaded = [];
            foreach ($request->file('pics') as $file) {
                $filename = $file->getClientOriginalName();
                Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'announcement/'
                            . auth()->user()->employee_no . '/'
                            . Carbon::today()->format('Y-m-d')
                            . '/' . $filename;
            }

            Storage::disk('local')->files($storage);

            return response(['uploaded' => $uploaded ]);
        }
    }

    public function updateAcknowledge(Request $request, $id)
    {
        $acknowledge = AnnouncementRecipient::where('announcement_id', $id)
        ->where('user_id', $request->user_id)
        ->where('distro_category_id', $request->distro_category_id)
        ->update(['acknowledged_at' => Carbon::now()]);

        return $acknowledge;
    }

    public function indexCompliance()
    {
        $userid = Auth()->user()->id;
        $roles = auth()->user()->roles[0]->name;

        if ($roles == "Super Admin Access" ||  $roles == "HR Admin Access") {
            $announcement = Announcement::with('sender', 'compliance.recipient.designation', 
            'compliance.recipient.office_location',
            'compliance.distro', 'compliance.announce_attached')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        } else {
            $announcement = Announcement::with('sender', 'compliance.recipient', 
            'compliance.distro', 'compliance.announce_attached')
            ->where('sender_id', $userid)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }

        $data_return = [];
        foreach ($announcement as $val1) {
            $acknowledge_sum = 0;

            foreach ($val1->compliance as $val2) {
                $val2['acknowledge_count'] = 0;
                if (!empty($val2->acknowledged_at)) {
                    $val2['acknowledge_count'] = 1;
                }

                $acknowledge_sum += $val2['acknowledge_count'];
                array_push($data_return, $val2);
            }

            $val1['acknowledge_percentage'] = round((($acknowledge_sum / count($val1->compliance)) * 100), 0) . '%';
            array_push($data_return, $val1);
        }

        return response()->json($announcement);
    }

    public function complianceDownload(int $id)
    {
        $announcement = Announcement::with('compliance.recipient', 'compliance.distro', 'compliance.announce_attached')
        ->findOrFail($id);
        return $announcement;
    }

    public function destroy(int $id)
    {
        AnnouncementAttachment::where('announcement_id', $id)->delete();
        AnnouncementRecipient::where('announcement_id', $id)->delete();
        Announcement::findOrFail($id)->delete();

        return response()->json("ok");
    }

    public function sentAnnouncement()
    {
        $roles = auth()->user()->roles[0]->name;

        if ($roles == "Super Admin Access" ||  $roles == "HR Admin Access") {
            $announcement = Announcement::with('sender', 'compliance.recipient', 
            'compliance.distro', 'compliance.announce_attached')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        } else {
            $announcement = Announcement::with('sender', 'compliance.recipient', 
            'compliance.distro', 'compliance.announce_attached')
            ->where('sender_id', Auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }

        return response()->json($announcement);
    }

    public function searchQueryCompliance($searchKey)
    {
        $userid = Auth()->user()->id;
        $roles = auth()->user()->roles[0]->name;

        $announcement = Announcement::with('sender', 'compliance.recipient', 
        'compliance.distro', 'compliance.announce_attached')
            ->where('subject', "LIKE", '%'.$searchKey.'%');

        if ($roles == "Super Admin Access" ||  $roles == "HR Admin Access") {
            $announcement =  $announcement->orderBy('created_at', 'desc')->get();

        } else {
            $announcement = $announcement->where('sender_id', $userid)
            ->orderBy('created_at', 'desc')
            ->get();
        }

        $data_return = [];
        foreach ($announcement as $val1) {
            $acknowledge_sum = 0;

            foreach ($val1->compliance as $val2) {
                $val2['acknowledge_count'] = 0;
                if (!empty($val2->acknowledged_at)) {
                    $val2['acknowledge_count'] = 1;
                }

                $acknowledge_sum += $val2['acknowledge_count'];
                array_push($data_return, $val2);
            }

            $val1['acknowledge_percentage'] = round((($acknowledge_sum / count($val1->compliance)) * 100), 0) . '%';
            array_push($data_return, $val1);
        }

        return response()->json($announcement);
    }

    public function searchQuerySent($searchKey)
    {
        $roles = auth()->user()->roles[0]->name;

        $announcement = Announcement::with('sender', 'compliance.recipient', 
        'compliance.distro', 'compliance.announce_attached')
        ->where('subject', "LIKE", '%'.$searchKey.'%');

        if ($roles == "Super Admin Access" ||  $roles == "HR Admin Access") {
            $announcement = $announcement->orderBy('created_at', 'desc')->get();

        } else {
            $announcement =  $announcement->where('sender_id', Auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        }

        return response()->json($announcement);
    }
}
