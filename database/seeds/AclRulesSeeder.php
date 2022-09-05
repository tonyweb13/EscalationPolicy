<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AclRulesSeeder extends Seeder
{
    public function run()
    {
        //Dashboard rules
        DB::table('acl_rules')->insert(
            ['rule_name' => 'dashboard',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Dashboard',
                'description' => 'Grant Access to Dashboard',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
        //Code Of Conduct Rules
        $code_of_conduct_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'code_of_conduct',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Code Of Conduct',
                'description' => 'Grant Access to Code Of Conduction',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $code_of_conduct_child_list = [
            [
                'rule_name' => 'schedule_adherence',
                'rule_parent_id' => $code_of_conduct_id,
                'is_enable' => true,
                'label' => 'Schedule Adherence',
                'description' => 'Grant Access to Schedule Adherence',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'performance_related',
                'rule_parent_id' => $code_of_conduct_id,
                'is_enable' => true,
                'label' => 'Performace Related',
                'description' => 'Grant Access to Performace Related',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'behaviour_related',
                'rule_parent_id' => $code_of_conduct_id,
                'is_enable' => true,
                'label' => 'Behaviour Related',
                'description' => 'Grant Access to Behaviour Related',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'information_security',
                'rule_parent_id' => $code_of_conduct_id,
                'is_enable' => true,
                'label' => 'Information Security',
                'description' => 'Grant Access to Schedule Adherence',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'fraud_dishonesty',
                'rule_parent_id' => $code_of_conduct_id,
                'is_enable' => true,
                'label' => 'Fraud, Dishonesty',
                'description' => 'Grant Access to Fraud, Dishonesty',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'gross_negligence',
                'rule_parent_id' => $code_of_conduct_id,
                'is_enable' => true,
                'label' => 'Gross Negligence',
                'description' => 'Grant Access to Schedule Adherence',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('acl_rules')->insert($code_of_conduct_child_list);

        //Behavioral Action Rules
        $behavioral_action_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'behavioral_action',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Behavioral Action',
                'description' => 'Grant Access to Behavioral Action',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //Level Of Gravity Rules
        $levels_of_gravity_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'levels_of_gravity',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Level Of Gravity',
                'description' => 'Grant Access to Level Of Gravity',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //IR Resolution Rules
        $ir_resolution_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'ir_resolution',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'IR Resolution',
                'description' => 'Grant Access to IR Resolution',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //Incident Report rules
        $incident_report_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'incident_report',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Incident Report',
                'description' => 'Grant Access to Incident Report',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $incident_report_child_list = [
            [
                'rule_name' => 'new',
                'rule_parent_id' => $incident_report_id,
                'is_enable' => true,
                'label' => 'New',
                'description' => 'Grant Access to New',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'in_progress',
                'rule_parent_id' => $incident_report_id,
                'is_enable' => true,
                'label' => 'In Progress',
                'description' => 'Grant Access to In Progress',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'on_hold',
                'rule_parent_id' => $incident_report_id,
                'is_enable' => true,
                'label' => 'On Hold',
                'description' => 'Grant Access to On Hold',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'closed',
                'rule_parent_id' => $incident_report_id,
                'is_enable' => true,
                'label' => 'Closed',
                'description' => 'Grant Access to Closed',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'invite',
                'rule_parent_id' => $incident_report_id,
                'is_enable' => true,
                'label' => 'Invite',
                'description' => 'Grant Access to Invite',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('acl_rules')->insert($incident_report_child_list);

        //Incident Report History rules
        $incident_report_history_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'incident_report_history',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Incident Report History',
                'description' => 'Grant Access to Incident Report History',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //Progression Offense rules
        $progression_offense_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'progression_offense',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Progression Offense',
                'description' => 'Grant Access to Progression Offense',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //Reports rules
        $reports_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'reports',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Reports',
                'description' => 'Grant Access to Reports',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //HRBP Clusters rules
        $hrbp_cluster_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'hrbp_cluster',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'HRBP Cluster',
                'description' => 'Grant Access to HRBP Cluster',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //Audit Logs rules
        $audit_logs_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'audit_logs',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Audit Logs',
                'description' => 'Grant Access to Audit Logs',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //Coaching Logs rules
        $coaching_logs_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'coaching_logs',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Coaching Logs',
                'description' => 'Grant Access to Coaching Logs',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $coaching_logs_child_list = [
            [
                'rule_name' => 'dashboard_raw',
                'rule_parent_id' => $coaching_logs_id,
                'is_enable' => true,
                'label' => 'Dashboard Raw',
                'description' => 'Grant Access to Dashboard Raw tab',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'dispute_dashboard_raw',
                'rule_parent_id' => $coaching_logs_id,
                'is_enable' => true,
                'label' => 'Dispute Dashboard Raw',
                'description' => 'Grant Access to Dispute Dashboard Raw tab',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'create_monthly_form',
                'rule_parent_id' => $coaching_logs_id,
                'is_enable' => true,
                'label' => 'Create Monthly Form',
                'description' => 'Grant Access to Create Monthly Form tab',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'create_weekly_form',
                'rule_parent_id' => $coaching_logs_id,
                'is_enable' => true,
                'label' => 'Create Weekly Form',
                'description' => 'Grant Access to Create Weekly Form tab',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'efficiency_goals',
                'rule_parent_id' => $coaching_logs_id,
                'is_enable' => true,
                'label' => 'Efficiency Goals',
                'description' => 'Grant Access to Efficiency Goals tab',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'coaching_log',
                'rule_parent_id' => $coaching_logs_id,
                'is_enable' => true,
                'label' => 'Coaching Log',
                'description' => 'Grant Access to Coaching Log tab',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('acl_rules')->insert($coaching_logs_child_list);

        //Settings rules
        $settings_id = DB::table('acl_rules')->insertGetId(
            ['rule_name' => 'settings',
                'rule_parent_id' => null,
                'is_enable' => true,
                'label' => 'Settings',
                'description' => 'Grant Access to Settings and Dependents',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $settings_children = [
            [
                'rule_name' => 'aclrules',
                'rule_parent_id' => $settings_id,
                'is_enable' => true,
                'label' => 'Acl Crud',
                'description' => 'Grant Access to ACL Rules Crud',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'users',
                'rule_parent_id' => $settings_id,
                'is_enable' => true,
                'label' => 'Users Table',
                'description' => 'Grant Access Users',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'rule_name' => 'roles',
                'rule_parent_id' => $settings_id,
                'is_enable' => true,
                'label' => 'Roles Table',
                'description' => 'Grant Access Roles',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('acl_rules')->insert($settings_children);
    }
}
