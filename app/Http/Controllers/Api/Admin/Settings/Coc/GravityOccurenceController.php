<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Admin\Settings\Coc\BehavioralAction;

class GravityOccurenceController extends Controller
{
    public function index(): JsonResponse
    {
        $behavioralAction = BehavioralAction::with([
            'gravity_offense',
            'first_occur',
            'second_occur',
            'third_occur',
            'fourth_occur',
            'fifth_occur',
            'sixth_occur',
            'seventh_occur'
        ])
        ->get();

        return response()->json($behavioralAction);
    }

    public function create(Request $request)
    {
        $behavioralAction = new BehavioralAction;
        $behavioralAction->gravity_id = $request->gravity_id;
        $behavioralAction->first_irr = $request->first_irr;
        $behavioralAction->second_irr = $request->second_irr;
        $behavioralAction->third_irr = $request->third_irr;
        $behavioralAction->fourth_irr = $request->fourth_irr;
        $behavioralAction->fifth_irr = $request->fifth_irr;
        $behavioralAction->sixth_irr = $request->sixth_irr;
        $behavioralAction->seventh_irr = $request->seventh_irr;
        $behavioralAction->save();

        return $behavioralAction;
    }

    public function show(int $id, Request $request)
    {
        $behavioralAction = BehavioralAction::updateOrCreate(['id' => $id], $request->all());

        return $behavioralAction;
    }

    public function edit(int $id)
    {
        $behavioralAction = BehavioralAction::all()->where('id', $id)->first();

        return $behavioralAction;
    }

    public function destroy(int $id)
    {
        $behavioralAction = BehavioralAction::findOrFail($id)->delete();

        return response()->json($behavioralAction);
    }
}
