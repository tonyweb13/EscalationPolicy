<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Respondent;
use App\Models\Admin\{
    IncidentReport,
    Article297Terminable
};

use App\Http\Controllers\PreviewDaController;

class PreviewNteController extends Controller
{
    public function edit(int $id)
    {
        $incident_report = IncidentReport::with(['irr'])->findOrFail($id);

        if ($incident_report->initial_irr_multiple) {
            $incident_report->initial_irr_multiple_new = json_decode($incident_report->initial_irr_multiple, true);
            $incident_report->initial_instance_new = json_decode($incident_report->initial_instance, true);
        }

        $instance_dropdown = PreviewDaController::dropdownInstance();
        $irr_dropdown =  PreviewDaController::dropdownIrr();

        return view('pdf.generate.preview.nte_prev_edit', compact('incident_report', 'instance_dropdown', 'irr_dropdown'));
    }

    public function update(Request $request, int $id)
    {
        if (intval($request->multiple_key)) {

            request()->validate([
                'date_nte_serve' => 'required',
            ]);

            $initial_instance_key = [];
            $initial_irr_key = [];
            for ($r=0; $r < intval($request->multiple_key); $r++) {
                $initial_instance_key[] = $_POST['initial_instance_new_'.$r];
                $initial_irr_key[] = intval($_POST['initial_irr_multiple_new_'.$r]);
            }
            array_push($initial_instance_key);
            array_push($initial_irr_key);

            IncidentReport::where('id', $id)
            ->update([
                'date_nte_serve' => $request->date_nte_serve, 
                'initial_instance' => json_encode($initial_instance_key), 
                'initial_irr_id' => json_encode($initial_irr_key),
            ]);

        } else {
            
            request()->validate([
                'date_nte_serve' => 'required',
                'initial_instance' => 'required',
                'initial_irr_id' => 'required',
            ]);

            IncidentReport::where('id', $id)
            ->update([
                'date_nte_serve' => $request->date_nte_serve, 
                'initial_instance' => $request->initial_instance, 
                'initial_irr_id' => $request->initial_irr_id,
            ]);
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

        $respondent = Respondent::where('ir_id', $id)->first();
        return redirect(
            'api/admin/incidentreport/generate/nte/preview/' 
            . $respondent->complainant_id . '/' . $respondent->respondent_user_id
        )
        ->with('success','NTE Updated Successfully!!!');
    }
}
