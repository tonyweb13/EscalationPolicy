<?php

/* count() funtion is not working in php 7.2  */
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Auth::routes();

Route::group([
    'prefix' => 'api',
    'middleware' => ['auth'],
], function () {
    Route::get('/user/supervisor/manager/access', 'Api\UsersController@supervisorManagerAccess');
    Route::get('/dropdown/users', 'Api\UsersController@dropdown');
    Route::get('/dropdown/users/email', 'Api\UsersController@dropdownEmail');
    Route::get('/dropdown/employee/hrbp', 'Api\UsersController@dropdownEmployeeWithHrbp');

    Route::group([
        'prefix' => 'notes',
    ], function () {
        Route::resource('/', 'Api\NotesController');
        Route::get('/retrieve/{comp_id}/{comp_user_id}/{resp_user_id}', 'Api\NotesController@notesRetrieve');
        Route::get('/count/hr/notreply', 'Api\NotesController@countNotesHrNotReply');
        Route::get('/count', 'Api\NotesController@countNotes');
        Route::get('/notify/replied', 'Api\NotesController@getNotesReplied');
    });

    Route::group([
        'prefix' => 'employee',
    ], function () {
        Route::get('/inprogress', 'Api\EmployeeController@newInprogress');
        Route::get('/onhold', 'Api\EmployeeController@onhold');
        Route::get('/invite', 'Api\EmployeeController@invite');
    });

    Route::group([
        'prefix' => 'complainant',
    ], function () {
        Route::resource('/', 'Api\ComplainantController');
        Route::post('/attach/file', 'Api\ComplainantController@attachFile');
        Route::get('/get/hrbp/{id}/{hr}', 'Api\ComplainantController@getRespondentHr');
        Route::post('/create/description', 'Api\ComplainantController@createDescription');
        Route::post('/complainant_edit/edit', 'Api\ComplainantController@complainantEdit');
    });

    Route::group([
        'prefix' => 'respondent',
    ], function () {
        Route::resource('/', 'Api\RespondentController');
        Route::get('/update/response/{id}', 'Api\RespondentController@updateResponse');
        Route::get('/update/hrbp_acknowledge_response/{id}', 'Api\RespondentController@updateAcknowledgeResponse');
        Route::get('/email/countIR', 'Api\RespondentController@countIRReply');
        Route::get('/email/countOnHold', 'Api\RespondentController@countOnHoldReply');
        Route::get('/get/hrbp/{id}', 'Api\RespondentController@getRespondentHr');
        Route::get('/email/notification', 'Api\RespondentController@notificationRespondentReply');
        Route::post('/attach/file', 'Api\RespondentController@attachFile');
        Route::get('/reply/reactivate/{ir_number}', 'Api\RespondentController@replyReactivate');
        Route::get(
            '/download/acknowledgement_letter/{respondent_user_id}',
            'Api\RespondentController@acknowledgementLetter'
        );
        Route::get(
            '/download/attachment/{folder}/{emp_no}/{date}/{filename}',
            'Api\RespondentController@downloadAttachment'
        );
    });

    Route::group([
        'prefix' => 'hearing',
    ], function () {
        Route::resource('/', 'Api\HearingAttendanceController');
        Route::get('/hr/approval/{id}', 'Api\HearingAttendanceController@hrApproval');
    });

    Route::group([
        'prefix' => 'mail',
    ], function () {
        Route::get(
            '/invitation/{ir_number}/{date}/{time}/{complainant_user_id}/{respondent_user_id}/{invites}',
            'Api\MailController@invitation'
        );
    });

    Route::group([
        'prefix' => 'settings',
    ], function () {
        Route::group([
            'prefix' => 'acl_rule',
        ], function () {
            Route::resource('/user_acl', 'Api\Settings\AclCrudController');
            Route::get('/list', 'Api\Settings\AclRuleController@list');
            Route::get('/user_list', 'Api\Settings\AclRuleController@userList');
            Route::get('/dropdown_rule_name', 'Api\Settings\AclRuleController@dropdownRuleName');
            Route::get('/dropdown_role_name', 'Api\Settings\AclRuleController@dropdownRoleName');
            Route::get('/dropdown_rule_name/{id}', 'Api\Settings\AclRuleController@selectedRuleName');
            Route::get('/rules/{id}', 'Api\Settings\AclRuleController@aclRules');

            Route::post('/create_acl_rule', 'Api\Settings\AclRuleController@createAclRule');
            Route::post('/create_acl_rule/{id}', 'Api\Settings\AclRuleController@updateAclRule');
            Route::post('/apply/{employee_no}', 'Api\Settings\AclRuleController@applyACLRules');
            Route::post(
                '/updateOrCreateUserRoleRules/{id}',
                'Api\Settings\AclRuleController@updateOrCreateUserRoleRules'
            );
            Route::post('/delete_acl_rule/{id}', 'Api\Settings\AclRuleController@deleteAclRule');
            Route::post('/remove/{employee_no}', 'Api\Settings\AclRuleController@removeACLRules');
        });
        Route::group([
            'prefix' => 'office',
        ], function () {
            Route::resource('/location', 'Api\Settings\OfficeController');
        });

        Route::get('/dropdown_vps_site_loc', 'Api\UsersController@dropdownVpsOfficeLocation');
        Route::post('/import/csv/sitelocation', 'Api\UsersController@importCsvSiteLocation');
    });

    Route::group([
        'prefix' => 'dashboard',
    ], function () {
        Route::post('/headers', 'Api\Dashboard\DashboardCountController@getDashboardHeaders');
        Route::get('/chart/count/cases/{status}/{month}', 'Api\Dashboard\DashboardCountController@countOpenClosed');
        Route::get('/chart/count/tat/{status}/{month}', 'Api\Dashboard\DashboardCountController@countWithinOverTAT');
        Route::get('/chart/count/ytd/{status}', 'Api\Dashboard\DashboardCountController@countYTD');
    });

    Route::group([
        'prefix' => 'admin',
    ], function () {

        Route::group([
            'prefix' => 'incidentreport',
        ], function () {
            Route::resource('/', 'Api\Admin\IncidentReportController');
            Route::get('/update/{id}', 'Api\Admin\IncidentReportController@updateIncident');
            Route::get('/signatory/employee/{id}', 'Api\Admin\IncidentReportController@signatoryEmployee');
            Route::get('/dropdown/office/location', 'Api\Settings\OfficeController@dropdownSite');
            Route::get('/status/incident/{statusid}', 'Api\Admin\IncidentReportController@statusIncident');
            Route::get('/userInfo', 'Api\Admin\IncidentReportController@userInfo');
            Route::get('/generate/nte/list/{comp_id}/{res_id}', 'Api\Admin\IncidentReportController@listNte');
            Route::get(
                '/generate/complainant/{comp_id}/{res_id}',
                'Api\Admin\IncidentReportController@generateComplainant'
            );
            Route::get(
                '/generate_admin_hearing/{comp_id}/{res_id}',
                'Api\Admin\IncidentReportController@generateAdminHearing'
            );
            Route::get('/generate/nte/{comp_id}/{res_id}', 'Api\Admin\IncidentReportController@generateNte');
            Route::get('/generate/nte/preview/{comp_id}/{res_id}', 'Api\Admin\IncidentReportController@previewNte');
            Route::get('/generate/irr/list/{comp_id}/{res_user_id}', 'Api\Admin\IncidentReportController@listIRR');
            Route::get('/generate/irr/{comp_id}/{res_user_id}', 'Api\Admin\IncidentReportController@generateIRR');
            Route::get('/generate/irr/preview/{comp_id}/{res_user_id}', 'Api\Admin\IncidentReportController@previewIRR');
            Route::post('/attach/file', 'Api\Admin\IncidentReportController@attachFile');
            Route::post('/mom_attach/file', 'Api\Admin\IncidentReportController@momAttachFile');
            Route::get(
                '/download/attachment/{folder}/{emp_no}/{date}/{filename}',
                'Api\Admin\IncidentReportController@downloadAttachment'
            );
            Route::get(
                '/download/mom_attachment/{folder}/{emp_no}/{date}/{filename}',
                'Api\Admin\IncidentReportController@downloadMoMAttachment'
            );
            Route::get(
                '/downloadHrInterventionDocument/{id}/{agreement}/{ir_id}/{irr_id}',
                'Api\Admin\IncidentReportController@downloadHrInterventionDocument'
            );
            Route::get(
                '/status/incident/count/{statusid}/new',
                'Api\Admin\IncidentReportController@statusIncidentCountNew'
            );
            Route::get('/download/{form_type}/{ir_id}/{ir_no}', 'Api\Admin\IncidentReportController@downloadForm');
            Route::get('/remove/attachments/{id}', 'Api\Admin\IncidentReportController@removeAttachment');
            Route::get('/request_edit', 'Api\Admin\IncidentReportController@requestEdit');
            Route::get('/request_edit/approval/{id}', 'Api\Admin\IncidentReportController@approveEdit');
            Route::post('/request_edit/attach/file', 'Api\Admin\IncidentReportController@irEditAttachFile');
            Route::get('/request_edit/edit/{id}', 'Api\Admin\IncidentReportController@irEdit');
            Route::get('/onhold_request', 'Api\Admin\IncidentReportController@onholdRequest');
            Route::get('/onhold_request/approval/{id}', 'Api\Admin\IncidentReportController@approveOnhold');
            Route::get('/onhold_request/count', 'Api\Admin\IncidentReportController@countOnHoldRequest');
        });

        Route::group([
            'prefix' => 'closed',
        ], function () {
            Route::get('/', 'Api\Admin\ClosedStatusController@index');
            // Route::get('/create', 'Api\Admin\ClosedStatusController@create');
            Route::get('/export/Da', 'Api\Admin\ClosedStatusController@exportClosedDa');
            Route::get('/update/irnumber', 'Api\Admin\ClosedStatusController@updateClosedIrnumber');
            Route::get('/export/{cat}/{subcat}', 'Api\Admin\ClosedStatusController@exportClosedFilter');
            Route::get('/export/{cat}/{subcat}/{thirdcat}', 'Api\Admin\ClosedStatusController@exportClosedFilterThird');
            Route::get('/dropdown/supervisor', 'Api\Admin\ClosedStatusController@dropdownSupervisor');
            Route::get('/dropdown/manager', 'Api\Admin\ClosedStatusController@dropdownManager');
            Route::get('/dropdown/provision', 'Api\Admin\ClosedStatusController@dropdownProvision');
            Route::get('/dropdown/offense', 'Api\Admin\ClosedStatusController@dropdownOffense');
            Route::get('/date/range', 'Api\Admin\ClosedStatusController@exportClosedDateRange');
        });

        Route::group([
            'prefix' => 'open',
        ], function () {
            Route::get('/cases/export_open', 'Api\Admin\ClosedStatusController@exportOpenCases');
        });

        Route::group([
            'prefix' => 'coaching',
        ], function () {
            Route::get('/selected_employee/{employee_no}', 'Api\Admin\Coaching\CoachingLogController@selectedUser');
            Route::get('/userToWeek/{user}/{month}', 'Api\Admin\Coaching\CoachingLogController@userToWeek');
            Route::get('/dropdown/department', 'Api\Admin\Coaching\CoachingLogController@dropdownDepartment');
            Route::get('/weekly', 'Api\Admin\Coaching\CoachingLogController@weekly');
            Route::get('/monthly', 'Api\Admin\Coaching\CoachingLogController@monthly');
            Route::get('/invalid/{id}', 'Api\Admin\Coaching\CoachingLogController@invalid');
            Route::get(
                '/dropdown/users/{month}',
                'Api\Admin\Coaching\CoachingLogController@employeeNameWithEmployeeNo'
            );
            Route::get(
                '/dropdown/users_with_employee_no',
                'Api\Admin\Coaching\CoachingLogController@employeeNameWithEmployeeNoWithoutFilter'
            );
            Route::get(
                '/create_monthly_form/{employee_no}/{month}',
                'Api\Admin\Coaching\CoachingLogController@createMonthlyForm'
            )->name('create_monthly_form');
            Route::get(
                '/create_coaching_form/{id}',
                'Api\Admin\Coaching\CoachingLogController@coachingForm'
            );
            Route::get(
                '/create_weekly_performce_form/{id}',
                'Api\Admin\Coaching\CoachingLogController@coachingPerformanceForm'
            );
            Route::get(
                '/acknowledge/{id}',
                'Api\Admin\Coaching\CoachingLogController@acknowledge'
            );
            Route::get(
                '/download_form/{id}',
                'Api\Admin\Coaching\CoachingLogController@downloadForm'
            );
            Route::get(
                '/create_weekly_form/{employee_no}/{month}/{week}',
                'Api\Admin\Coaching\CoachingLogController@createWeeklyForm'
            );
            Route::get(
                '/dropdown/sort_employee_name/{department_id}',
                'Api\Admin\Coaching\CoachingLogController@dropdownEmployeeName'
            );
            Route::get(
                '/dropdown/sort_dept_id/{employee_name}',
                'Api\Admin\Coaching\CoachingLogController@dropdownDeptId'
            );
            Route::post(
                '/importKPI/{emp_no}',
                'Api\Admin\Coaching\CoachingLogController@importKPI'
            )->name('upload_KPI');

            Route::post(
                '/image_attach/{cl_no}',
                'Api\Admin\Coaching\CoachingLogController@importImage'
            )->name('upload_image');
            Route::get('/dropdown/employee_no/', 'Api\Admin\Coaching\CoachingLogController@dropdownEmployeeNo');
            Route::get('/dropdown/portfolio/', 'Api\Admin\Coaching\CoachingLogController@dropdownProfileName');
            Route::get('/efficiency_goal', 'Api\Admin\Coaching\CoachingLogController@efficiencyGoal');
            Route::get('/dispute_dashboard', 'Api\Admin\Coaching\CoachingLogController@disputeDashboard');
            Route::get('/coaching', 'Api\Admin\Coaching\CoachingLogController@coaching');
            Route::get('/downloadExcel/{type}', 'Api\Admin\Coaching\CoachingLogController@downloadExcel');
            Route::get(
                '/importExcel',
                'Api\Admin\Coaching\CoachingLogController@importExcel'
            )->name('upload_dashboard');
            Route::post(
                '/importExcelDispute',
                'Api\Admin\Coaching\CoachingLogController@importExcelDispute'
            )->name('upload_dispute');
            Route::post(
                '/efficiency_goal',
                'Api\Admin\Coaching\CoachingLogController@createEfficiencyGoal'
            )->name('create_efficiency_goal');
            Route::post(
                '/efficiency_goal/{id}',
                'Api\Admin\Coaching\CoachingLogController@updateEfficiencyGoal'
            )->name('update_efficiency_goal');
            Route::post(
                '/delete_efficiency/{id}',
                'Api\Admin\Coaching\CoachingLogController@deleteEfficiencyGoal'
            )->name('delete_efficiency_goal');
            Route::get(
                '/coaching_log_form',
                'Api\Admin\Coaching\CoachingLogController@createCoaching'
            )->name('create_coaching_log');
            Route::get(
                '/added_form',
                'Api\Admin\Coaching\CoachingLogController@addForm'
            )->name('added_form');
            Route::post(
                '/add_weekly_performance_form',
                'Api\Admin\Coaching\CoachingLogController@addWeekly'
            )->name('add_weekly_performance');
            Route::post(
                '/weekly_performance_form',
                'Api\Admin\Coaching\CoachingLogController@createWeekly'
            )->name('create_weekly_performance');
            Route::post(
                '/edit_weekly_performance_form',
                'Api\Admin\Coaching\CoachingLogController@editWeekly'
            )->name('edit_weekly_performance');

            /* commmented temporarily 11322020 */
            // Route::post(
            //     '/weekly_performance_form',
            //     'Api\Admin\Coaching\CoachingLogController@createWeekly'
            // )->name('create_weekly_performance');
            // Route::post(
            //     '/edit_weekly_performance_form',
            //     'Api\Admin\Coaching\CoachingLogController@editWeekly'
            // )->name('edit_weekly_performance');


            Route::get(
                '/update_coaching',
                'Api\Admin\Coaching\CoachingLogController@updateCoaching'
            )->name('update_coaching_log');


            Route::get(
                '/export_coachinglog',
                'Api\Admin\Coaching\CoachingLogController@exportCoachingLogReport'
            );

            Route::get('/search_coaching/{key}',
                'Api\Admin\Coaching\CoachingLogController@searchQuery'
            );

            Route::get('/keyperformance/indicator/{id}',
                'Api\Admin\Coaching\CoachingLogController@keyPerformanceIndicator'
            );

            Route::get('/keyperformance/indicator/update/{id}',
                'Api\Admin\Coaching\CoachingLogController@keyPerformanceIndicatorUpdate'
            );
        });

        Route::group([
            'prefix' => 'progressionoffense',
        ], function () {
            Route::get('/{id}', 'Api\Admin\ProgressionOffenseController@progressionOffense');
            Route::get('/attendancepoints/attendance', 'Api\Admin\ProgressionOffenseController@attendanceOffense');
            Route::get('/refresh/period', 'Api\Admin\ProgressionOffenseController@refreshPeriod');
            Route::get(
                '/record/{employeeId}/{categoryId}/{respondentId}',
                'Api\Admin\ProgressionOffenseController@employeeProgressionOffense'
            );
        });

        Route::group([
            'prefix' => 'irhistory'
        ], function () {
            Route::post('/attach/signed', 'Api\Admin\IncidentReportHistoryController@attachSigned');
            Route::get('/all', 'Api\Admin\IncidentReportHistoryController@getIrHistoryAll');
            Route::get('/download/nte/{filename}', 'Api\Admin\IncidentReportHistoryController@downloadNTE');
            Route::get('/download/irr/{filename}', 'Api\Admin\IncidentReportHistoryController@downloadIRR');
            Route::get('/reopen/create', 'Api\Admin\IncidentReportHistoryController@create');
            Route::get('/enable/da/{ir_id}', 'Api\Admin\IncidentReportHistoryController@enableDa');
            Route::get(
                '/reopen/approval/{id}',
                'Api\Admin\IncidentReportHistoryController@reopenApproval'
            );
            Route::get('/export_irhist', 'Api\Admin\ClosedStatusController@exportIrHistory');
        });

        Route::group([
            'prefix' => 'hrbp',
        ], function () {
            Route::resource('/cluster', 'Api\Admin\HrbpClusterController');
            Route::get('/cluster/dropdown/site/location', 'Api\Admin\HrbpClusterController@dropdownSiteLocation');
            Route::get('/cluster/get/email/{employee_no}', 'Api\Admin\HrbpClusterController@getEmailAddress');
            Route::get('/cluster/dropdown/agentnotingroup', 'Api\Admin\HrbpClusterController@dropdownUserNotInGroup');
            Route::get('/cluster/dropdown/head', 'Api\Admin\HrbpClusterController@dropdownHeadOperation');
            Route::get('/cluster/dropdown/hr/site/leader', 'Api\Admin\HrbpClusterController@dropdownHrSiteLeader');
            Route::get('/cluster/dropdown/not/in/usergroup', 'Api\Admin\HrbpClusterController@dropdownNotExistGroup');
            Route::get('/cluster/dropdown/not/in/cluster', 'Api\Admin\HrbpClusterController@dropdownNotExistGroupAndCluster');
            Route::get('/cluster/dropdown/hrbp', 'Api\Admin\HrbpClusterController@dropdownHRBP');
            Route::get('/cluster/dropdown/hrbpById', 'Api\Admin\HrbpClusterController@dropdownHRBPbyId');
            Route::get('/cluster/dropdown/hrbpByName', 'Api\Admin\HrbpClusterController@dropdownHRBPbyName');
            Route::get('/cluster/find/hrsl/{employee_no}', 'Api\Admin\HrbpClusterController@findHrsl');
            Route::post('/cluster/import/csv', 'Api\Admin\HrbpClusterController@importCsv');
        });

        Route::get('/audit/logs', 'Api\Admin\AuditController@index');

        Route::group([
            'prefix' => 'settings',
        ], function () {

            Route::group([
                'prefix' => 'coc',
            ], function () {

                Route::resource(
                    '/incidentreportresolution',
                    'Api\Admin\Settings\Coc\IncidentReportResolutionController'
                );
                Route::resource('/gravity', 'Api\Admin\Settings\Coc\GravityController');
                Route::resource('/yearly_total_infraction', 'Api\Admin\Settings\Coc\YearlyTotalInfractionController');
                Route::resource('/monthly_total_infraction', 'Api\Admin\Settings\Coc\MonthlyTotalInfractionController');
                Route::resource('/offense', 'Api\Admin\Settings\Coc\OffenseController');
                Route::resource('/attendance/points/system', 'Api\Admin\Settings\Coc\AttendancePointsSystemController');
                Route::resource('/behavioralaction', 'Api\Admin\Settings\Coc\GravityOccurenceController');

                Route::get('/download', 'Api\Admin\Settings\Coc\CategoryController@downloadExcel');

                Route::get('/dropdown/category', 'Api\Admin\Settings\Coc\CategoryController@dropdown');
                Route::get('/dropdown/gravity', 'Api\Admin\Settings\Coc\GravityController@dropdown');
                Route::get('/dropdown/irr', 'Api\Admin\Settings\Coc\IncidentReportResolutionController@dropdown');
                Route::get('/dropdown/offense/category/{catid}', 'Api\Admin\Settings\Coc\OffenseController@dropdown');
                Route::get('/offense/category/{catid}', 'Api\Admin\Settings\Coc\OffenseController@category');
                Route::get('/offense/codeofconduct/{id}', 'Api\Admin\Settings\Coc\OffenseController@codeofconduct');
                Route::get('/offense/codeofconduct/{id}/{emp_no}', 'Api\Admin\Settings\Coc\OffenseController@codeofconductwithinstance');
                Route::get('/offense/progression/{offense_id}/{emp_no}', 'Api\Admin\Settings\Coc\OffenseController@progressionOccurenceShow');
                Route::get('/offense/coc/{offense_id}', 'Api\Admin\Settings\Coc\OffenseController@caseClosure');
                Route::get(
                    '/dropdown/attendance/infraction',
                    'Api\Admin\Settings\Coc\AttendancePointsSystemController@dropdownInfraction'
                );
                Route::get(
                    '/attendance/points/systems/{id}',
                    'Api\Admin\Settings\Coc\AttendancePointsSystemController@getattendancepointssystems'
                );
                Route::get(
                    '/dropdown/attendance/points',
                    'Api\Admin\Settings\Coc\AttendancePointsSystemController@dropdown'
                );
                Route::get(
                    '/offense/codeofconduct/multiple/{id}/{offenses}/{emp_no}',
                    'Api\Admin\Settings\Coc\OffenseController@codeofconductmultiple'
                );
                Route::get(
                    '/dropdown/offense/category/multiple/{catid}/{offenses}',
                    'Api\Admin\Settings\Coc\OffenseController@dropdownMultiple'
                );
            });
        });

        Route::group([
            'prefix' => 'approval',
        ], function () {
            Route::get('/list/nte', 'Api\Admin\IncidentReportController@headOfficerListNte');
            Route::get('/signatory/create', 'Api\Admin\IncidentReportController@createHeadOfficerApproval');
        });

        Route::group([
            'prefix' => 'search',
        ], function () {
            Route::get('/irhistory/{key}', 'Api\Admin\IncidentReportHistoryController@searchQuery');
            Route::get('/closed/{key}', 'Api\Admin\ClosedStatusController@searchQuery');
            Route::get('/auditlogs/{key}', 'Api\Admin\AuditController@searchQuery');
            Route::get('/distro/{key}', 'Api\DistroController@searchQuery');
            Route::get('/compliance/{key}', 'Api\AnnouncementController@searchQueryCompliance');
            Route::get('/sent/{key}', 'Api\AnnouncementController@searchQuerySent');
            Route::get('/coc/{key}', 'Api\Admin\Settings\Coc\OffenseController@searchQuery');
            Route::get('/coachinglog/{key}', 'Api\Admin\Coaching\CoachingLogController@searchQuery');
        });
    });

    Route::group([
        'prefix' => 'announcement',
    ], function () {
        Route::resource('/announce', 'Api\AnnouncementController');
        Route::post('/attach/file', 'Api\AnnouncementController@attachFile');
        Route::resource('/distro', 'Api\DistroController');
        Route::get('/dropdown/distro', 'Api\DistroController@dropdown');
        Route::get('/update/acknowledge/{announce_id}', 'Api\AnnouncementController@updateAcknowledge');
        Route::get('/compliance', 'Api\AnnouncementController@indexCompliance');
        Route::get('/compliance/download/{id}', 'Api\AnnouncementController@complianceDownload');
        Route::get('/sent', 'Api\AnnouncementController@sentAnnouncement');
    });

    Route::resource('/acknowledgementreceipt', 'Api\AcknowledgementReceiptController');
    Route::get('/acknowledgementreceipt/supervisormanager/{id}', 'Api\AcknowledgementReceiptController@getSupervisorManager');
    Route::get('/osh/list', 'Api\OshModuleController@index');
    Route::get('/osh/save/finish', 'Api\OshModuleController@oshSaveFinish');
    Route::get('/osh/employee/checking', 'Api\OshModuleController@oshEmployeeChecking');

    Route::resource('/egreetings', 'Api\EGreetingsController');
    Route::post('/egreetings/attach/template1', 'Api\EGreetingsController@attachTemplate1');
    Route::post('/egreetings/attach/template2', 'Api\EGreetingsController@attachTemplate2');
    Route::post('/egreetings/attach/profilepic', 'Api\EGreetingsController@attachProfilePic');
});

Route::get('/', 'AppController@index')->name('dashboard');
Route::get('/acknowledge/{ir_number}', 'AppController@acknowledgeEmailRespondent')->name('acknowledge');
Route::get('/acknowledgementreceipt/{id}', 'AppController@acknowledgementReceipt')->name('acknowledgementreceipt');
Route::get('/acknowledgementmanager/{id}/{ir_number}', 'AppController@acknowledgementManager')->name('acknowledgementmanager');
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
Route::resource('preview_da','PreviewDaController');
Route::resource('preview_nte','PreviewNteController');

Route::get('/{vue_template}', function ($name) {
    return view('index');
})->where('vue_template', '.*');
