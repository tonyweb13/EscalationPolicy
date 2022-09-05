<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Settings\Coc\{
    AttendancePointsSystem,
    AttendancePoint
};
use Illuminate\Http\JsonResponse;

class AttendancePointsSystemController extends Controller
{
    public function index(): JsonResponse
    {
        $attendance = AttendancePointsSystem::with(['category', 'attendancepoint'])->get();

        return response()->json($attendance);
    }

    public function create(Request $request)
    {
        $attendance = new AttendancePointsSystem;
        $attendance->type_infraction = $request->type_infraction;
        $attendance->category_id = $request->category_id;
        $attendance->attendancepoint_id = $request->attendancepoint_id;
        $attendance->definition = $request->definition;
        $attendance->save();

        return $attendance;
    }

    public function show(int $id, Request $request)
    {
        $attendance = AttendancePointsSystem::with(['category', 'attendancepoint'])
        ->updateOrCreate(['id' => $id], $request->all());

        return $attendance;
    }

    public function edit(int $id)
    {
        $attendance = AttendancePointsSystem::all()->where('id', $id)->first();

        return $attendance;
    }

    public function destroy(int $id)
    {
        $attendance = AttendancePointsSystem::findOrFail($id)->delete();

        return response()->json($attendance);
    }

    public function dropdown()
    {
        $attendancepoints = AttendancePoint::select('id as value', 'attendance_points AS text')->get();

        return response()->json($attendancepoints);
    }

    public function dropdownInfraction(): JsonResponse
    {
        $attendancepoints = AttendancePointsSystem::select('id as value', 'type_infraction AS text')
        ->with(['attendancepoint'])
        ->get();

        return response()->json($attendancepoints);
    }

    public function getattendancepointssystems(int $id)
    {
        $attendance = AttendancePointsSystem::with(['attendancepoint'])->findOrFail($id);

        return $attendance;
    }
}
