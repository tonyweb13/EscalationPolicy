<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Settings\AclRule;
use App\Models\{User, Role, Rules, OfficeLocation, Department, Designation};
use DB;

class AclRuleController extends Controller
{
    public function list() : JsonResponse
    {
        $aclrules = AclRule::leftJoin('acl_rules as rule2', 'acl_rules.rule_parent_id', '=', 'rule2.id')
            ->select(
                'acl_rules.id',
                'acl_rules.rule_name',
                'acl_rules.rule_parent_id',
                DB::raw("(CASE WHEN (acl_rules.is_enable = 1) THEN 'Yes' ELSE 'No' END) as is_enable"),
                'acl_rules.label',
                'acl_rules.description',
                DB::raw("(CASE WHEN (rule2.rule_name = NULL) THEN '' ELSE rule2.rule_name END) as rule_parent")
            )
            ->orderBy('id')
            ->get();

        return response()->json($aclrules);
    }

    public function dropdownRuleName()
    {
        $rule_name = AclRule::select('id as value', DB::raw('CONCAT(rule_name) AS text'))->get();

        return response()->json($rule_name);
    }

    public function dropdownRoleName()
    {
        $rule_name = Role::select('id as value', DB::raw('CONCAT(name) AS text'))->get();

        return response()->json($rule_name);
    }

    public function selectedRuleName($rule_parent_id)
    {
        $selected_rule_name = AclRule::where('id', $rule_parent_id)
            ->select('id as value', DB::raw('CONCAT(rule_name) AS text'))->get();

        return response()->json($selected_rule_name);
    }

    public function createAclRule(Request $request)
    {
        $acl_rule = AclRule::create([
            'rule_name' => $request->params['rule_name'],
            'rule_parent_id' => $request->params['rule_parent']['value'],
            'label' => $request->params['label'],
            'description' => $request->params['description']
        ]);

        return response()->json();
    }

    public function updateAclRule(int $id, Request $request)
    {
        $acl_rule = AclRule::updateOrCreate(['id' => $id], [
            'rule_name' => $request->params['rule_name'],
            'rule_parent_id' => $request->params['rule_parent']['value'],
            'label' => $request->params['label'],
            'description' => $request->params['description']
        ]);

        return response()->json($acl_rule);
    }

    public function updateOrCreateUserRoleRules(int $id, Request $request)
    {
        if ($request->params['role']['value']) {
            $user = User::where('employee_no', $request->params['employee_no'])->first();
            $user->roles()->sync($request->params['role']['value']);
        }

        if ($request->params['rules']) {
            $fields['rules'] = $request->params['rules'];
            $acl_rules = Rules::where('employee_no', $request->params['employee_no'])->firstOrFail();
            $acl_rules->fill($fields);
            $acl_rules->save();
        }
        if ($request->params['vps_office_location']['value']) {
            UsersController::UpdateVpsOfficeLocation(
                $request->params['employee_no'],
                $request->params['vps_office_location']['value'],
                $request->params['vps_office_location']['text']
            );
        }

        return response()->json();
    }

    public function deleteAclRule(int $id)
    {
        $acl_rule = AclRule::findOrFail($id)->delete();

        return response()->json($acl_rule);
    }

    public function userList(Request $request)
    {
        $users = User::with(['roles' => function ($query) {
            $query->select('role_id', 'name');
        },'designation', 'office_location', 'department'])->get();

        return response()->json($users);
    }

    public function applyACLRules($employee_no)
    {
        $rules = [];
        $user = User::where('employee_no', $employee_no)->with('roles')->firstOrFail();
        $acl_rule = $this->retrieveFormattedACLRules(strtolower($user->roles[0]->name));
        $apply_rules = DB::table('rules')->insert(['employee_no' => $employee_no, 'rules' => json_encode($acl_rule)]);
        if ($apply_rules) {
            $rules = Rules::where('employee_no', $employee_no)->select('rules')->firstOrFail();
        }

        return response()->json($rules);
    }

    public function removeACLRules($employee_no)
    {
        $rules = Rules::where('employee_no', $employee_no)->delete();

        return response()->json($rules);
    }

    public function aclRules($user_id)
    {
        $user = User::find($user_id);
        $rules = Rules::where('employee_no', $user->employee_no)->first();

        return response()->json($rules['rules']);
    }

    public function retrieveFormattedACLRules($role)
    {
        $admin = "Super Admin Access";
        $acl_parent = AclRule::whereNull('rule_parent_id')->get();
        $rule = $this->userRules($role);
        $designation = auth()->user()->designation->name;

        if ($role === 'super admin access') {
            $is_applicable = [true, 'is_enable' => true];
            $is_not_applicable = [true, 'is_enable' => true];
        } elseif ($role === 'hr admin access') {
            $is_applicable = [true, 'is_enable' => true];
            $is_not_applicable = [false, 'is_enable' => false];
        } elseif ($role === 'hr access') {
            $is_applicable = [true, 'is_enable' => true];
            $is_not_applicable = [false, 'is_enable' => false];
        } elseif ($role === 'regular supervisor access') {
            $is_applicable = [true, 'is_enable' => true];
            $is_not_applicable = [false, 'is_enable' => false];
        } else {
            $is_applicable = [true, 'is_enable' => true];
            $is_not_applicable = [false, 'is_enable' => false];
        }

        $rules = [
            'dashboard' => [
                'is_enable' => $is_applicable[0],
            ],
            'acknowledge_receipt' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'add' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' || Str::contains($designation, 'Engagement') == 1) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'edit' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' || Str::contains($designation, 'Engagement') == 1) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'archive' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' || Str::contains($designation, 'Engagement') == 1) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'export' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' || Str::contains($designation, 'Engagement') == 1) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                ]
            ],
            'announcement' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'compose_mail' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                        || $role === 'hr access' || Str::contains($designation, 'Engagement') == 1
                        || Str::contains($designation, 'Compensation') == 1
                        || Str::contains($designation, 'Nurse') == 1 ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'distro' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                        || $role === 'hr access' || Str::contains($designation, 'Engagement') == 1
                        || Str::contains($designation, 'Compensation') == 1
                        || Str::contains($designation, 'Nurse') == 1 ) ? $is_applicable[0] : $is_not_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                                || $role === 'hr access' || Str::contains($designation, 'Engagement') == 1
                                || Str::contains($designation, 'Compensation') == 1
                                || Str::contains($designation, 'Nurse') == 1 ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                        ]
                    ],
                    'compliance' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                        || $role === 'hr access' || Str::contains($designation, 'Engagement') == 1
                        || Str::contains($designation, 'Compensation') == 1
                        || Str::contains($designation, 'Nurse') == 1 ) ? $is_applicable[0] : $is_not_applicable[0],
                        'child_rules' => [
                            'view' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                                || $role === 'hr access' || Str::contains($designation, 'Engagement') == 1
                                || Str::contains($designation, 'Compensation') == 1
                                || Str::contains($designation, 'Nurse') == 1 ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'download' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                                || $role === 'hr access' || Str::contains($designation, 'Engagement') == 1
                                || Str::contains($designation, 'Compensation') == 1
                                || Str::contains($designation, 'Nurse') == 1 ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                        ]
                    ],
                ],
            ],
            'code_of_conduct' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'attendance' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'cwd_policy' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'negligence' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'general_house_rules' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'fraud' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'misconduct' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'poor_work_ethics' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'information_security' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'zero_tolerance_policy' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'compliance_breach' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'general_company_rules_and_regulations' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'negligence_pip' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'lms_guidelines' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'corrective_policy' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'hard_return' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'warranted_escalation' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'attendance_supervisor_and_manager' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'attendance_trainer' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'credit_repair_offer' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                    'credit_repair_offer_supervisor' => [
                        'is_enable' => $is_applicable[0],
                        'child_rules' => [
                            'add' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'archive' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'export' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ]
                    ],
                ]
            ],
            'behavioral_action' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'add' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'edit' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'archive' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'export' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ]
                ]
            ],
            'levels_of_gravity' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'add' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'edit' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'archive' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'export' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ) ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'yearly_total_infraction' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                        $role === 'hr access') ? $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'monthly_total_infraction' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                        $role === 'hr access') ? $is_applicable[0] : $is_not_applicable[0],
                    ],
                ]
            ],
            'case_closure' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'add' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access') ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'edit' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access') ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'delete' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access') ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ]
                ]
            ],
            'incident_report' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'create_incident_report' => [
                        'is_enable' => ( $role === 'super admin access' || $role === 'hr admin access' ||
                            $role === 'hr access' || $role === 'regular supervisor access') ?
                            $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'new' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                            $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                        'child_rules' => [
                            'note' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'review' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ],
                    ],
                    'in_progress' => [
                        'is_enable' => $is_applicable['is_enable'],
                        'child_rules' => [
                            'note' => [
                                'is_enable' => $is_applicable[0],
                            ],
                            'reply' => [
                                'is_enable' => ($role === 'regular supervisor access'
                                || $role === 'regular user access') ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'view' => [
                                'is_enable' => $is_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'nte' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'cc' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'attend' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ],
                    ],
                    'on_hold' => [
                        'is_enable' => $is_applicable['is_enable'],
                        'child_rules' => [
                            'note' => [
                                'is_enable' => $is_applicable[0],
                            ],
                            'reply' => [
                                'is_enable' => ($role === 'regular supervisor access'
                                || $role === 'regular user access') ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'view' => [
                                'is_enable' => $is_applicable[0],
                            ],
                            'edit' => [
                                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access' ) ? $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'nte' => [
                                'is_enable' => ($role == 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access'  || $role === 'regular supervisor access') ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'cc' => [
                                'is_enable' => ($role == 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access'  || $role === 'regular supervisor access') ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ],
                            'attend' => [
                                'is_enable' => ($role == 'super admin access' || $role === 'hr admin access' ||
                                    $role === 'hr access'  || $role === 'regular supervisor access') ?
                                    $is_applicable[0] : $is_not_applicable[0],
                            ]
                        ],
                    ],
                    'closed' => [
                        'is_enable' => $role !== 'regular user access' ? $is_applicable[0] : $is_not_applicable[0],
                        'view' => [
                            'is_enable' => $is_applicable[0],
                        ],
                    ],
                ]
            ],
            'incident_report_history' => [
                'is_enable' => $is_applicable[0],
                'child_rules' => [
                    'view' => [
                        'is_enable' => $is_applicable[0],
                    ],
                    'nte' => [
                        'is_enable' => $is_applicable[0],
                    ],
                    'cc' => [
                        'is_enable' => $is_applicable[0],
                    ],
                    'export' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                            $role === 'hr access') ? $is_applicable[0] : $is_not_applicable[0],
                    ]
                ]
            ],
            'hrbp_cluster' => [
                'is_enable' => ($role == 'super admin access' || $role === 'hr admin access' ||
                    $role === 'hr access'  || $role === 'regular supervisor access') ?
                    $is_applicable[0] : $is_not_applicable[0],
                'child_rules' => [
                    'add' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                        || $role === 'hr access')
                            ? $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'edit' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                        || $role === 'hr access')
                            ? $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'archive' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access'
                        || $role === 'hr access')
                            ? $is_applicable[0] : $is_not_applicable[0],
                    ],
                    'export' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access' ||
                            $role === 'hr access') ? $is_applicable[0] : $is_not_applicable[0],
                    ]
                ]
            ],
            'progression_offense' => [
                'is_enable' => $is_applicable[0],
            ],
            'reports' => [
                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access') ?
                $is_applicable[0] : $is_not_applicable[0],
            ],
            'audit_logs' => [
                'is_enable' => ($role === 'super admin access' || $role === 'hr admin access')
                    ? $is_applicable[0] : $is_not_applicable[0],
                'child_rules' => [
                    'Export' => [
                        'is_enable' => ($role === 'super admin access' || $role === 'hr admin access')
                            ? $is_applicable[0] : $is_not_applicable[0],
                    ],
                ],
            ],
            'coaching_logs' => [
                'is_enable' => $is_applicable[0],
            ],
            'aclrules' => [
                'is_enable' => ( $role === 'super admin access')
                ? $is_applicable[0] : $is_not_applicable[0],
            ],
            'user' => [
                'is_enable' => ( $role === 'super admin access'
                || Str::contains($designation, 'Network Administrator') == 1
                || Str::contains($designation, 'Help Desk') == 1
                || Str::contains($designation, 'Helpdesk') == 1)
                ? $is_applicable[0] : $is_not_applicable[0],
            ],
            'roles' => [
                'is_enable' => ( $role === 'super admin access'
                || Str::contains($designation, 'Network Administrator') == 1
                || Str::contains($designation, 'Help Desk') == 1
                || Str::contains($designation, 'Helpdesk') == 1)
                ? $is_applicable[0] : $is_not_applicable[0],
            ],
        ];

        return $rules;
    }

    private function userRules($role)
    {
        switch ($role) {
            case 'Super Admin Role':
                $rules = ['is_applicable' => true, 'is_enable' => true];
                break;
            case 'HR Admin Access':
                $rules = ['is_applicable' => false, 'is_enable' => true];
                break;
            case 'HR Access':
                $rules = ['is_applicable' => false, 'is_enable' => true];
                break;
            case 'Regular Supervisor Access':
                $rules = ['is_applicable' => false, 'is_enable' => true];
                break;
            case 'Regular User Access':
                $rules = ['is_applicable' => false, 'is_enable' => true];
                break;
            default:
                $rules = ['is_applicable' => false, 'is_enable' => true];
                break;
        }

        return $rules;
    }
}
