<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Respondent;
use App\Models\Admin\{
    IncidentReport,
    Article297Terminable
};
use App\Models\Admin\Settings\Coc\IncidentReportResolution;

class PreviewDaController extends Controller
{
    public function edit(int $id)
    {
        $incident_report = IncidentReport::with(['irr'])->findOrFail($id);

        if ($incident_report->final_irr_multiple) {
            $incident_report->final_irr_multiple_new = json_decode($incident_report->final_irr_multiple, true);
            $incident_report->final_instance_new = json_decode($incident_report->final_instance, true);
        }

        /* suspension date multiple */
        $suspension_date_multi =  json_decode($incident_report->suspension_date);
        if ($suspension_date_multi) {
            $incident_report->suspension_date_multi = $suspension_date_multi;
        }

        $instance_dropdown = $this->dropdownInstance();
        $irr_dropdown = $this->dropdownIrr();
        $daStatusStageCase_dropdown = $this->dropdownDaStatusStageCase();
        $respondent = Respondent::where('ir_id', $id)->first();
        $get_da_status_stage_case = $respondent->da_status_stage_case;

        return view('pdf.generate.preview.irr_prev_edit', compact('incident_report', 
        'instance_dropdown', 'irr_dropdown', 'daStatusStageCase_dropdown', 'get_da_status_stage_case'));
    }

    public function update(Request $request, int $id)
    {
        if (intval($request->multiple_key)) {

            request()->validate([
                'date_da' => 'required',
                'irr_id' => 'required',
            ]);

            $final_instance_key = [];
            $final_irr_key = [];
            for ($r=0; $r < intval($request->multiple_key); $r++) {
                $final_instance_key[] = $_POST['final_instance_new_'.$r];
                $final_irr_key[] = intval($_POST['final_irr_multiple_new_'.$r]);
            }
            array_push($final_instance_key);
            array_push($final_irr_key);

            IncidentReport::where('id', $id)
            ->update([
                'date_da' => $request->date_da, 
                'final_instance' => json_encode($final_instance_key), 
                'final_irr_multiple' => json_encode($final_irr_key),
                'irr_id' => $request->irr_id
            ]);

        } else if ($request->irr_absolved == "Absolved") {

            request()->validate([
                'date_da' => 'required',
            ]);

            IncidentReport::where('id', $id)
            ->update(['date_da' => $request->date_da]);

        } else {

            request()->validate([
                'date_da' => 'required',
                'final_instance' => 'required',
                'final_irr_single' => 'required',
                'irr_id' => 'required',
            ]);

            IncidentReport::where('id', $id)
            ->update([
                'date_da' => $request->date_da, 
                'final_instance' => $request->final_instance, 
                'final_irr_single' => $request->final_irr_single,
                'irr_id' => $request->irr_id
            ]);
        }

        if (intval($request->suspension_date_multi_key)) {
            /* multiple suspension date */
            $suspension_date_multi = [];
            for ($s=0; $s < intval($request->suspension_date_multi_key); $s++) {
                $suspension_date_multi[] = $_POST['suspension_date_multi_'.$s];
            }
            array_push($suspension_date_multi);
            IncidentReport::where('id', $id)->update(['suspension_date' => json_encode($suspension_date_multi)]);

        } else if ($request->suspension_date)  {
            /* single suspension date */
            IncidentReport::where('id', $id)->update(['suspension_date' => $request->suspension_date]);
        } else {
            IncidentReport::where('id', $id)->update(['suspension_date' => null]);
        }

        if ($request->art_297) {
            $getArtExist = Article297Terminable::where('incident_report_id', $id)->first();

            if ($getArtExist) {
                Article297Terminable::where('incident_report_id', $id)
                ->update(['article_297_edited' => json_encode($request->art_297)]);
            } else {
                $art297 = new Article297Terminable;
                $art297->incident_report_id = $id;
                $art297->article_297_edited = json_encode($request->art_297);
                $art297->save();
            }
        }

        if ($request->da_status_stage_case) {
            Respondent::where('ir_id', $id)
            ->update(['da_status_stage_case' => $request->da_status_stage_case]);
        }

        $respondent = Respondent::where('ir_id', $id)->first();
        return redirect(
            'api/admin/incidentreport/generate/irr/preview/' 
            . $respondent->complainant_id . '/' . $respondent->respondent_user_id
        )
        ->with('success','DA Updated Successfully!!!');
    }

    public static function dropdownInstance()
    {
        return [
            "1st Instance" => "1st Instance",
            "2nd Instance" => "2nd Instance",
            "3rd Instance" => "3rd Instance",
            "4th Instance" => "4th Instance",
            "5th Instance" => "5th Instance",
            "6th Instance" => "6th Instance",
            "7th Instance" => "7th Instance",
        ];
    }

    public static function dropdownIrr()
    {
        $irrs = IncidentReportResolution::all();

        $irr_get = [];
        foreach ($irrs as $irr) {
            $irr_get[$irr->id] = $irr->name;
        }

        return $irr_get;
    }

    public static function dropdownDaStatusStageCase()
    {
        return [
            '' => 'Please Select',
            'Pending return of DA to HR from Ops' => 'Pending return of DA to HR from Ops',
            'Turnover of DA to Ops' => 'Turnover of DA to Ops',
            'DA - For signature of authorized signatories' => 'DA - For signature of authorized signatories',
            'DA Draft by HR' => 'DA Draft by HR',
            'DA for Agent signature' => 'DA for Agent signature',
            'Closed - Resigned' => 'Closed - Resigned',
            'Closed - Terminated' => 'Closed - Terminated',
            'Closed for 201 Filing' => 'Closed for 201 Filing',
            'On Preventive Suspension' => 'On Preventive Suspension',
        ];
    }
}
