<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Jobs\EmailAcknowledgementReceipt;
use App\Http\Controllers\Api\ValidatorController;
use Exception;
use App\Models\{
    AcknowledgementReceipt,
    User
};

class AcknowledgementReceiptController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = auth()->user()->roles[0]->name;
        $designation = auth()->user()->designation->name;
        $acknowledgementUser = AcknowledgementReceipt::where('user_id', Auth()->user()->id)->first();

        $AcknowledgementReceipt = [];
        if ($acknowledgementUser->user_id) {
            $AcknowledgementReceipt = AcknowledgementReceipt::with(['employee_user', 'supervisor_user', 'manager_user'])
            ->where('user_id', Auth()->user()->id)
            ->orderBy('ticket_number', 'desc')
            ->get();

        } else if ($roles == "Super Admin Access" || $roles == "HR Admin Access" || Str::contains($designation, 'Engagement') == 1) {
            $AcknowledgementReceipt = AcknowledgementReceipt::with(['employee_user', 'supervisor_user', 'manager_user'])
            ->orderBy('ticket_number', 'desc')
            ->get();
        }

        return response()->json($AcknowledgementReceipt);
    }

    public function create(Request $request)
    {
        $getUser = User::findOrFail($request->user_id);

        $validator = ValidatorController::AcknowledgementReceiptCreateValidator($request);
        $validatorEmail = ValidatorController::AcknowledgementReceiptEmailValidator($getUser);

        if (!$validator->fails() && !$validatorEmail->fails()) {
            $AcknowledgementReceipt = new AcknowledgementReceipt;
            $AcknowledgementReceipt->creator_id = Auth()->user()->id;
            $AcknowledgementReceipt->user_id = $request->user_id;
            $AcknowledgementReceipt->email_address = $getUser->email_address;
            $AcknowledgementReceipt->supervisor_id = $request->supervisor_id;
            $AcknowledgementReceipt->manager_id = $request->manager_id;
            $AcknowledgementReceipt->incentive = $request->incentive;
            $AcknowledgementReceipt->prize = $request->prize;
            $AcknowledgementReceipt->item = $request->item;
            $AcknowledgementReceipt->date_received = $request->date_received;
            $AcknowledgementReceipt->save();

            AcknowledgementReceipt::where('id', $AcknowledgementReceipt->id)->update([
                'ticket_number' => STR_PAD($AcknowledgementReceipt->id, 9, '0', STR_PAD_LEFT)
            ]);

            if ($getUser && $AcknowledgementReceipt->id) {
                EmailAcknowledgementReceipt::dispatch(
                    $AcknowledgementReceipt->id,
                    $getUser->first_name. " " . $getUser->last_name,
                    $getUser->email_address,
                    $request->date_received
                );
            }
            return $AcknowledgementReceipt;

        } else {
            throw new Exception($validator->messages());
            throw new Exception($validatorEmail->messages());
        }
    }

    public function show(int $id, Request $request)
    {
        $AcknowledgementReceipt = AcknowledgementReceipt::updateOrCreate(['id' => $id], $request->all());

        return $AcknowledgementReceipt;
    }

    public function edit(int $id)
    {
        $AcknowledgementReceipt = AcknowledgementReceipt::all()->where('id', $id)->first();

        return $AcknowledgementReceipt;
    }

    public function destroy(int $id)
    {
        $AcknowledgementReceipt = AcknowledgementReceipt::findOrFail($id)->delete();

        return response()->json($AcknowledgementReceipt);
    }

    public function getSupervisorManager(int $id)
    {
        $getSupervision = User::with(['employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign'])->findOrFail($id);

        $getData = [];

        if ($getSupervision->employee_supervisor->supervisor_assign) {
            $getData['supervisor'] = $getSupervision->employee_supervisor->supervisor_assign->first_name 
                                    . " " . $getSupervision->employee_supervisor->supervisor_assign->last_name;

            $getData['supervisor_id'] = $getSupervision->employee_supervisor->supervisor_assign->id;
        }

        if ($getSupervision->employee_supervisor->supervisor_assign) {
            $getData['supervisor'] = $getSupervision->employee_supervisor->supervisor_assign->first_name 
                                    . " " . $getSupervision->employee_supervisor->supervisor_assign->last_name;

            $getData['supervisor_id'] = $getSupervision->employee_supervisor->supervisor_assign->id;
        }

        if ($getSupervision->employee_supervisor->supervisor_assign->employee_supervisor->supervisor_assign) {
            $getData['manager'] = $getSupervision->employee_supervisor->supervisor_assign->employee_supervisor->supervisor_assign->first_name 
                                    . " " . $getSupervision->employee_supervisor->supervisor_assign->employee_supervisor->supervisor_assign->last_name;

            $getData['manager_id'] = $getSupervision->employee_supervisor->supervisor_assign->employee_supervisor->supervisor_assign->id;
        }

        return $getData;
    }
}
