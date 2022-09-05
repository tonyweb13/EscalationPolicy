<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use App\Http\Controllers\Controller;
use App\Models\Admin\Settings\Coc\IncidentReportResolution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ValidatorController;
use Exception;

class IncidentReportResolutionController extends Controller
{

    public function index(): JsonResponse
    {
        $irr = IncidentReportResolution::all();

        return response()->json($irr);
    }

    public function create(Request $request)
    {
        $validator = ValidatorController::IrrValidator($request);

        if (!$validator->fails()) {
                $irr = new IncidentReportResolution;
                $irr->name = $request->name;
                $irr->save();
        
                return $irr;
        } else {
            throw new Exception($validator->messages());
        }   
    }

    public function show(int $id, Request $request)
    {
        $validator = ValidatorController::IrrValidator($request);

        if (!$validator->fails()) {
            $irr = IncidentReportResolution::updateOrCreate(['id' => $id], $request->all());

            return $irr;
            
        } else {
            throw new Exception($validator->messages());
        }   
    }

    public function edit(int $id)
    {
        $irr = IncidentReportResolution::all()->where('id', $id)->first();

        return $irr;
    }

    public function destroy(int $id)
    {
        $irr = IncidentReportResolution::findOrFail($id)->delete();

        return response()->json($irr);
    }

    public function dropdown(): JsonResponse
    {
        $description = IncidentReportResolution::select('id as value', 'name as text')->get();

        return response()->json($description);
    }
}
