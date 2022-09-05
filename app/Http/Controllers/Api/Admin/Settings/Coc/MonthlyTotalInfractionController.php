<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Settings\Coc\MonthlyTotalInfraction;
use Illuminate\Http\JsonResponse;

class MonthlyTotalInfractionController extends Controller
{
    public function index(): JsonResponse
    {
        $monthly = MonthlyTotalInfraction::whereMonth('created_at', date('m'))->get();

        return response()->json($monthly);
    }
}
