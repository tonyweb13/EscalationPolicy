<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use Auth;
use App\Models\Admin\IncidentReport;
use App\Models\{
    Respondent,
    AcknowledgementReceipt
};

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('acknowledgeEmailRespondent');
        $this->middleware('auth')->except('acknowledgementReceipt');
    }

    public function index()
    {
        return view('index');
    }

    private function acknowledgeCheck($ir_number)
    {
        $respondent_check = Respondent::where('ir_number', $ir_number)->first();

        return $respondent_check->email_acknowledge_date;
    }

    public function acknowledgeEmailRespondent($ir_number)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $check = $this->acknowledgeCheck($ir_number);
        if ($check == null) {
            $respondent = Respondent::where('ir_number', $ir_number);
            $respondent->update([
                'email_acknowledge_date' => Carbon::now(),
            ]);
            $status = ['success' => 'Incident Report #'.$ir_number.' has been Successfully Acknowledged.'];
        } else {
            $status = ['error' => 'Incident Report #'.$ir_number.'  is Already Acknowledged.'];
        }

        return view('acknowledge', $status);
    }

    public function acknowledgementReceipt($id)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $acknowledge_check = AcknowledgementReceipt::where('id', $id)->first();

        if ($acknowledge_check->acknowledgement_confirmation == null) {
            AcknowledgementReceipt::where('id', $id)->update(['acknowledgement_confirmation' => 1]);
            $status = ['success' => 'Acknowledgement Receipt Success!'];
        } else {
            $status = ['error' => 'Already Acknowledged.'];
        }
        
        return view('acknowledge', $status);
    }

    public function acknowledgementManager($id, $ir_number)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $ir_check = IncidentReport::where('id', $id)->first();

        if ($ir_check->manager_acknowledge == '1') {
            IncidentReport::where('id', $id)->update(['manager_acknowledge' => Carbon::now()->format("Y-m-d")]);
            $status = ['success' => 'Incident Report #'.$ir_number.' has been Successfully Acknowledged.'];
        } else {
            $status = ['error' => 'Incident Report #'.$ir_number.'  is Already Acknowledged.'];
        }
        
        return view('acknowledge', $status);
    }
}
