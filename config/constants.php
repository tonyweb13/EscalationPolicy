<?php

return [
    'Dashboard_Raw' => [
        'Employee ID' => 20171004,
        'Employee Name' => "User 1",
        'Designation' => 'Loan Originator',
        'Priority' => 'Prio 2',
        'Tenureship' => 'Tenured',
        'Portfolio' => 'Loan1',
        'Site Location' => 'Market-Market',
        'Days' => '-',
        'Coverage' => '05/01/2019-05/31/2019',
        'SCG' => 10000,
        'MTDG' => 10000,
        'Team' => 'User 2',
        'Cluster' => 'User 3',
        'Loan Amount' => 220,
        'Attendance' => 'Present',
        'In Call %' => 30,
        'Ready %' => 40,
        'Wrap up %' => 50,
        'Not Ready %' => 40,
        "CoverageTotal Loan Amount" => 8475,
        "MTDTotal Loan Amount" => 8475,
        "AttendanceReliability" => 1,
        'Major' => 0,
        'Minor' => 0,
        "ReportDate" => '13-May-19',
        'Week Count' => 'Week 1',
        'Running Week' => 'May 13 - May 19'
    ],
    'Dispute_Dashboard_Raw' => [
        'Employee ID' => 20171004,
        'Employee Name' => "User 1",
        'Designation' => 'Loan Originator',
        'Priority' => 'Prio 2',
        'Tenureship' => 'Tenured',
        'Portfolio' => 'Loan2',
        'Site Location' => 'Market-Market',
        'Days' => '-',
        'Coverage' => '05/01/2019-05/31/2019',
        'SCG' => 10000,
        'MTDG' => 10000,
        'Team' => 'User 2',
        'Cluster' => 'User 3',
        'Loan Amount' => 0,
        'Attendance' => 'Present',
        'In Call %' => 0,
        'Ready %' => 0,
        'Wrap up %' => 0,
        'Not Ready %' => 0,
        "CoverageTotal Loan Amount" => 8475,
        "MTDTotal Loan Amount" => 8475,
        "AttendanceReliability" => 1,
        'Major' => 0,
        'Minor' => 0,
        "ReportDate" => '13-May-19',
        'Week Count' => 'Week 1',
        'Running Week' => 'May 13 - May 19'
    ],
    'Rules_Admin' => [
        "dashboard" => ["is_enable"=>true],
        "code_of_conduct" => [
            "is_enable" => true,
            "child_rules" => [
                "schedule_adherence" => ["is_enable" => true],
                "performance_related" => ["is_enable" => true ],
                "behaviour_related" => ["is_enable"=>true],
                "information_security" => ["is_enable"=>true],
                "fraud_dishonesty" => ["is_enable"=>true],
                "gross_negligence" => ["is_enable"=>true]
            ]
        ],
        "disciplinary_action" => [
            "is_enable"=>true,
            "child_rules" => [
                "Add" => ["is_enable"=>true],
                "Edit" => ["is_enable"=>true],
                "Delete" => ["is_enable"=>true],
                "Export" => ["is_enable"=>true]
            ]
        ],
        "levels_of_gravity" => [
            "is_enable" => true,
            "child_rules" => [
                "Add" => ["is_enable"=>true],
                "Edit" => ["is_enable"=>true],
                "Delete" => ["is_enable"=>true],
                "Export" => ["is_enable"=>true]
            ]
        ],
        "ir_resolution" => [
            "is_enable"=>true,
            "child_rules" => [
                "Add" => ["is_enable"=>true],
                "Edit" => ["is_enable"=>true],
                "Delete" => ["is_enable"=>true]
            ]
        ],
        "incident_report" => [
            "is_enable" => true,
            "child_rules" => [
                "new" => [
                    "is_enable"=>true,
                    "child_rules" => [
                        "Add"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ],
                "in_progress" => [
                    "is_enable"=>true,
                    "child_rules" => [
                        "Add"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ],
                "on_hold"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Add"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ],
                "closed"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Add"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ],
                "invite"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Add"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ]
            ]
        ],
        "incident_report_history"=>[
            "is_enable"=>true,
            "child_rules"=>[
                "Add"=>["is_enable"=>true],
                "Edit"=>["is_enable"=>true],
                "Delete"=>["is_enable"=>true]
            ]
        ],
        "progression_offense"=>["is_enable"=>true],
        "reports"=>[
            "is_enable"=>true,
            "child_rules"=>[
                "Download"=>["is_enable"=>true]
            ]
        ],
        "hrbp_cluster"=>[
            "is_enable"=>true,
            "child_rules"=>[
                "Export"=>["is_enable"=>true],
                "Add"=>["is_enable"=>true],
                "Edit"=>["is_enable"=>true],
                "Delete"=>["is_enable"=>true]
            ]
        ],
        "audit_logs"=>[
            "is_enable"=>true,
            "child_rules"=>[
                "Export"=>["is_enable"=>true]
            ]
        ],
        "coaching_logs"=>[
            "is_enable"=>true,
            "child_rules"=>[
                "dashboard_raw"=>["is_enable"=>true],
                "dispute_dashboard_raw"=>["is_enable"=>true],
                "create_monthly_form"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Download"=>["is_enable"=>true],
                        "Create"=>["is_enable"=>true]
                    ]
                ],
                "create_weekly_form"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Download"=>["is_enable"=>true],
                        "Create"=>["is_enable"=>true]
                    ]
                ],
                "efficiency_goals"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Add"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ],
                "coaching_log_tab"=>[
                    "is_enable"=>true,
                    "child_rules"=>[
                        "Create"=>["is_enable"=>true],
                        "Edit"=>["is_enable"=>true],
                        "Delete"=>["is_enable"=>true]
                    ]
                ]
            ]
        ],
        "aclrules"=>["is_enable"=>true],
        "user"=>["is_enable"=>true],
        "roles"=>["is_enable"=>true]
    ],
];
