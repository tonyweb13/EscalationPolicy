<template>
    <div>
        <loading-modal v-if="isLoading" />
            <v-client-table :data="rows" :columns="columns" :options="options" >
                <template slot="actions" slot-scope="props">
                    <button class='btn btn-default btn-xs'
                        @click="noteForm(props.row, 'invite')">
                        <i class="fa fa-paperclip"></i> Note
                    </button>
                    <button class="btn btn-warning btn-xs"
                    @click="viewReplyForm(props.row, 'view')">
                        <i class="fa fa-eye"></i> View
                    </button>

                    <button v-if="props.row.incident_report.invite_ir
                        && props.row.incident_report.invite_ir.going == 'Yes'"
                        class='btn btn-danger btn-xs'
                        :disabled="props.row.incident_report.invite_ir.going == 'Yes'">
                        <i class="fa fa-calendar"></i>
                            Confirmed
                    </button>
                    <button v-else-if="props.row.incident_report.invite_ir
                        && props.row.incident_report.invite_ir.going == 'Pending'"
                        class='btn btn-danger btn-xs'
                        @click="eventAttend(props.row)">
                        <i class="fa fa-calendar"></i>
                            Pending
                    </button>
                    <button v-else-if="props.row.incident_report.invite_ir
                        && props.row.incident_report.invite_ir.going == 'No'"
                        class='btn btn-danger btn-xs'
                        :disabled = "props.row.incident_report.invite_ir.going == 'No'"
                        @click="eventAttend(props.row)">
                        <i class="fa fa-calendar"></i>
                            Attend - No
                    </button>
                    <button v-else
                        class='btn btn-danger btn-xs'
                        @click="eventAttend(props.row)">
                        <i class="fa fa-calendar"></i>
                            Attend
                    </button>
                </template>
                <template slot="hrApproval" slot-scope="props">
                    <div v-if="props.row.incident_report.invite_ir">
                        {{ props.row.incident_report.invite_ir.hr_approval }}
                    </div>
                </template>
            </v-client-table>
            <modal v-if="openModal" @close="openModal = false">
                <h3 slot="header">{{ headerName }}</h3>
                <not-attend v-if="openAction == 'notAttend'" :hearingProps="hearing_data" slot="body" />
                <note v-if="openAction == 'note'" :noteProps="note_data" slot="body" />
                <view-form v-if="openAction == 'view'" :respondentViewProps="respondent_data" slot="body" />
            </modal>
    </div>
</template>
<script>
    import notAttend from '@/modules/complainant_respondent/notAttendForm.vue'
    import note from '@/modules/complainant_respondent/note.vue'
    import viewForm from '@/modules/complainant_respondent/viewForm.vue'

    export default {
        components: {
            notAttend,
            note,
            viewForm,
        },
        props:
            {
                respondentProps: Number,
                labels: {
                    type: Object,
                    default() {
                        return {
                            notAttend: 'Admin Hearing Form',
                            view: 'View Incident Report',
                        }
                    }
                },
            },

        data: function () {
            return {
                isLoading: false,
                columns: [
                    'actions',
                    'ir_number',
                    'incident_report.date_admin_hearing',
                    'incident_report.time_admin_hearing',
                    'incident_report.meeting_place',
                    'hrApproval'
                  ],
                rows:  [],
                options: {
                    headings: {
                        'ir_number': 'IR Number',
                        'incident_report.date_admin_hearing': 'Date Admin Hearing',
                        'incident_report.time_admin_hearing': 'Time Admin Hearing',
                        'incident_report.meeting_place': 'Meeting Place',
                        'hrApproval': 'HR Approval'
                    },
                    sortable: [],
                    filterable: [
                        'incident_report.date_admin_hearing',
                        'incident_report.time_admin_hearing',
                        'incident_report.meeting_place',
                    ],
                },
                headerName: '',
                openModal: false,
                openAction: '',
                hearing_data: {
                    ir_number: '',
                    going: '',
                    ir_id: '',
                    requestor: '',
                    // requestor_user_id: '',
                    reason: '',
                    suggested_date: '',
                    suggested_time: '',
                    suggested_place: '',
                    hr_approval: '',
                },
                note_data: {
                   comp_complainant_id: '',
                   comp_complainant_user_id: '',
                   comp_respondent_user_id: '',
                   comp_hr_user_id:'',
                   comp_noted_by: '',
                   comp_ir_number: '',
                   comp_invite_user_id: '',
                   comp_invite_user: '',
                },
                respondent_data: {
                    res_reported: '',
                    res_ir_number: '',
                    res_complainant: '',
                    res_offense: '',
                    res_category: '',
                    res_gravity: '',
                    res_prescriptive_period: '',
                    res_description: '',
                    res_date_incident_start: '',
                    res_date_incident_end: '',
                    res_attachments: '',
                    res_witness: '',
                    res_invite: '',
                    res_notes: '',
                    res_incident_report: '',
                    res_status: this.$constants.Status,
                },
            }
        },

        mounted() {
            this.$emit("update", this.updatePagination);
        },

        created(){
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('inviteList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },

        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Default_API.get("/employee/invite")
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            eventAttend: function (props_row) {

                let date_options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                let date_admin  = new Date(props_row.incident_report.date_admin_hearing);

                let titleIntro = '';
                if (props_row.incident_report.invite_ir && props_row.incident_report.invite_ir.going == "Pending") {

                    if (props_row.incident_report.invite_ir.hr_approval == 'Approve') {
                        titleIntro = 'HR Approve your suggest, Are you going?';
                    } else {
                         titleIntro = 'HR Disapprove your suggest, Are you going?';
                    }

                } else {
                    titleIntro = 'Are you going in Admin Hearing?';
                }

                this.$swal({
                    title: titleIntro,
                    html: '<small><i>For follow ups and additional notes, please leave a note ' +
                    'to your HR Business Partner using the Note option </i></small><br><br>' +
                    '<b>Dated:</b> ' + date_admin.toLocaleDateString("en-US", date_options)
                    + "<br>" +
                    '<b>Time:</b> ' + props_row.incident_report.time_admin_hearing
                    + "<br>" +
                    '<b>Meeting Place:</b> ' + props_row.incident_report.meeting_place,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                .then((result) => {
                    if (result.value) {

                        if (props_row.incident_report.invite_ir &&
                        props_row.incident_report.invite_ir.going == "Pending") {

                            this.$constants.Default_API.get("/hearing/hr/approval/" +
                            props_row.incident_report.invite_ir.id, {
                                params: {
                                    going: "Yes",
                                    hr_approval: props_row.incident_report.invite_ir.hr_approval,
                                    ir_id: props_row.incident_report.invite_ir.ir_id,
                                    requestor_user_id: props_row.incident_report.invite_ir.requestor_user_id,
                                    reason: props_row.incident_report.invite_ir.reason,
                                    suggested_date: props_row.incident_report.invite_ir.suggested_date,
                                    suggested_time: props_row.incident_report.invite_ir.suggested_time,
                                    suggested_place: props_row.incident_report.invite_ir.suggested_place,
                                }
                            })
                            .then(response => {
                                this.$bus.$emit('init_modal', false);
                                this.$bus.$emit('updateList');
                                this.$bus.$emit('updateDashboard');
                                return response.data;
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });

                        } else {

                            this.$constants.Default_API.get("/hearing/create", {
                                params: {
                                    ir_id: props_row.incident_report.id,
                                    going: "Yes",
                                    requestor: "Invite",
                                    requestor_user_id: this.$ep.user.id,
                                    suggested_date: props_row.incident_report.date_admin_hearing,
                                    suggested_time: props_row.incident_report.time_admin_hearing,
                                    suggested_place: props_row.incident_report.meeting_place,
                                    hr_approval: "Confirmed"
                                }
                            }).then(response => {
                                this.hearing = response.data;
                                this.$success('Success: See you in Admin Hearing!');
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        }

                        this.getList();

                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {

                        this.openModal = true;
                        this.openAction = "notAttend";
                        this.headerName = this.labels.notAttend;
                        this.hearing_data.ir_id = props_row.incident_report.id;
                        this.hearing_data.requestor = "Invite";
                        this.hearing_data.requestor_user_id = this.$ep.user.id;
                        if (props_row.incident_report.invite_ir) {
                            this.hearing_data.respondent_id = props_row.incident_report.invite_ir.id;
                        }

                    }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            noteForm: function (props_row, notedBy) {
                this.openModal = true;
                if(props_row){
                    console.log("props_row==");
                    console.log(props_row);

                    this.openAction = "note";
                    this.headerName = this.labels.note;
                    this.note_data.comp_complainant_id = props_row.complainant_id;
                    this.note_data.comp_complainant_user_id = props_row.complainant.complainant_user_id;
                    this.note_data.comp_respondent_user_id = props_row.respondent_user_id;
                    this.note_data.comp_noted_by = notedBy;
                    this.note_data.comp_ir_number = props_row.ir_number;
                    // this.note_data.comp_invite_user_id = props_row.incident_report.invite_ir.requestor_user_id;
                    this.note_data.comp_invite_user_id = this.$ep.user.id;
                    this.note_data.comp_hr_user_id = props_row.incident_report.hr_user_id;
                }
            },
            viewReplyForm: function (props_row, action) {
                this.openModal = true;

                if (props_row) {
                    this.openAction = "view";
                    this.headerName = this.labels.view;
                    this.respondent_data.res_incident_report = props_row.incident_report;
                    this.respondent_data.res_status = props_row.status_id;

                    this.respondent_data.res_id = props_row.id;
                    this.respondent_data.res_reported = props_row.created_at;
                    this.respondent_data.res_ir_number = props_row.ir_number;
                    this.respondent_data.res_offense = props_row.complainant.offense.offense;
                    this.respondent_data.res_category = props_row.complainant.offense.category.name;
                    this.respondent_data.res_gravity = props_row.complainant.offense.gravity.gravity;
                    this.respondent_data.res_offense_description = props_row.complainant.offense.description;
                    this.respondent_data.res_prescriptive_period = props_row.complainant.offense.gravity.prescriptive_period;
                    this.respondent_data.res_date_incident_start = props_row.complainant.date_incident_start;
                    this.respondent_data.res_date_incident_end = props_row.complainant.date_incident_end;

                    if(props_row.complainant.complainant_user){
                        this.respondent_data.res_complainant = props_row.complainant.complainant_user.first_name
                                                            +" "+props_row.complainant.complainant_user.last_name;
                    }

                    if(props_row.witness_user){
                        this.respondent_data.res_witness = props_row.complainant.witness_user.first_name
                                                            +" "+props_row.complainant.witness_user.last_name;
                    }
                }
            },
        }
    }
</script>
