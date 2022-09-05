<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Models\Note;
use App\Jobs\EmailNote;
use App\Models\Respondent;
use App\Models\User;

class NotesController extends Controller
{
    public function index(): JsonResponse
    {
        $note = Respondent::all();

        return response()->json($note);
    }

    public function create(Request $request)
    {
        $note = new Note;
        $note->complainant_id = $request->complainant_id;
        $note->notes = $request->notes;

        if ($request->reply_to == "invite" && $request->invite_user_id) {
            $note->hr_user_id = Auth()->id();
            $note->complainant_user_id = $request->complainant_user_id;
            $note->respondent_user_id = $request->respondent_user_id;
            $note->invite_user_id = $request->invite_user_id;
            $note->reply_to = $request->reply_to;
            $note->noted_by = "hr_bp";
            $note->save();

            if ($request->invite_user_id) {
                $email_address = $this->findEmailAddress($request->invite_user_id);
                if ($email_address) {
                    EmailNote::dispatch($request->ir_number, $email_address);
                }
            }
        } elseif ($request->noted_by == "invite" && $request->invite_user_id) {
            $note->hr_user_id = $request->hr_user_id;
            $note->complainant_user_id = $request->complainant_user_id;
            $note->respondent_user_id = $request->respondent_user_id;
            $note->invite_user_id = Auth()->id();
            $note->reply_to = "hr_bp";
            $note->noted_by = "invite";
            $note->save();

            // EmailNote::dispatch($request->ir_number, $request->hr_user_id);
            if ($request->hr_user_id) {
                $email_address = $this->findEmailAddress($request->hr_user_id);
                if ($email_address) {
                    EmailNote::dispatch($request->ir_number, $email_address);
                }
            }
        } elseif ($request->noted_by == "complainant") {
            $note->hr_user_id = $request->hr_user_id;
            $note->complainant_user_id = Auth()->id();
            $note->respondent_user_id = $request->respondent_user_id;
            $note->reply_to = "hr_bp";
            $note->noted_by = "complainant";
            $note->save();

            // EmailNote::dispatch($request->ir_number, $request->hr_user_id);
            if ($request->hr_user_id) {
                $email_address = $this->findEmailAddress($request->hr_user_id);
                if ($email_address) {
                    EmailNote::dispatch($request->ir_number, $email_address);
                }
            }
        } elseif ($request->noted_by == "respondent") {
            $note->hr_user_id = $request->hr_user_id;
            $note->respondent_user_id = Auth()->id();
            $note->complainant_user_id = $request->complainant_user_id;
            $note->reply_to = "hr_bp";
            $note->noted_by = "respondent";
            $note->save();

            // EmailNote::dispatch($request->ir_number, $note->hr_user_id);
            if ($note->hr_user_id) {
                $email_address = $this->findEmailAddress($note->hr_user_id);
                if ($email_address) {
                    EmailNote::dispatch($request->ir_number, $email_address);
                }
            }
        } elseif ($request->noted_by == "hr_bp") {
            $note->hr_user_id = Auth()->id();
            $note->complainant_user_id = $request->complainant_user_id;
            $note->respondent_user_id = $request->respondent_user_id;
            $note->reply_to = $request->reply_to;
            $note->noted_by = "hr_bp";
            $note->save();

            if ($request->reply_to == "complainant") {
                // EmailNote::dispatch($request->ir_number, $request->complainant_user_id);
                if ($request->complainant_user_id) {
                    $email_address = $this->findEmailAddress($request->complainant_user_id);
                    if ($email_address) {
                        EmailNote::dispatch($request->ir_number, $email_address);
                    }
                }
            } elseif ($request->reply_to == "respondent") {
                // EmailNote::dispatch($request->ir_number, $request->respondent_user_id);
                if ($request->respondent_user_id) {
                    $email_address = $this->findEmailAddress($request->respondent_user_id);
                    if ($email_address) {
                        EmailNote::dispatch($request->ir_number, $email_address);
                    }
                }
            }
        }

        return $note;
    }

    public function notesRetrieve(int $comp_id, int $comp_user_id, int $resp_user_id): JsonResponse
    {
        $note = Note::with(['complainant_user', 'respondent_user', 'hr_user', 'invite_user'])
        ->where('complainant_id', $comp_id)
        ->where('complainant_user_id', $comp_user_id)
        ->where('respondent_user_id', $resp_user_id)
        ->orderBy('created_at')
        ->get();

        return response()->json($note);
    }

    private function findEmailAddress(int $user_id)
    {
        $user = User::findOrFail($user_id);
        return $user['email_address'];
    }

    public function countNotes()
    {
        return $this->getNotesReplied()->count();
    }

    public function getNotesReplied()
    {
        $userid = Auth()->id();
        $roles = auth()->user()->roles[0]->name;

        $notes = Respondent::with('complainant.notify_notes')
        ->whereHas('complainant.notify_notes', function ($query) use ($userid, $roles) {

            if ($roles == "Super Admin Access" || $roles == "HR Access" || $roles == "HR Admin Access") {
                $query->where('reply_to', 'hr_bp');
                $query->where('hr_user_id', $userid);

            } else {
                $query->where('reply_to', 'respondent');
                $query->where('respondent_user_id', $userid);

                $query->OrWhere('reply_to', 'complainant');
                $query->where('complainant_user_id', $userid);
                
                $query->OrWhere('reply_to', 'invite');
                $query->where('invite_user_id', $userid);
            }
        })
        ->where('status_id', '!=', 4)
        ->orderBy('created_at', 'desc')
        ->get();

        $data_return = [];
        foreach ($notes as $key => $value) {
            
            if (($roles == 'Super Admin Access' || $roles == 'HR Admin Access' || $roles == 'HR Access') 
            && $value->complainant->notify_notes->noted_by != 'hr_bp') {
                    $getNote = $value;

            } else if (($roles == 'Regular User Access' || $roles == 'Regular Supervisor Access') 
            && $value->complainant->notify_notes->noted_by == 'hr_bp') {
                $getNote = $value;
            }
            array_push($data_return, $getNote);
        }

        $data_to_collection = Collection::make($data_return);
        return $data_to_collection->filter();
    }
}
