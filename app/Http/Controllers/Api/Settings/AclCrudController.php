<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AclRulesRequest;
use App\Models\{Rules, User};
use App\Models\Settings\AclRule;
use Illuminate\Http\JsonResponse;
use DB;

class AclCrudController extends Controller
{
    public function index()
    {
        $acl_rules = AclRule::all();
        
        return response()->json($acl_rules);
    }

    public function store(AclRulesRequest $request)
    {
        AclRule::create($request->fields);

        return response()->json();
    }

    public function show($id)
    {
        $rule = AclRule::where('id', $id)->firstOrFail();

        return response()->json($rule);
    }

    public function update(AclRulesRequest $request, AclRule $acl_rule)
    {
        $acl_rule->fill($request->fields);
        $acl_rule->save();

        return response()->json();
    }

    public function destroy(AclRule $acl_rule)
    {
        $acl_rule->delete();

        return response()->json();
    }
}
