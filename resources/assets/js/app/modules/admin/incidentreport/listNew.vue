<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div>
            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                    <div v-if="props.row.status_id == 1">
                        <span v-if="role == 'Super Admin Access' || role == 'HR Admin Access'
                        || role == 'HR Access'">
                            <button class='btn btn-default btn-xs'
                                @click="viewEditNoteRow(props.row, 'note')"
                                v-if="rule.incident_report.child_rules.new.child_rules.note.is_enable">
                                <i class="fa fa-paperclip"></i> Note
                            </button>
                            <button class='btn btn-warning btn-xs'
                            @click="viewEditNoteRow(props.row, 'review')"
                            v-if="rule.incident_report.child_rules.new.child_rules.review.is_enable">
                                <i class="fa fa-eye"></i>
                                {{ labels.reviewBtn }}
                            </button>
                            <template>
                                <button type="button" class="btn btn-primary btn-xs"
                                    @click="complainantEdit(props.row)">
                                    <i class="fa fa-pencil-square"></i> Edit Complain
                                </button>
                            </template>
                        </span>
                        <span v-else>
                            <button class='btn btn-default btn-xs'
                                @click="viewEditNoteRow(props.row, 'note')"
                                :disabled="props.row.hrbp_employee_no != user.employee_no"
                                v-if="rule.incident_report.child_rules.new.child_rules.note.is_enable">
                                <i class="fa fa-paperclip"></i> Note
                            </button>
                            <button class='btn btn-warning btn-xs'
                            @click="viewEditNoteRow(props.row, 'review')"
                            :disabled="props.row.hrbp_employee_no != user.employee_no"
                            v-if="rule.incident_report.child_rules.new.child_rules.review.is_enable">
                                <i class="fa fa-eye"></i>
                                {{ labels.reviewBtn }}
                            </button>
                        </span>
                    </div>

                </template>

                <template slot="reported" slot-scope="props">
                    {{ props.row.complainant.created_at | formatDate }}
                </template>

                <template slot="complainant" slot-scope="props" v-if="props.row.complainant.complainant_user">
                    <div v-if="props.row.respondent_user_id != user.id && props.row.complainant.complainant_user">
                        {{ props.row.complainant.complainant_user.first_name }}
                        {{ props.row.complainant.complainant_user.last_name }}
                    </div>
                </template>

                <template slot="respondent" slot-scope="props" v-if="props.row.respondent_user">
                    {{ props.row.respondent_user.first_name }} {{ props.row.respondent_user.last_name }}
                </template>

                <template slot="offense" slot-scope="props">
                    <div v-if="props.row.offense" style="width:200px; text-align: justify;">
                        {{ props.row.offense }}
                    </div>
                    <div v-else style="width:200px; text-align: justify;">
                         Multiple Offense ( {{ JSON.parse(props.row.complainant.offense_id.replace(/\\|"/gi,'')).length }} )
                    </div>
                </template>

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
                    <span>
                        <span v-if="props.row.complainant.offense &&
                        props.row.complainant.offense.gravity.gravity == 'Grave'
                            && props.row.ageing > '16'">
                            <b style="color:red">{{ props.row.ageing }} working days</b>
                        </span>
                        <span v-else-if="props.row.complainant.offense
                            && props.row.complainant.offense.gravity.gravity != 'Grave'
                            && props.row.ageing > '12'">
                            <b style="color:red">{{ props.row.ageing }} working days</b>
                        </span>
                        <span v-else>
                           {{ props.row.ageing }} working days
                        </span>
                    </span>
                </template>

            </v-client-table>

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <request-edit-form v-if="openAction == 'approve_edit'" :complainantProps="complainant_data"
                    slot="body" />
                <request-pending-form v-if="openAction == 'request_edit' || openAction == 'complainant_request'"
                    :complainantProps="complainant_data" slot="body" />
                <view-form v-if="openAction == 'review' || openAction == 'view' || openAction == 'edit'"
                    :complainantProps="complainant_data" slot="body" />
                <reply-form v-if="openAction == 'reply'" :respondentProps="complainant_data" slot="body" />
                <note v-if="openAction == 'note'
                    && (role == 'Super Admin Access'
                    || role == 'HR Admin Access'
                    || role == 'HR Access')" :noteProps="note_data" slot="body" />
                <note-com-res v-else-if="openAction == 'note'
                    && ( role == 'Regular User Access' || role == 'Regular Supervisor Access')"
                    :noteProps="note_data" slot="body" />
                <going v-if="openAction == 'going'" :goingProps="going_data" slot="body" />

                <!-- <on-hold-edit-form v-if="openAction == 'approve_onhold'" :complainantProps="complainant_data"
                    slot="body" /> -->
                <on-hold-form v-if="openAction == 'onhold_request' || openAction == 'approve_onhold'"
                    :complainantProps="complainant_data" slot="body" />
                <complainant-edit-form v-if="openAction == 'complainant_edit'"
				:complainantProps="complainant_data" slot="body" />
            </modal>
        </div>
    </div>
</template>
<script>
    import requestPendingForm from '@/modules/admin/incidentreport/requestEditPendingForm.vue'
    import requestEditForm from '@/modules/admin/incidentreport/requestEditForm.vue'
    import onHoldForm from '@/modules/admin/incidentreport/onHoldForm.vue'
    import viewForm from '@/modules/admin/incidentreport/viewForm.vue'
    import note from '@/modules/admin/incidentreport/note.vue'
    import going from '@/modules/admin/incidentreport/going.vue'
    import upload from '@/modules/admin/incidentreport/upload.vue'
    import replyForm from '@/modules/complainant_respondent/replyForm.vue'
    import noteComRes from '@/modules/complainant_respondent/note.vue'
    import complainantEditForm from '@/modules/admin/incidentreport/complainantEditForm.vue'

    export default {
        components: {
            viewForm,
            replyForm,
            note,
            noteComRes,
            going,
            upload,
            requestEditForm,
            requestPendingForm,
            onHoldForm,
            complainantEditForm
        },

        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            edit: 'Edit Incident Report',
                            view: 'View Incident Report',
                            review: 'Review Incident Report',
                            note: 'Notes',
                            // upload: 'Upload Signed NTE / IRR',
                            going: 'Attendance Admin Hearing',
                            viewBtn: 'View',
                            reviewBtn: 'Review',
                            replyBtn: 'Reply',
                        }
                    }
                },
            },

        data: function () {
            return {
                user: this.$ep.user,
                role: this.$ep.role,
                rule: this.$ep.rule,
                isLoading: false,
                columns: [
                    'actions',
                    'ir_number',
                    'reported',
                    'complainant',
                    'respondent_user.employee_no',
                    'respondent',
                    'offense',
                    'incident_report.date_admin_hearing',
                    'hrbp',
                    'ageing'
                ],
                rows:  [],
                options: {
                    headings: {
                        'ir_number': 'IR Number',
                        'respondent_user.employee_no': 'Emp. No',
                        'complainant.date_incident_start': 'Date Incident',
                        'offense': 'Offense',
                        'incident_report.date_admin_hearing': 'Date Admin Hearing',
                        'hrbp': 'HRBP Name',
                        'ageing': 'Ageing',
                    },
                    sortable: [
                        'ir_number',
                        'reported',
                        'respondent_user.employee_no',
                        'respondent',
                        'complainant.date_incident_start',
                        'offense',
                        'incident_report.date_admin_hearing',
                        'hrbp',
                        'ageing'
                    ],
                    filterable: [
                        'created_at',
                        'respondent_user.employee_no',
                        'respondent_user.first_name',
                        'respondent_user.last_name',
                        'ir_number',
                        'complainant.date_incident_start',
                        'offense',
                        'incident_report.date_admin_hearing',
                        'reported',
                        'respondent',
                        'complainant.complainant_user.first_name',
                        'complainant.complainant_user.last_name',
                        'incident_report.hrbp_user.first_name',
                        'incident_report.hrbp_user.last_name',
                        'hrbp_user.first_name',
                        'hrbp_user.last_name',
                        'ageing'
                    ],
                },
                headerName: '',
                openModal: false,
                complainant_data: {
                    inc_id: '',
                    comp_id: '',
                    comp_remarks: '',
                    comp_date_response: '',
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
                    comp_status: '',
                    comp_incident_report: '',
                    comp_action: '',
                    comp_respondent_user_id: '',
                    comp_offense_id: [],
                    comp_offense_instance: [],
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
                    comp_signed: '',
                    comp_hrbp_user_id: '',
                    comp_hrbp_user_first: '',
                    comp_hrbp_user_last: '',
                    comp_hrbp_user_employee_no: '',
                    comp_hrbp_user_designation: '',
                    comp_rtw: 0,
                    comp_type_of_date: '',
                    comp_start_date: '',
                    comp_takes_date: '',
                    comp_first_comment: '',
                    comp_second_comment: '',
                    comp_last_date: '',
                    comp_respondent_email: '',
                    comp_supervisor_email: '',
                    comp_manager_email: '',
                    comp_progress_occurence: '',
                    comp_offense_id: '',
                    comp_offense_cnt: 0,
                    comp_archived: '',
                    comp_prepared_by_first: '',
                    comp_prepared_by_last: '',
                    comp_prepared_by_designation: '',
                    comp_prepared_by_empno: '',
                    comp_requested_by_first: '',
                    comp_requested_by_last: '',
                    comp_requested_by_designation: '',
                    comp_requested_by_empno: '',
                    comp_approved_by_first: '',
                    comp_approved_by_last: '',
                    comp_approved_by_designation: '',
                    comp_approved_by_empno: '',
                    comp_noted1_by_first: '',
                    comp_noted1_by_last: '',
                    comp_noted1_by_designation: '',
                    comp_noted1_by_empno: '',
                    comp_noted2_by_first: '',
                    comp_noted2_by_last: '',
                    comp_noted2_by_designation: '',
                    comp_noted2_by_empno: '',
                    comp_noted3_by_first: '',
                    comp_noted3_by_last: '',
                    comp_noted3_by_designation: '',
                    comp_noted3_by_empno: '',
                    comp_irr_archived: '',
                    comp_supervisor_first: '',
                    comp_supervisor_last: '',
                    comp_supervisor_employee_no: '',
                    comp_supervisor_designation: '',
                    comp_manager_first: '',
                    comp_manager_last: '',
                    comp_manager_employee_no: '',
                    comp_manager_designation: '',
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
            }
        },
        created(){
            if (this.$route.hash == '#new' || this.$route.hash === "") {
                this.getList();
            }
            this.$emit("update", this.updatePagination);
            this.$bus.$on('newList', this.getList);
            this.$bus.$on('updateList', this.getList);

            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            eventModalClose: function () {
                this.complainant_data = {
                    inc_id: '',
                    comp_id: '',
                    comp_remarks: '',
                    comp_is_attachment_tk_image: '',
                    comp_date_response: '',
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
                    comp_status: '',
                    comp_incident_report: '',
                    comp_action: '',
                    comp_respondent_user_id: '',
                    comp_progress_occurence: '',
                    comp_offense_id: '',
                    comp_first_offense: '',
                    comp_second_offense: '',
                    comp_third_offense: '',
                    comp_fourth_offense: '',
                    comp_fifth_offense: '',
                    comp_sixth_offense: '',
                    comp_seventh_offense: '',
                    comp_progression_occurence_multi: [],
                    comp_nte_upload: '',
                    comp_type_of_date: '',
                    comp_start_date: '',
                    comp_takes_date: '',
                    comp_last_date: '',
                    comp_first_comment: '',
                    comp_second_comment: '',
                    comp_mom: '',
                    comp_hrbp_user: '',
                    comp_offense_check: '',
                    comp_offense_cnt: 0,
                    comp_initial_irr_id: '',
                    comp_initial_instance: '',
                    comp_archived: '',
                    comp_irr_archived: '',
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
            getList: function () {
                this.isLoading = true
                this.$constants.Incident_API.get("/status/incident/"+1)
                .then(response => {
                    this.rows = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventDownloadForm: function(form_type, incident_id, ir_number) {
                let downloadForm
                if (form_type == "RTW") {
                    downloadForm = "RTW Form"
                } else if(form_type == "SN") {
                    downloadForm = "SN Form"
                }

                this.$swal({
                    title: 'Download Form',
                    text: 'Are you sure you want to download '+ downloadForm +'?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                .then((result) => {
                if (result.value) {
                window.location.href = "/api/admin/incidentreport/download/"+form_type+"/"+incident_id+"/"+ir_number;
                this.$success(downloadForm + ' downloaded successfully!');

                } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                    this.$failure(downloadForm + ' has been cancelled');
                }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
            });
            },
            requestEdit: function (props_row, button_action) {
                this.openModal = true
                if (button_action == "approve_edit") {
                    this.headerName = "Edit Approved by "+props_row.complainant_request.approver_name.first_name+
                    " "+props_row.complainant_request.approver_name.last_name;
                    this.openAction = "approve_edit";
                    this.complainant_data.comp_request = props_row.complainant_request.id;
                    this.complainant_data.comp_attached_hr = props_row.attached_hr;
                    this.complainant_data.comp_is_attachment_tk_image = props_row.complainant.is_attachment_tk_image;
                    this.complainant_data.comp_attachments = props_row.complainant.attachments;
                    this.complainant_data.res_id = props_row.id;
                    this.complainant_data.comp_id = props_row.complainant.id;
                    this.complainant_data.comp_status = props_row.status_id;
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
                    this.complainant_data.comp_first_comment =  props_row.first_comment;
                    this.complainant_data.comp_second_comment =  props_row.second_comment;
                    this.complainant_data.comp_ir_id =  props_row.incident_report.id;
                    this.complainant_data.comp_respondent = props_row.respondent_user.first_name + " "
                        + props_row.respondent_user.last_name + "- HRBP:" + props_row.hrbp_user.first_name + " "
                        + props_row.hrbp_user.last_name;
                    this.complainant_data.comp_respondent_user_id = props_row.respondent_user.id
                    this.complainant_data.comp_witness = props_row.complainant.witness_user;


                    // request approve
                    this.complainant_data.comp_approver_id = props_row.complainant_request.approver_name.id;
                    this.complainant_data.comp_approver_name = props_row.complainant_request.approver_name.first_name
                    + " " + props_row.complainant_request.approver_name.last_name;
                    // last part

                    if (props_row.complainant.offense) {
                        this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                        this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                        this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                        this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                        this.complainant_data.comp_offense_id = props_row.complainant.offense_id;
                        this.complainant_data.comp_prescriptive_period
                            = props_row.complainant.offense.gravity.prescriptive_period;

                        this.complainant_data.comp_offense_check = 'offense';
                    } else if (props_row.complainant.attendancepointssystem) {

                        this.complainant_data.comp_offense
                            = props_row.complainant.attendancepointssystem.type_infraction;

                        this.complainant_data.comp_category = "Attendance With Points";
                        this.complainant_data.comp_gravity = "Attendance Points - " +
                            props_row.complainant.attendancepointssystem.attendancepoint.attendance_points;

                        this.complainant_data.comp_offense_description
                            = props_row.complainant.attendancepointssystem.definition;

                        this.complainant_data.comp_offense_id = props_row.complainant.attendancepointssystem.id;
                        this.complainant_data.comp_prescriptive_period = '';
                        this.complainant_data.comp_offense_check = 'attendance';
                    }
                } else if (button_action == "request_edit") {
                    this.headerName = "Complainant Request To Edit";
                    this.openAction = "request_edit";
                    this.complainant_data.res_id = props_row.id;

                    this.complainant_data.comp_id = props_row.complainant.id;
                    this.complainant_data.comp_status = props_row.status_id;
                    this.complainant_data.comp_reported = props_row.created_at;
                    this.complainant_data.comp_ir_number = props_row.ir_number;

                    this.complainant_data.comp_requestor_id = this.user.id;
                    this.complainant_data.comp_requestor_name = this.user.first_name + " " + this.user.last_name;

                    this.complainant_data.comp_approver_id = props_row.hrbp_user.id;
                    this.complainant_data.comp_approver_name = props_row.hrbp_user.first_name + " " +
                    props_row.hrbp_user.last_name;
                } else if (button_action == 'complainant_request') {
                    this.headerName = "Request To Edit IR";
                    this.openAction = "complainant_request";
                    this.complainant_data.comp_request_edit = props_row.request_status;
                    this.complainant_data.comp_request_id = props_row.complainant_request.id;
                    this.complainant_data.comp_request_reason = props_row.complainant_request.reason;

                    this.complainant_data.comp_requestor_id = props_row.complainant_request.requestor_name.id;
                    this.complainant_data.comp_requestor_name = props_row.complainant_request.requestor_name.first_name
                    + " " + props_row.complainant_request.requestor_name.last_name;

                    this.complainant_data.comp_approver_id = props_row.complainant_request.approver_name.id;
                    this.complainant_data.comp_approver_name = props_row.complainant_request.approver_name.first_name
                    + " " + props_row.complainant_request.approver_name.last_name;
                }
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
                    this.complainant_data.comp_archived =  props_row.archived;
                    this.complainant_data.comp_irr_archived =  props_row.irr_exist;

                    if (props_row.date_response) {
                        this.complainant_data.comp_date_response = props_row.date_response
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

                    if(props_row.respondent_user.employee_supervisor.supervisor_assign){
                        this.complainant_data.comp_supervisor_email =
                        props_row.respondent_user.employee_supervisor.supervisor_assign.email_address;
                    }

                    if(props_row.respondent_user.employee_supervisor.supervisor_assign
                    && props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign){
                        this.complainant_data.comp_manager_email =
                        props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.email_address;
                    }

                    if (props_row.complainant.offense) {

                        this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                        this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                        this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                        this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                        this.complainant_data.comp_offense_id = props_row.complainant.offense_id;
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
                    }
                }
            },
            complainantEdit: function (props_row) {
                this.openModal = true
                this.openAction = "complainant_edit";
                this.headerName = 'Edit Complain IR Number: #' + props_row.ir_number;
                this.complainant_data.comp_id = props_row.complainant.id;
                this.complainant_data.comp_ir_number = props_row.ir_number;
                this.complainant_data.comp_date_incident_start = props_row.complainant.date_incident_start;
                this.complainant_data.comp_date_incident_end = props_row.complainant.date_incident_end;
                this.complainant_data.comp_description = props_row.complainant.description
                this.complainant_data.comp_respondent = props_row.respondent_user.first_name + " "
                + props_row.respondent_user.last_name + "- HRBP:" + props_row.hrbp_user.first_name + " "
                + props_row.hrbp_user.last_name;
                this.complainant_data.comp_respondent_user_id = props_row.respondent_user.id
                if (props_row.complainant.offense) {
                    this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                    this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                    this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                    this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                    this.complainant_data.comp_offense_id = props_row.complainant.offense_id;
                    this.complainant_data.comp_offense_check = 'offense';
                } else if (props_row.complainant.offense_id.length > 1) {
                    this.complainant_data.comp_multiple_offense = props_row.complainant.offense_id;
                }
            },
            onHoldRequest: function (props_row, button_action) {
                this.openModal = true
                if (button_action == "approve_onhold") {
                    this.headerName = "Request To Approve On Hold IR";
                    this.openAction = "approve_onhold";
                    this.complainant_data.comp_request_edit = props_row.request_status;
                    this.complainant_data.comp_request_id = props_row.onhold_request.id;
                    this.complainant_data.comp_request_reason = props_row.onhold_request.reason;

                    this.complainant_data.comp_requestor_id = props_row.onhold_request.requestor_name.id;
                    this.complainant_data.comp_requestor_name = props_row.onhold_request.requestor_name.first_name
                    + " " + props_row.onhold_request.requestor_name.last_name;

                    this.complainant_data.comp_approver_id = props_row.onhold_request.approver_name.id;
                    this.complainant_data.comp_approver_name = props_row.onhold_request.approver_name.first_name
                    + " " + props_row.onhold_request.approver_name.last_name;
                } else if (button_action == 'onhold_request') {
                    this.headerName = "Request To On Hold IR";
                    this.openAction = "onhold_request";
                    this.complainant_data.res_id = props_row.id;

                    this.complainant_data.comp_id = props_row.complainant.id;
                    this.complainant_data.comp_status = props_row.status_id;
                    this.complainant_data.comp_reported = props_row.created_at;
                    this.complainant_data.comp_ir_number = props_row.ir_number;

                    this.complainant_data.comp_requestor_id = this.user.id;
                    this.complainant_data.comp_requestor_name = this.user.first_name + " " + this.user.last_name;

                    this.complainant_data.comp_approver_id = props_row.hrbp_user.id;
                    this.complainant_data.comp_approver_name = props_row.hrbp_user.first_name + " " +
                    props_row.hrbp_user.last_name;
                }
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
                    this.complainant_data.comp_archived =  props_row.archived;

                    if (props_row.date_response) {
                        this.complainant_data.comp_date_response = props_row.date_response
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

                    if(props_row.respondent_user.employee_supervisor.supervisor_assign){
                        this.complainant_data.comp_supervisor_email =
                        props_row.respondent_user.employee_supervisor.supervisor_assign.email_address;
                    }

                    if(props_row.respondent_user.employee_supervisor.supervisor_assign
                    && props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign){
                        this.complainant_data.comp_manager_email =
                        props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.email_address;
                    }

                    if (props_row.complainant.offense) {

                        this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                        this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                        this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                        this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                        this.complainant_data.comp_offense_id = props_row.complainant.offense_id;
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
                    }
                }
            },
            viewEditNoteRow: function (props_row, button_action) {
                this.openModal = true
                if(props_row){

                    if (button_action == "note") {
                        this.headerName = this.labels.note;
                        this.openAction = "note";

                        if (props_row.complainant && props_row.complainant.complainant_user_id) {
                            this.note_data.comp_complainant_id = props_row.complainant.id;
                            this.note_data.comp_complainant_user_id = props_row.complainant.complainant_user.id;
                        }

                        this.note_data.comp_respondent_user_id = props_row.respondent_user.id;
                        this.note_data.comp_ir_number = props_row.ir_number;

                    } else if (button_action == "going") {
                        this.headerName = this.labels.going;
                        this.openAction = "going";

                        this.going_data.respondent_user_id = props_row.respondent_user.id;
                        this.going_data.complainant_id = props_row.complainant.id;
                        this.going_data.complainant_user_id = props_row.complainant.complainant_user.id;
                        this.going_data.ir_number = props_row.ir_number;

                        this.going_data.list_invites_user = props_row.incident_report.invite_user;
                        this.going_data.respondent_name = props_row.respondent_user.first_name
                                                            +" "+ props_row.respondent_user.last_name;

                        if(props_row.incident_report.invite_hearing.length > 0){
                            this.going_data.list_invites_hearing = props_row.incident_report.invite_hearing;
                        }

                        if(props_row.respondent_hearing) {
                            this.going_data.respondent_id = props_row.respondent_hearing.id;
                            this.going_data.respondent_ir_id = props_row.respondent_hearing.ir_id;
                            this.going_data.respondent_going = props_row.respondent_hearing.going;
                            this.going_data.respondent_reason = props_row.respondent_hearing.reason;
                            this.going_data.respondent_suggested_date = props_row.respondent_hearing.suggested_date;
                            this.going_data.respondent_suggested_time = props_row.respondent_hearing.suggested_time;
                            this.going_data.respondent_suggested_place = props_row.respondent_hearing.suggested_place;
                            this.going_data.respondent_hr_approval = props_row.respondent_hearing.hr_approval;
                        } else {
                            this.going_data.respondent_going = "Not Confirm";
                        }
                    } else {

                        if (button_action == "view") {
                            this.headerName = this.labels.view;
                            this.openAction = "view";

                            if (props_row.attached_hr) {
                                this.complainant_data.comp_attached_hr = props_row.attached_hr;
                            }
                            if (props_row.date_response) {
                                this.complainant_data.comp_date_response = props_row.date_response;
                            }
                            if (props_row.incident_report.initial_instance) {
                                this.complainant_data.comp_initial_instance = props_row.incident_report.initial_instance;
                            }
                            if (props_row.incident_report.initial_irr_id) {
                                this.complainant_data.comp_initial_irr_id = props_row.incident_report.initial_irr_id;
                            }
                        } else if (button_action == "review") {
                            this.headerName = this.labels.review;
                            this.openAction = "review";

                        } else if (button_action == "edit") {
                            this.headerName = this.labels.edit;
                            this.openAction = "edit";
                            this.complainant_data.comp_action = button_action;
                            // this.complainant_data.comp_rtw = props_row.incident_report.rtw;

                            if(props_row.incident_report){
                                this.complainant_data.inc_id = props_row.incident_report.id;
                                this.complainant_data.comp_irr_id = props_row.incident_report.irr_id;
                            }

                            if (props_row.signed) {
                                this.complainant_data.comp_nte_upload = props_row.signed.nte_upload;
                            }

                            if(props_row.attached_hr){
                                this.complainant_data.comp_attached_hr = props_row.attached_hr;
                            }

                            if (props_row.incident_report.is_generate_nte_invalid_ir) {

                             this.complainant_data.comp_is_generate_nte_invalid_ir
                                = props_row.incident_report.is_generate_nte_invalid_ir;
                            }

                            if (props_row.signed) {
                                this.complainant_data.comp_signed = props_row.signed;
                            }

                            if (props_row.incident_report.initial_instance) {
                                this.complainant_data.comp_initial_instance = props_row.incident_report.initial_instance;
                            }
                            if (props_row.incident_report.initial_irr_id) {
                                this.complainant_data.comp_initial_irr_id = props_row.incident_report.initial_irr_id;
                            }
                        }

                        this.complainant_data.res_id = props_row.id;
                        this.complainant_data.comp_id = props_row.complainant.id;
                        // this.complainant_data.complainant_id = props_row.complainant.id;
                        this.complainant_data.comp_status = props_row.status_id;
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
                        this.complainant_data.comp_first_comment =  props_row.first_comment;
                        this.complainant_data.comp_second_comment =  props_row.second_comment;
                        this.complainant_data.comp_mom =  props_row.mom;
                        this.complainant_data.comp_archived =  props_row.archived;
                        this.complainant_data.comp_irr_archived =  props_row.irr_exist;
                        // this.complainant_data.comp_is_generate_nte_invalid_ir
                        // = props_row.incident_report.is_generate_nte_invalid_ir;

                    if (props_row.incident_report && props_row.incident_report.ir_signatories) {
                        /* prepared_by */
                        if (props_row.incident_report.ir_signatories.prepared_by_empno) {
                            this.complainant_data.comp_prepared_by_first =
                            props_row.incident_report.ir_signatories.prepared_by_first;

                            this.complainant_data.comp_prepared_by_last =
                            props_row.incident_report.ir_signatories.prepared_by_last;

                            this.complainant_data.comp_prepared_by_designation =
                            props_row.incident_report.ir_signatories.prepared_by_designation;

                            this.complainant_data.comp_prepared_by_empno =
                            props_row.incident_report.ir_signatories.prepared_by_empno;
                        }

                        /* requested_by */
                        if (props_row.incident_report.ir_signatories.requested_by_empno) {
                            this.complainant_data.comp_requested_by_first =
                            props_row.incident_report.ir_signatories.requested_by_first;

                            this.complainant_data.comp_requested_by_last =
                            props_row.incident_report.ir_signatories.requested_by_last;

                            this.complainant_data.comp_requested_by_designation =
                            props_row.incident_report.ir_signatories.requested_by_designation;

                            this.complainant_data.comp_requested_by_empno =
                            props_row.incident_report.ir_signatories.requested_by_empno;
                        }

                        /* approved_by */
                        if (props_row.incident_report.ir_signatories.approved_by_empno) {
                            this.complainant_data.comp_approved_by_first =
                            props_row.incident_report.ir_signatories.approved_by_first;

                            this.complainant_data.comp_approved_by_last =
                            props_row.incident_report.ir_signatories.approved_by_last;

                            this.complainant_data.comp_approved_by_designation =
                            props_row.incident_report.ir_signatories.approved_by_designation;

                            this.complainant_data.comp_approved_by_empno =
                            props_row.incident_report.ir_signatories.approved_by_empno;
                        }

                        /* noted1_by */
                        if (props_row.incident_report.ir_signatories.noted1_by_empno) {
                            this.complainant_data.comp_noted1_by_first =
                            props_row.incident_report.ir_signatories.noted1_by_first;

                            this.complainant_data.comp_noted1_by_last =
                            props_row.incident_report.ir_signatories.noted1_by_last;

                            this.complainant_data.comp_noted1_by_designation =
                            props_row.incident_report.ir_signatories.noted1_by_designation;

                            this.complainant_data.comp_noted1_by_empno =
                            props_row.incident_report.ir_signatories.noted1_by_empno;
                        }

                        /* noted2_by */
                        if (props_row.incident_report.ir_signatories.noted1_by_empno) {
                            this.complainant_data.comp_noted2_by_first =
                            props_row.incident_report.ir_signatories.noted2_by_first;

                            this.complainant_data.comp_noted2_by_last =
                            props_row.incident_report.ir_signatories.noted2_by_last;

                            this.complainant_data.comp_noted2_by_designation =
                            props_row.incident_report.ir_signatories.noted2_by_designation;

                            this.complainant_data.comp_noted2_by_empno =
                            props_row.incident_report.ir_signatories.noted2_by_empno;
                        }

                        /* noted3_by */
                        if (props_row.incident_report.ir_signatories.noted3_by_empno) {
                            this.complainant_data.comp_noted3_by_first =
                            props_row.incident_report.ir_signatories.noted3_by_first;

                            this.complainant_data.comp_noted3_by_last =
                            props_row.incident_report.ir_signatories.noted3_by_last;

                            this.complainant_data.comp_noted3_by_designation =
                            props_row.incident_report.ir_signatories.noted3_by_designation;

                            this.complainant_data.comp_noted3_by_empno =
                            props_row.incident_report.ir_signatories.noted3_by_empno;
                        }
                    }

                        if (props_row.hrbp_user) {
                            this.complainant_data.comp_hrbp_user =
                            props_row.hrbp_user.first_name + " " + props_row.hrbp_user.last_name;

                            this.complainant_data.comp_hrbp_user_id = props_row.hrbp_user.id;

                            this.complainant_data.comp_hrbp_user_first = props_row.hrbp_user.first_name;

                            this.complainant_data.comp_hrbp_user_last = props_row.hrbp_user.last_name;

                            this.complainant_data.comp_hrbp_user_employee_no =  props_row.hrbp_user.employee_no;

                            this.complainant_data.comp_hrbp_user_designation =  props_row.hrbp_user.designation.name;
                        }

                    /* Single Offense */
                    if (props_row.complainant.offense) {


                    this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                    this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                    this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                    this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                    this.complainant_data.comp_offense_id = props_row.complainant.offense.id;
                    this.complainant_data.comp_prescriptive_period
                    = props_row.complainant.offense.gravity.prescriptive_period;

                    for(let p=0; p < props_row.progression_occurence.length; p++){

                    if (props_row.complainant.offense.id == props_row.progression_occurence[p].offense_id) {
                    this.complainant_data.comp_first_offense =
                    props_row.progression_occurence[p].first_respondent_id &&
                    props_row.progression_occurence[p].first_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].first_respondent_id).slice(-9)
                     : null;

                    this.complainant_data.comp_second_offense =
                    props_row.progression_occurence[p].second_respondent_id &&
                    props_row.progression_occurence[p].second_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].second_respondent_id).slice(-9)
                     : null;

                    this.complainant_data.comp_third_offense =
                    props_row.progression_occurence[p].third_respondent_id &&
                    props_row.progression_occurence[p].third_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].third_respondent_id).slice(-9)
                     : null;

                    this.complainant_data.comp_fourth_offense =
                    props_row.progression_occurence[p].fourth_respondent_id  &&
                    props_row.progression_occurence[p].fourth_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].fourth_respondent_id).slice(-9)
                     : null;

                    this.complainant_data.comp_fifth_offense =
                    props_row.progression_occurence[p].fifth_respondent_id  &&
                    props_row.progression_occurence[p].fifth_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].fifth_respondent_id).slice(-9)
                     : null;

                    this.complainant_data.comp_sixth_offense =
                    props_row.progression_occurence[p].sixth_respondent_id  &&
                    props_row.progression_occurence[p].sixth_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].sixth_respondent_id).slice(-9)
                     : null;

                    this.complainant_data.comp_seventh_offense =
                    props_row.progression_occurence[p].seventh_respondent_id  &&
                    props_row.progression_occurence[p].seventh_occurrence != 'cleansed' ?
                    String("000000000" + props_row.progression_occurence[p].seventh_respondent_id).slice(-9)
                     : null;

                    if (props_row.progression_occurence[p].offense_id == props_row.complainant.offense.id) {

                    let getRefreshOccur = '';
                    if (props_row.progression_occurence[p].first_occurrence == '1st Instance'
                    || props_row.progression_occurence[p].second_occurrence == '1st Instance'
                    || props_row.progression_occurence[p].third_occurrence == '1st Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '1st Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '1st Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '1st Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '1st Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.first_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].first_occurrence == '2nd Instance'
                    || props_row.progression_occurence[p].second_occurrence == '2nd Instance'
                    || props_row.progression_occurence[p].third_occurrence == '2nd Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '2nd Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '2nd Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '2nd Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '2nd Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.second_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].first_occurrence == '3rd Instance'
                    || props_row.progression_occurence[p].second_occurrence == '3rd Instance'
                    || props_row.progression_occurence[p].third_occurrence == '3rd Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '3rd Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '3rd Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '3rd Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '3rd Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.third_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].first_occurrence == '4th Instance'
                    || props_row.progression_occurence[p].second_occurrence == '4th Instance'
                    || props_row.progression_occurence[p].third_occurrence == '4th Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '4th Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '4th Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '4th Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '4th Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.fourth_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].first_occurrence == '5th Instance'
                    || props_row.progression_occurence[p].second_occurrence == '5th Instance'
                    || props_row.progression_occurence[p].third_occurrence == '5th Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '5th Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '5th Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '5th Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '5th Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.fifth_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].first_occurrence == '6th Instance'
                    || props_row.progression_occurence[p].second_occurrence == '6th Instance'
                    || props_row.progression_occurence[p].third_occurrence == '6th Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '6th Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '6th Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '6th Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '6th Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.sixth_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].first_occurrence == '7th Instance'
                    || props_row.progression_occurence[p].second_occurrence == '7th Instance'
                    || props_row.progression_occurence[p].third_occurrence == '7th Instance'
                    || props_row.progression_occurence[p].fourth_occurrence == '7th Instance'
                    || props_row.progression_occurence[p].fifth_occurrence == '7th Instance'
                    || props_row.progression_occurence[p].sixth_occurrence == '7th Instance'
                    || props_row.progression_occurence[p].seventh_occurrence == '7th Instance') {
                    getRefreshOccur = props_row.complainant.offense.instance.seventh_instance_coc_id.toString();
                    }

                    if (props_row.progression_occurence[p].first_occurrence != null
                    && props_row.progression_occurence[p].first_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].first_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].first_occurrence == 'cleansed'
                    && props_row.progression_occurence[p].second_occurrence != null
                    && props_row.progression_occurence[p].second_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].second_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].second_occurrence == 'cleansed'
                    && props_row.progression_occurence[p].third_occurrence != null
                    && props_row.progression_occurence[p].third_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].third_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].third_occurrence == 'cleansed'
                    && props_row.progression_occurence[p].fourth_occurrence != null
                    && props_row.progression_occurence[p].fourth_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].fourth_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].fourth_occurrence == 'cleansed'
                    && props_row.progression_occurence[p].fifth_occurrence != null
                    && props_row.progression_occurence[p].fifth_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].fifth_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].fifth_occurrence == 'cleansed'
                    && props_row.progression_occurence[p].sixth_occurrence != null
                    && props_row.progression_occurence[p].sixth_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].sixth_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].sixth_occurrence == 'cleansed'
                    && props_row.progression_occurence[p].seventh_occurrence != null
                    && props_row.progression_occurence[p].seventh_occurrence != 'cleansed') {
                    this.complainant_data.comp_initial_instance =
                    props_row.progression_occurence[p].seventh_occurrence;
                    this.complainant_data.comp_initial_irr_id = getRefreshOccur

                    } else if (props_row.progression_occurence[p].first_occurrence == 'cleansed'
                    || props_row.progression_occurence[p].second_occurrence == 'cleansed'
                    || props_row.progression_occurence[p].third_occurrence == 'cleansed'
                    || props_row.progression_occurence[p].fourth_occurrence == 'cleansed'
                    || props_row.progression_occurence[p].fifth_occurrence == 'cleansed'
                    || props_row.progression_occurence[p].sixth_occurrence == 'cleansed'
                    || props_row.progression_occurence[p].seventh_occurrence == 'cleansed') {
                    this.complainant_data.comp_initial_instance = "1st Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.first_instance_coc_id.toString();

                    } else if (props_row.progression_occurence[p].first_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "1st Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.first_instance_coc_id.toString();

                    } else if (props_row.progression_occurence[p].second_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "2nd Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.second_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].third_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "3rd Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.third_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].fourth_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "4th Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.fourth_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].fifth_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "5th Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.fifth_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].sixth_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "6th Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.sixth_instance_coc_id.toString();
                    } else if (props_row.progression_occurence[p].seventh_respondent_id == null) {
                    this.complainant_data.comp_initial_instance = "7th Instance";
                    this.complainant_data.comp_initial_irr_id =
                    props_row.complainant.offense.instance.seventh_instance_coc_id.toString();
                                    }
                                }
                            }
                        }

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
                        this.complainant_data.comp_offense_id  =
                        JSON.parse(props_row.complainant.offense_id.replace(/\\|"/gi,''));
                        this.complainant_data.comp_progression_occurence_multi = props_row.progression_occurence
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

                        if(props_row.respondent_user.employee_supervisor &&
                            props_row.respondent_user.employee_supervisor.supervisor_assign){
                            this.complainant_data.comp_supervisor_email =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.email_address;

                            this.complainant_data.comp_supervisor_first =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.first_name;

                            this.complainant_data.comp_supervisor_last =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.last_name;

                            this.complainant_data.comp_supervisor_employee_no =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.employee_no;

                            this.complainant_data.comp_supervisor_designation =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.designation.name;
                        } else {
                            this.complainant_data.comp_supervisor_email =
                            props_row.complainant.complainant_user.email_address;

                            this.complainant_data.comp_supervisor_first =
                            props_row.complainant.complainant_user.first_name;

                            this.complainant_data.comp_supervisor_last =
                            props_row.complainant.complainant_user.last_name;

                            this.complainant_data.comp_supervisor_employee_no =
                            props_row.complainant.complainant_user.employee_no;

                            this.complainant_data.comp_supervisor_designation =
                            props_row.complainant.complainant_user.designation.name;
                        }

                        if(props_row.respondent_user.employee_supervisor &&
                        props_row.respondent_user.employee_supervisor.supervisor_assign &&
                        props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor &&
                        props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign){
                            this.complainant_data.comp_manager_email =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.email_address;

                            this.complainant_data.comp_manager_first =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.first_name;

                            this.complainant_data.comp_manager_last =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.last_name;

                            this.complainant_data.comp_manager_employee_no =
                            props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.employee_no;
                            if (props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation) {
                                this.complainant_data.comp_manager_designation =
                                props_row.respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation.name;
                            }
                        }

                    }
                }
            },
            eventGenerateNte: function (complainant_id, respondent_user, is_generate_nte_invalid_ir, date_hearing) {
                let titleDL = '';
                if (is_generate_nte_invalid_ir == 4 && date_hearing != null) {
                    titleDL = ' with Preventive Suspension and Admin Hearing';
                }
                else if (is_generate_nte_invalid_ir == 1 && date_hearing != null  ) {
                    titleDL = ' with Admin Hearing';
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
            viewReplyForm: function (props_row, action) {
                this.openModal = true;

                if (props_row) {
                    if (action == "view") {
                        this.openAction = "view";
                        this.headerName = this.labels.view;
                        this.complainant_data.comp_incident_report = props_row.incident_report;
                        this.complainant_data.comp_respondent_response = props_row.response;
                        this.complainant_data.comp_respondent_hrbp_acknowledge_response = props_row.hrbp_acknowledge_response;
                        this.complainant_data.comp_status = props_row.status_id;

                    // if(props_row.supervisor_approval) {
                    //     this.complainant_data.res_supervisor_approval = props_row.supervisor_approval.approver_status;
                    // }

                    } else if (action == "reply") {
                        this.openAction = "reply";
                        this.complainant_data.comp_id = props_row.id;
                        this.headerName = this.labels.reply;
                        this.complainant_data.comp_respondent_response = props_row.response;
                    }

                    this.complainant_data.res_id = props_row.id;
                    // this.complainant_data.res_ir_id = props_row.ir_id;
                    // this.complainant_data.respondent_user_id = props_row.respondent_user_id;
                    // this.complainant_data.res_reported = props_row.created_at;
                    // this.complainant_data.res_ir_number = props_row.ir_number;

                    this.complainant_data.respondent_user_id = props_row.respondent_user_id;
                    this.complainant_data.comp_reported = props_row.created_at;
                    this.complainant_data.comp_ir_number = props_row.ir_number;
                    this.complainant_data.comp_progress_occurence = props_row.progression_occurence;

                    if (props_row.complainant.offense) {
                        this.complainant_data.comp_category = props_row.complainant.offense.category.name;
                        this.complainant_data.comp_offense = props_row.complainant.offense.offense;
                        this.complainant_data.comp_gravity = props_row.complainant.offense.gravity.gravity;
                        this.complainant_data.comp_offense_description = props_row.complainant.offense.description;
                        this.complainant_data.comp_prescriptive_period = props_row.complainant.offense.gravity.prescriptive_period;

                    } else if (props_row.complainant.attendancepointssystem) {
                        this.complainant_data.comp_category = "Attendance";
                        this.complainant_data.comp_offense = props_row.complainant.attendancepointssystem.type_infraction;
                        this.complainant_data.comp_gravity
                        = "Attendance Points - " +
                        props_row.complainant.attendancepointssystem.attendancepoint.attendance_points;
                        this.complainant_data.comp_offense_description
                        = props_row.complainant.attendancepointssystem.definition;
                        this.complainant_data.comp_prescriptive_period = '';
                    }

                    this.complainant_data.comp_date_incident_start = props_row.complainant.date_incident_start;
                    this.complainant_data.comp_date_incident_end = props_row.complainant.date_incident_end;
                    this.complainant_data.comp_description = props_row.complainant.description
                    this.complainant_data.comp_remarks = props_row.complainant.remarks
                    this.complainant_data.comp_attachments = props_row.complainant.attachments

                    if(props_row.complainant.complainant_user){
                        this.complainant_data.comp_complainant = props_row.complainant.complainant_user.first_name
                                                            +" "+props_row.complainant.complainant_user.last_name;
                    }

                    if (props_row.progression_offense) {
                        if (props_row.progression_offense.respondent_first_offense) {
                            this.complainant_data.comp_first_offense = props_row.progression_offense.respondent_first_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_second_offense) {
                            this.complainant_data.comp_second_offense = props_row.progression_offense.respondent_second_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_third_offense) {
                            this.complainant_data.comp_third_offense = props_row.progression_offense.respondent_third_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_fourth_offense) {
                            this.complainant_data.comp_fourth_offense = props_row.progression_offense.respondent_fourth_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_fifth_offense) {
                            this.complainant_data.comp_fifth_offense = props_row.progression_offense.respondent_fifth_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_sixth_offense) {
                            this.complainant_data.comp_sixth_offense = props_row.progression_offense.respondent_sixth_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_seventh_offense) {
                            this.complainant_data.comp_seventh_offense = props_row.progression_offense.respondent_seventh_offense.complainant.offense.offense;
                        }
                    }

                    if(props_row.notes){
                       this.complainant_data.comp_notes = props_row.complainant.notes.notes;
                    }

                    if(props_row.complainant.witness_user){
                        this.complainant_data.comp_witness = props_row.complainant.witness_user;
                    }
                }
            },
            noteForm: function (props_row, notedBy) {
                this.openModal = true;
                if(props_row){
                    this.openAction = "note";
                    this.headerName = this.labels.note;
                    this.note_data.comp_complainant_id = props_row.complainant_id;
                    this.note_data.comp_complainant_user_id = props_row.complainant.complainant_user_id;
                    this.note_data.comp_respondent_user_id = props_row.respondent_user_id;
                    this.note_data.comp_noted_by = notedBy;
                    this.note_data.comp_ir_number = props_row.ir_number;
                    if (props_row.incident_report) {
                        this.note_data.comp_hr_user_id = props_row.incident_report.hr_user_id;
                    } else {
                        this.note_data.comp_hr_user_id = props_row.hrbp_user.id;
                    }
                }
            },
        }
    }
</script>
