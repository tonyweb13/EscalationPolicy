<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Settings\Coc\{
    GravityOffense,
    Gravity
};
use Illuminate\Http\JsonResponse;

class GravityController extends Controller
{

    public function index(): JsonResponse
    {
        $gravity = GravityOffense::with(['gravity'])->get();

        return response()->json($gravity);
    }

    public function create(Request $request)
    {
        $gravity = new GravityOffense;
        $gravity->fields = $request->fields;
        $gravity->gravity_id = $request->gravity_id;
        $gravity->description = $request->description;
        $gravity->save();

        return $gravity;
    }

    public function show(int $id, Request $request)
    {
        $gravity = GravityOffense::updateOrCreate(['id' => $id], $request->all());

        return $gravity;
    }

    public function edit(int $id)
    {
        $gravity = GravityOffense::all()->where('id', $id)->first();

        return $gravity;
    }

    public function destroy(int $id)
    {
        $gravity = GravityOffense::findOrFail($id)->delete();

        return response()->json($gravity);
    }

    public function dropdown(): JsonResponse
    {
        $gravity = Gravity::select('id as value', DB::raw('CONCAT(gravity, " - ", prescriptive_period) AS text'))
                   ->get();

        return response()->json($gravity);
    }
}
