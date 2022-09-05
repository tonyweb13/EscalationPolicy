<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Audit;

class AuditController extends Controller
{
    public function index()
    {
        $all = Audit::with(['audit_user'])
        ->orderBy('id', 'desc')
        ->paginate(10);

        return $all;
    }

    public function searchQuery($searchKey)
    {
        $all = Audit::with(['audit_user'])
            ->where('old_values', 'LIKE', '%'.$searchKey.'%')
            ->orWhere('new_values', 'LIKE', '%'.$searchKey.'%')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($all);
    }
}
