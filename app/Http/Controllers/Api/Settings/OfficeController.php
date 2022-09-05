<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Settings\Office;

class OfficeController extends Controller
{
    public function index() : JsonResponse
    {
        $office = Office::all();

        return response()->json($office);
    }


    public function destroy(int $id)
    {
        $office = Office::findOrFail($id)->delete();

        return response()->json($office);
    }

    public function create(Request $request)
    {
        $office = Office::create([
            'site' => $request->site,
            'complete_address' => $request->complete_address
        ]);

        return response()->json();
    }

    public function show(int $id, Request $request)
    {
        $office = Office::updateOrCreate(['id' => $id], [
            'site' => $request->site,
            'complete_address' => $request->complete_address
        ]);

        return response()->json($office);
    }

    public function dropdownSite()
    {
        $office = Office::select('id as value', 'complete_address AS text')->get();

        return response()->json($office);
    }
}
