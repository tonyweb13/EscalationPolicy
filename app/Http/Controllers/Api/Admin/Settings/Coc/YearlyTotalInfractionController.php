<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Settings\Coc\YearlyTotalInfraction;
use Illuminate\Http\JsonResponse;

class YearlyTotalInfractionController extends Controller
{
    public function index(): JsonResponse
    {
        $yearly = YearlyTotalInfraction::whereYear('created_at', date('Y'))->get();

        return response()->json($yearly);
    }
}
