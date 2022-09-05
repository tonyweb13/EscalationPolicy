<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">
            <input-search v-if="role != 'Regular User Access'" @search="eventSearch(searchKey)" >
            <input type="text"
                    v-if="role != 'Regular User Access'"
                    class="form-control search-custom-input"
                    placeholder="Type IR Number or First Name or Last Name or Employee ID here..."
                    v-model="searchKey"
                    @change='eventSearch(searchKey)'
                >
            </input-search>

            <v-client-table :data="rows" :columns="columns" :options="options">
                    <template slot="actions" slot-scope="props">
                        <button class='btn btn-warning btn-xs'
                             @click="actionRow(props.row, 'view')"
                             v-if="rule.incident_report_history.child_rules.view.is_enable">
                             <i class="fa fa-eye"></i>
                             View
                        </button>

                        <button type="button" class="btn btn-primary btn-xs"
                            :disabled="props.row.incident_report.is_generate_nte_invalid_ir == 2"
                            @click="eventGenerateNte(props.row.complainant.id, props.row.respondent_user.id,
                            props.row.incident_report.is_generate_nte_invalid_ir)"
                            v-if="rule.incident_report_history.child_rules.nte.is_enable">
                            <i class="fa fa-download"></i> NTE
                        </button>
                        <button type="button" class="btn btn-info btn-xs"
                            :disabled="props.row.incident_report.is_generate_nte_invalid_ir == 2 ||
                            (props.row.incident_report.is_visible_respondent == 0 &&
                            props.row.respondent_user.employee_no == user.employee_no)"
                            @click="eventGenerateIrr(props.row.complainant.id, props.row.respondent_user.id,
                            props.row.ir_number, props.row.incident_report.irr_id)"
                            v-if="rule.incident_report_history.child_rules.cc.is_enable">
                            <i class="fa fa-download"></i> DA
                        </button>
                        <button
                            class='btn btn-default btn-xs'
                            @click="downloadAcknoweledgeLetter(props.row)"
                            v-if="props.row.incident_report.irr_id == 9 && props.row.type_of_date == 0 &&
                            props.row.start_date != null && props.row.takes_date != null && props.row.last_date != null
                            || props.row.incident_report.irr_id == 9 && props.row.type_of_date == 1 &&
                            props.row.start_date != null"
                        >
                            <i class="fa fa-paperclip"></i> Acknowledgement
                        </button>
                        <button class='btn btn-danger btn-xs'
                            v-if="props.row.reopen && props.row.reopen.ir_number == props.row.ir_number
                            && (role == 'Super Admin Access' || role == 'HR Admin Access' || role == 'HR Access')"
                            @click="actionRow(props.row, 'pending')">
                            <i class="fa fa-refresh"></i>
                            {{ props.row.reopen.request_status }}
                        </button>
                        <button class='btn btn-danger btn-xs'
                            @click="actionRow(props.row, 'reopen')"
                            v-else-if="role == 'Super Admin Access' || role == 'HR Admin Access'
                            || role == 'HR Access'">
                            <i class="fa fa-refresh"></i>
                            Reopen
                        </button>
                    </template>
                    <template slot="is_visible_to_respondent" slot-scope="props">
                        <div v-if="props.row.incident_report.is_visible_respondent == 1">
                            <div class="label label-success">YES</div>
                        </div>
                        <div v-else>
                            <div v-if="role == 'Super Admin Access' || role == 'HR Admin Access'
                            || role == 'HR Access'">
                                <button type="button" class="btn btn-default btn-xs" @click="enableDA(props.row)">
                                    <i class="fa fa-check"></i> Enable
                                </button>
                            </div>
                            <div v-else>
                                <div class="label label-default">NO</div>
                            </div>
                        </div>
                    </template>
                    <template slot="reported" slot-scope="props">
                        {{ props.row.complainant.created_at | formatDate }}
                    </template>
                    <template slot="complainant" slot-scope="props">
                        <div v-if="props.row.respondent_user.id != user.id">
                            {{ props.row.complainant.complainant_user.first_name }}
                            {{ props.row.complainant.complainant_user.last_name }}
                        </div>
                    </template>
                    <template slot="respondent" slot-scope="props">
                        {{ props.row.respondent_user.first_name }} {{ props.row.respondent_user.last_name }}
                    </template>
                    <template slot="offense" slot-scope="props">
                        <div style="width:200px; text-align: justify;">
                            <div v-if="props.row.complainant.offense">
                                {{ props.row.complainant.offense.offense }}
                            </div>
                            <div v-else style="width:200px; text-align: justify;">
                                <!-- Multiple Offense -->
                                Multiple Offense ( {{ JSON.parse(props.row.complainant.offense_id.replace(/\\|"/gi,'')).length }} )
                            </div>
                        </div>
                    </template>
                    <!-- <template v-if="props.row.status_id > 1 && props.row.incident_report"
                        slot="date_admin_hearing" slot-scope="props"> -->
                    <template v-if="props.row.incident_report"
                        slot="date_admin_hearing" slot-scope="props">
                        <div
                        v-if="props.row.incident_report.date_admin_hearing"
                        style="width: 140px;">
                            <small>
                            {{ props.row.incident_report.date_admin_hearing }}
                            {{ props.row.incident_report.time_admin_hearing }}<br>
                            {{ props.row.incident_report.meeting_place }}
                            </small>
                        </div>
                        <div v-else>
                            N/A
                        </div>
                    </template>
                    <template slot="hrbp" slot-scope="props">
                        <span v-if="props.row.incident_report
                        && props.row.incident_report.hrbp_user">
                            {{ props.row.incident_report.hrbp_user.first_name }}
                            {{ props.row.incident_report.hrbp_user.last_name }}
                        </span>
                        <span v-else-if="props.row.hrbp_user">
                            {{ props.row.hrbp_user.first_name }}
                            {{ props.row.hrbp_user.last_name }}
                        </span>
                    </template>
                    <template v-if="props.row.ageing" slot="ageing" slot-scope="props">
                        <span v-if="props.row.complainant.offense">
                            <span v-if="props.row.complainant.offense.gravity.gravity == 'Grave'
                            && props.row.ageing > '16'">
                                <b style="color:red">{{ props.row.ageing }} working days</b>
                            </span>
                            <span v-else-if="props.row.complainant.offense.gravity.gravity != 'Grave'
                            && props.row.ageing > '12'">
                                <b style="color:red">{{ props.row.ageing }} working days</b>
                            </span>
                            <span v-else>
                               {{ props.row.ageing }} working days
                            </span>
                        </span>
                        <span v-else>
                               {{ props.row.ageing }} working days
                        </span>
                    </template>
            </v-client-table>

            <v-paginator style="margin-top: -50px; position: absolute;" :options="resource_options"
            :resource_url="resource_url" @update="updateResource" v-show="showPagination" />

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <view-form v-if="openAction == 'view'" :complainantProps="complainant_data" slot="body" />
                <reopen-form v-if="openAction == 'reopen'" :complainantProps="complainant_data" slot="body" />
            </modal>
        </the-ibox>
    </div>
</template>
<script>
    import viewForm from '@/modules/admin/incidentreport/viewForm.vue'
    import reopenForm from '@/modules/irhistory/reopenForm.vue'

    export default {
        components: {
            viewForm,
            reopenForm,
		},
        data: function () {
            return {
                user: this.$ep.user,
                rule: this.$ep.rule,
                role: this.$ep.role,
                isLoading: false,
                columns: [
                    'actions',
                    'is_visible_to_respondent',
                    'ir_number',
                    'reported',
                    'complainant',
                    'respondent',
                    'offense',
                    'incident_report.date_admin_hearing',
                    'incident_report.date_da',
                    'hrbp',
                    'ageing',
                    'da_status_stage_case'
                ],
                rows:  [],
                options: {
                    headings: {
                        'ir_number': 'IR Number',
                        'complainant.date_incident_start': 'Date Incident',
                        'offense': 'Offense',
                        'incident_report.date_admin_hearing': 'Date Admin Hearing',
                        'incident_report.date_da': 'Date Closure',
                        'hrbp': 'HRBP Name',
                        'ageing': 'Ageing',
                        'da_status_stage_case': 'DA Stage Case',
                    },
                    sortable: [
                        'ir_number',
                        'reported',
                        'complainant',
                        'respondent',
                        'complainant.date_incident_start',
                        'offense',
                        'incident_report.date_admin_hearing',
                        'hrbp',
                        'ageing'
                    ],
                    filterable: false,
                },
                headerName: '',
                openModal: false,
                complainant_data: {
                    res_id: '',
                    inc_id: '',
                    comp_id: '',
                    comp_remarks: '',
                    comp_is_generate_nte_invalid_ir: '',
                    comp_date_admin_hearing: '',
                    comp_is_under_investigation: '',
                    comp_irr_id: '',
                    comp_invite_user_id: '',
                    comp_type_invite: '',
                    comp_ageing: '',
                    comp_reported: '',
                    comp_ir_number: '',
                    comp_complainant: '',
                    comp_respondent: '',
                    comp_offense: '',
                    comp_category: '',
                    comp_gravity: '',
                    comp_prescriptive_period: '',
                    comp_description: '',
                    comp_date_incident: '',
                    comp_offense_description: '',
                    comp_witness: '',
                    comp_respondent_response: '',
                    comp_status_id: '',
                    comp_incident_report: '',
                    comp_action: '',
                    comp_respondent_user_id: '',
                    comp_offense_id: '',
                    comp_first_offense: '',
                    comp_second_offense: '',
                    comp_third_offense: '',
                    comp_fourth_offense: '',
                    comp_fifth_offense: '',
                    comp_sixth_offense: '',
                    comp_seventh_offense: '',
                    comp_nte_upload: '',
                    comp_attached_hr: [],
                    comp_attachments: [],
                    comp_mom_attachment: [],
                    comp_mom: '',
                    comp_signed: '',
                    comp_hrbp_user_id: '',
                    comp_rtw: 0,
                    comp_type_of_date: '',
                    comp_start_date: '',
                    comp_takes_date: '',
                    comp_last_date: '',
                    comp_respondent_email: '',
                    comp_supervisor_email: '',
                    comp_manager_email: '',
                    comp_requestor_id: '',
                    comp_approver_id: '',
                    comp_requestor_name: '',
                    comp_approver_name: '',
                    comp_reopen_request: '',
                    comp_reopen_reason: '',
                    comp_reopen_status_id: '',
                    comp_offense_id: '',
                    comp_offense_cnt: 0,
                    comp_respondent_employee_no: '',
                    comp_date_da: '',
                    comp_archived: '',
                    comp_irr_archived: '',
                    comp_updated_at: '',
                },
                openAction: '',
                note_data: {
                    comp_complainant_id: '',
                    comp_complainant_user_id: '',
                    comp_respondent_user_id: '',
                    comp_ir_number: '',
                },
                going_data: {
                    list_invites_user: [],
                    list_invites_hearing: [],
                    respondent_name: '',
                    respondent_going: '',
                    respondent_reason: '',
                    respondent_suggested_date: '',
                    respondent_suggested_time: '',
                    respondent_suggested_place: '',
                    complainant_id: '',
                    complainant_user_id: '',
                    respondent_user_id: '',
                    ir_number: '',
                },
                resource_url: '/api/admin/irhistory/all',
                resource_options: {
                    remote_data: 'data',
                    remote_current_page: 'current_page',
                    remote_last_page: 'last_page',
                    remote_next_page_url: 'next_page_url',
                    remote_prev_page_url: 'prev_page_url',
                    next_button_text: 'Go Next',
                    previous_button_text: 'Go Back'
                },
                searchKey: '',
                showPagination: true
            }
        },
        created(){
            this.$emit("update", this.updatePagination);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            updateResource(data){
                this.isLoading = true
                this.rows = data
                this.isLoading = false
            },
            eventSearch: function (searchKey) {
                if (searchKey) {
                    this.isLoading = true
                    this.showPagination = false
                    this.$constants.Admin_API.get("/search/irhistory/"+searchKey)
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else {
                    location.reload();
                }
            },
            eventModalClose: function () {
                this.complainant_data = {
                    res_id: '',
                    inc_id: '',
                    comp_id: '',
                    comp_remarks: '',
                    comp_is_generate_nte_invalid_ir: '',
                    comp_date_admin_hearing: '',
                    comp_is_under_investigation: '',
                    comp_irr_id: '',
                    comp_invite_user_id: '',
                    comp_type_invite: '',
                    comp_ageing: '',
                    comp_reported: '',
                    comp_ir_number: '',
                    comp_complainant: '',
                    comp_respondent: '',
                    comp_offense: '',
                    comp_category: '',
                    comp_gravity: '',
                    comp_prescriptive_period: '',
                    comp_description: '',
                    comp_date_incident: '',
                    comp_offense_description: '',
                    comp_witness: '',
                    comp_respondent_response: '',
                    comp_status_id: '',
                    comp_incident_report: '',
                    comp_action: '',
                    comp_respondent_user_id: '',
                    comp_offense_id: '',
                    comp_first_offense: '',
                    comp_second_offense: '',
                    comp_third_offense: '',
                    comp_fourth_offense: '',
                    comp_fifth_offense: '',
                    comp_sixth_offense: '',
                    comp_seventh_offense: '',
                    comp_nte_upload: '',
                    comp_attached_hr: [],
                    comp_attachments: [],
                    comp_mom_attachment: [],
                    comp_mom: '',
                    comp_signed: '',
                    comp_hrbp_user_id: '',
                    comp_rtw: 0,
                    comp_type_of_date: '',
                    comp_start_date: '',
                    comp_takes_date: '',
                    comp_last_date: '',
                    comp_respondent_email: '',
                    comp_supervisor_email: '',
                    comp_manager_email: '',
                    comp_requestor_id: '',
                    comp_approver_id: '',
                    comp_requestor_name: '',
                    comp_approver_name: '',
                    comp_reopen_request: '',
                    comp_reopen_reason: '',
                    comp_reopen_status_id: '',
                    comp_offense_id: '',
                    comp_offense_cnt: 0,
                    comp_respondent_employee_no: '',
                    comp_date_da: '',
                    comp_archived: '',
                    comp_irr_archived: '',
                    comp_multi_offense_id: [],
                    initial_irr_id: '',
                    initial_instance: '',
                };
                this.going_data = {
                    invites_hearing: [],
                    invites_name: [],
                    invites_going: [],
                    invites_reason: [],
                    invites_suggested_date: [],
                    invites_suggested_time: [],
                    invites_suggested_place: [],
                    respondent_name: '',
                    respondent_going: '',
                    respondent_reason: '',
                    respondent_suggested_date: '',
                    respondent_suggested_time: '',
                    respondent_suggested_place: '',
                };
            },
            // getList: function () {
            //     this.isLoading = true
            //     this.$constants.Admin_API.get("/irhistory/all")
            //     .then(response => {
            //         this.rows = response.data;
            //         this.isLoading = false
            //     })
            //     .catch(error => {
            //         this.globalErrorHandling(error);
            //     });
            // },
            downloadAcknoweledgeLetter: function (incident) {
                window.location.href = "/api/respondent/download/acknowledgement_letter/"+incident.respondent_user.id;
            },
            actionRow: function (props_row, button_action) {
                this.openModal = true


                if (props_row) {

                    if(props_row.attached_hr){
                        this.complainant_data.comp_attached_hr = props_row.attached_hr;
                    }

                    this.complainant_data.res_id = props_row.id;
                    this.complainant_data.comp_id = props_row.complainant.id;
                    this.complainant_data.comp_reported = props_row.created_at;
                    this.complainant_data.comp_ir_number = props_row.ir_number;
                    this.complainant_data.comp_date_incident_start = props_row.complainant.date_incident_start;
                    this.complainant_data.comp_date_incident_end = props_row.complainant.date_incident_end;
                    this.complainant_data.comp_description = props_row.complainant.description
                    this.complainant_data.comp_incident_report = props_row.incident_report;
                    this.complainant_data.comp_witness = props_row.complainant.witness_user;
                    this.complainant_data.comp_hrbp_acknowledge_response = props_row.hrbp_acknowledge_response;
                    this.complainant_data.comp_type_of_date = props_row.type_of_date;
                    this.complainant_data.comp_start_date =  props_row.start_date;
                    this.complainant_data.comp_takes_date = props_row.takes_date;
                    this.complainant_data.comp_last_date =  props_row.last_date;
                    this.complainant_data.comp_date_da =  props_row.incident_report.date_da;
                    this.complainant_data.comp_archived =  props_row.archived;
                    this.complainant_data.comp_irr_archived =  props_row.irr_exist;

                    if (props_row.date_response) {
                        this.complainant_data.comp_date_response = props_row.date_response
                    }

                    if (props_row.incident_report.minutes_of_the_meeting_attachment_id) {
                        this.complainant_data.comp_mom_attachment = props_row.incident_report.mom_attachment;
                    }

                    if (props_row.mom) {
                        this.complainant_data.comp_mom = props_row.mom;
                    }
                    if (props_row.progression_occurence) {
                        this.complainant_data.comp_first_offense = props_row.progression_occurence.first_respondent_id;
                        this.complainant_data.comp_second_offense = props_row.progression_occurence.second_respondent_id;
                        this.complainant_data.comp_third_offense = props_row.progression_occurence.third_respondent_id;
                        this.complainant_data.comp_fourth_offense = props_row.progression_occurence.fourth_respondent_id;
                        this.complainant_data.comp_fifth_offense = props_row.progression_occurence.fifth_respondent_id;
                        this.complainant_data.comp_sixth_offense = props_row.progression_occurence.sixth_respondent_id;
                        this.complainant_data.comp_seventh_offense = props_row.progression_occurence.seventh_respondent_id;
                        this.complainant_data.comp_first_offense = this.complainant_data.comp_first_offense ? String("000000000" + this.complainant_data.comp_first_offense).slice(-9) : null;
                        this.complainant_data.comp_second_offense = this.complainant_data.comp_second_offense ? String("000000000" + this.complainant_data.comp_second_offense).slice(-9) : null;
                        this.complainant_data.comp_third_offense =  this.complainant_data.comp_third_offense ? String("000000000" + this.complainant_data.comp_third_offense).slice(-9) : null;
                        this.complainant_data.comp_fourth_offense = this.complainant_data.comp_fourth_offense ? String("000000000" + this.complainant_data.comp_fourth_offense).slice(-9) : null;
                        this.complainant_data.comp_fifth_offense =  this.complainant_data.comp_fifth_offense ? String("000000000" + this.complainant_data.comp_fifth_offense).slice(-9) : null;
                        this.complainant_data.comp_sixth_offense =  this.complainant_data.comp_sixth_offense ? String("000000000" + this.complainant_data.comp_sixth_offense).slice(-9) : null;
                        this.complainant_data.comp_seventh_offense = this.complainant_data.comp_seventh_offense ? String("000000000" + this.complainant_data.comp_seventh_offense).slice(-9) : null;
                    }
                    if (props_row.complainant.complainant_user) {
                        this.complainant_data.comp_complainant = props_row.complainant.complainant_user.first_name
                                                        +" "+props_row.complainant.complainant_user.last_name;

                        this.complainant_data.comp_complainant_user_id = props_row.complainant.complainant_user.id;
                    }

                    if (props_row.respondent_user) {
                        this.complainant_data.comp_respondent = props_row.respondent_user.first_name
                                                                +" "+props_row.respondent_user.last_name;

                        this.complainant_data.comp_respondent_user_id = props_row.respondent_user.id;
                    }

                    if(props_row.complainant.remarks == null){
                        this.complainant_data.comp_remarks = '';
                    } else {
                        this.complainant_data.comp_remarks = props_row.complainant.remarks;
                    }

                    if(props_row.response == ''){
                        this.complainant_data.comp_respondent_response = '';
                    } else {
                        this.complainant_data.comp_respondent_response = props_row.response;
                    }

                    if(props_row.complainant.attachments){
                        this.complainant_data.comp_attachments = props_row.complainant.attachments;
                    }

                    this.complainant_data.comp_respondent_email =  props_row.respondent_user.email_address;

                    if(props_row.respondent_user.employee_supervisor && props_row.respondent_user.employee_supervisor.supervisor_assign){
                        this.complainant_data.comp_supervisor_email =
                        props_row.respondent_user.employee_supervisor.supervisor_assign.email_address;
                    }

                    if(props_row.respondent_user.employee_supervisor && props_row.respondent_user.employee_supervisor.supervisor_assign
                        && props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor
                        && props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign){
                        this.complainant_data.comp_manager_email =
                        props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.email_address;
                    }
                    if (props_row.complainant.offense) {

                        this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                        this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                        this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                        this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                        this.complainant_data.comp_offense_id = props_row.complainant.offense.id;
                        this.complainant_data.comp_prescriptive_period
                        = props_row.complainant.offense.gravity.prescriptive_period;

                    } else if (props_row.complainant.attendancepointssystem) {

                        if (props_row.complainant.attendancepointssystem) {
                            this.complainant_data.comp_offense =
                            props_row.complainant.attendancepointssystem.type_infraction;
                        }

                        this.complainant_data.comp_category = "Attendance";
                        this.complainant_data.comp_gravity
                        = "Attendance Points - " +
                        props_row.complainant.attendancepointssystem.attendancepoint.attendance_points;

                        this.complainant_data.comp_offense_description
                        = props_row.complainant.attendancepointssystem.definition;

                        this.complainant_data.comp_offense_id = props_row.complainant.attendancepointssystem.id;
                        this.complainant_data.comp_prescriptive_period = '';
                    } else {

                        /* multiple Offense */
                        this.complainant_data.comp_multi_offense_id  = JSON.parse(props_row.complainant.offense_id.replace(/\\|"/gi,''));
                        // this.complainant_data.comp_offense_cnt  = JSON.parse(props_row.complainant.offense_id.replace(/\\|"/gi,'')).length;
                    }
                    if (props_row.incident_report.initial_irr_id) {
                        this.complainant_data.initial_irr_id = props_row.incident_report.initial_irr_id;
                    }
                    if (props_row.incident_report.initial_instance) {
                        this.complainant_data.initial_instance = props_row.incident_report.initial_instance;
                    }
                }
                if (button_action == "view") {
                    this.headerName = "View Incident Report History";
                    this.openAction = "view";
                    this.complainant_data.comp_status_id = props_row.status_id;
                    if (props_row.incident_report.updated_at) {
                        this.complainant_data.comp_updated_at = props_row.incident_report.updated_at;
                    }
                } else if (button_action == "pending") {
                    this.headerName = "Reopen Incident Report";
                    this.openAction = "reopen";
                    this.complainant_data.comp_status_id = props_row.reopen.status_id;
                    this.complainant_data.comp_reopen_request = props_row.reopen.request_status;
                    this.complainant_data.comp_reopen_reason = props_row.reopen.reason;

                    this.complainant_data.comp_requestor_id = props_row.reopen.requestor_name.id;
                    this.complainant_data.comp_requestor_name = props_row.reopen.requestor_name.first_name
                    + " " + props_row.reopen.requestor_name.last_name;

                    this.complainant_data.comp_approver_id = props_row.reopen.approver_name.id;
                    this.complainant_data.comp_approver_name = props_row.reopen.approver_name.first_name
                    + " " + props_row.reopen.approver_name.last_name;

                    this.complainant_data.comp_respondent_employee_no = props_row.respondent_user.employee_no;


                } else if (button_action == "reopen") {
                    this.headerName = "Reopen Close into Incident Report";
                    this.headerName = "Reopen Closed into Incident Report";
                    this.openAction = "reopen";

                    this.complainant_data.comp_status_id = props_row.status_id;

                    this.complainant_data.comp_requestor_id = this.user.id;
                    this.complainant_data.comp_requestor_name = this.user.first_name + " " + this.user.last_name;

                    this.complainant_data.comp_approver_id = props_row.hrsl_user.id;
                    this.complainant_data.comp_approver_name = props_row.hrsl_user.first_name + " " +
                    props_row.hrsl_user.last_name;
                }
            },
            eventGenerateNte: function (complainant_id, respondent_user, is_generate_nte_invalid_ir) {
                let titleDL = '';

                if (is_generate_nte_invalid_ir == 4) {
                    titleDL = ' with Preventive Suspension and Admin Hearing';
                }

                this.$swal({
                    title: 'Are you sure you want to Generate NTE ' + titleDL + ' PDF file?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            window.location.href = "/api/admin/incidentreport/generate/nte/"
                            + complainant_id + "/" + respondent_user;
                            this.$success('Generate NTE  ' + titleDL + ' downloaded successfully!');
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Generate NTE  ' + titleDL + ' has been cancelled');
                        }
                    })
            },
            eventGenerateIrr: function (complainant_id, respondent_user, ir_number, irr_id) {
                let getTitle = '';

                if (this.role == "Super Admin Access" || this.role == "HR Admin Access"
                || this.role == "HR Access") {
                    getTitle = 'Are you sure you want to Preview Generate Case Closure?';

                } else {
                    getTitle = 'Are you sure you want to Generate Case Closure PDF file?';
                }

                this.$swal({
                    title: getTitle,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            if (this.role == "Super Admin Access" || this.role == "HR Admin Access"
                            || this.role == "HR Access") {
                                window.open(
                                    "/api/admin/incidentreport/generate/irr/preview/"
                                    + complainant_id + "/" + respondent_user,
                                    "_blank"
                                );
                            } else {
                                window.location.href = "/api/admin/incidentreport/generate/irr/"
                                + complainant_id + "/" + respondent_user;
                                this.$success('Generate Case Closure downloaded successfully!');
                            }
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Generate Case Closure has been cancelled');
                        }
                    })
            },
            enableDA: function (props_row) {
                this.$swal({
                    title: "Are you sure you want to enable respondent DA visibility?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                }).then((result) => {
                    if (result.value) {
                        this.$constants.Admin_API.get("/irhistory/enable/da/"+props_row.ir_id)
                        .then(response => {
                            response;
                            this.$success('Enable respondent DA visibility successfully!');
                            location.reload();
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$failure('Enable respondent DA visibility cancelled');
                    }
                })


            }
        }
    }
</script>
