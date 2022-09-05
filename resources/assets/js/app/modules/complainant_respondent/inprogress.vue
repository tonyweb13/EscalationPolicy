<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div>
            <the-ibox title="">
                <v-client-table :data="rows" :columns="columns" :options="options" >
                    <template slot="actions" slot-scope="props">
                        <div style="width:310px;">
                            <span v-if="(props.row.response == '' || props.row.response == null)
                            && props.row.respondent_user_id == current_user.id">
                                <button class='btn btn-success btn-xs'
                                    @click="viewReplyForm(props.row, 'reply')">
                                    <i class="fa fa-send"></i> {{ labels.replyBtn }}
                                </button>
                            </span>
                            <span v-else>
                                <button v-if="props.row.status_id != 4"
                                    class='btn btn-warning btn-xs'
                                    @click="viewReplyForm(props.row, 'view')">
                                    <i class="fa fa-eye"></i> {{ labels.viewBtn }}
                                </button>

                            </span>
                            <button v-if="props.row.respondent_user_id == current_user.id"
                                class='btn btn-default btn-xs'
                                @click="noteForm(props.row, 'respondent')">
                                <i class="fa fa-paperclip"></i> Note
                            </button>
                            <button v-else
                                class='btn btn-default btn-xs'
                                @click="noteForm(props.row, 'complainant')">
                                <i class="fa fa-paperclip"></i> Note
                            </button>
                            <!-- <button :disabled="!props.row.incident_report
                                || props.row.incident_report.is_generate_nte_invalid_ir != 1"
                                type="button" class="btn btn-primary btn-xs"
                                @click="eventGenerateNte(props.row.complainant.id, props.row.respondent_user.id)">
                                <i class="fa fa-download"></i> NTE
                            </button>
                            <button type="button" class="btn btn-info btn-xs"
                                :disabled="!props.row.incident_report ||
                                props.row.incident_report.is_under_investigation != 1"
                                @click="eventGenerateIrr(props.row.complainant.id, props.row.respondent_user.id)" >
                                <i class="fa fa-download"></i> IRR
                            </button> -->

                            <!-- <span v-if="props.row.incident_report && props.row.incident_report.date_admin_hearing">
                                <span v-if="props.row.respondent_hearing">
                                    <button v-if="props.row.respondent_hearing.going == 'Yes'"
                                        class='btn btn-warning btn-xs'
                                        :disabled="props.row.respondent_hearing.going == 'Yes'">
                                        <i class="fa fa-calendar"></i>
                                            Attending
                                    </button>
                                    <button v-else-if="props.row.respondent_hearing.going == 'Pending'"
                                        class='btn btn-warning btn-xs'
                                        @click="eventAttend(props.row)">
                                        <i class="fa fa-calendar"></i>
                                            Pending
                                    </button>
                                    <button v-else-if="props.row.respondent_hearing.going == 'No'"
                                        class='btn btn-warning btn-xs'
                                        :disabled = "props.row.respondent_hearing.going == 'No'"
                                        @click="eventAttend(props.row)">
                                        <i class="fa fa-calendar"></i>
                                            Attend - No
                                    </button>
                                </span>
                                <button v-else
                                    class='btn btn-warning btn-xs'
                                    @click="eventAttend(props.row)">
                                    <i class="fa fa-calendar"></i>
                                        Attend
                                </button>
                            </span> -->

                        </div>
                    </template>
                    <template slot="reported" slot-scope="props">
                        {{ props.row.created_at | formatDate }}
                    </template>
                    <!-- <template slot="complainant" slot-scope="props">
                        {{ props.row.complainant.complainant_user.first_name }}
                        {{ props.row.complainant.complainant_user.last_name }}
                    </template> -->
                    <template slot="respondent" slot-scope="props">
                        {{ props.row.respondent_user.first_name }}
                        {{ props.row.respondent_user.last_name }}
                    </template>
                    <template slot="offense" slot-scope="props">
                        <span v-if="props.row.complainant.offense">
                            {{ props.row.complainant.offense.offense }}
                        </span>
                        <span v-else>
                            {{ props.row.complainant.attendancepointssystem.type_infraction }}
                        </span>
                    </template>
                    <template slot="date_admin_hearing" slot-scope="props">
                        <span v-if="props.row.incident_report.date_admin_hearing">
                            {{ props.row.incident_report.date_admin_hearing }}
                        </span>
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
                        <span v-if="props.row.complainant.offense && props.row.complainant.offense.gravity_id == '4'
                        && props.row.ageing > '16'">
                            <b style="color:red">{{ props.row.ageing }} working days</b>
                        </span>
                        <span v-else-if="props.row.complainant.offense && props.row.complainant.offense.gravity_id != '4'
                        && props.row.ageing > '12'">
                            <b style="color:red">{{ props.row.ageing }} working days</b>
                        </span>
                        <span v-else>
                        {{ props.row.ageing }} working days
                        </span>
                    </template>
                </v-client-table>

                <modal v-if="openModal" @close="openModal = false">
                    <h3 slot="header">{{ headerName }}</h3>
                    <reply-form v-if="openAction == 'reply'" :respondentProps="respondent_data" slot="body" />
                    <view-form v-if="openAction == 'view'" :respondentViewProps="respondent_data" slot="body" />
                    <note v-if="openAction == 'note'" :noteProps="note_data" slot="body" />
                    <!-- <not-attend v-if="openAction == 'notAttend'" :hearingProps="hearing_data" slot="body" /> -->
                </modal>

            </the-ibox>
        </div>
    </div>
</template>

<script>
    import viewForm from '@/modules/complainant_respondent/viewForm.vue'
    import replyForm from '@/modules/complainant_respondent/replyForm.vue'
    import note from '@/modules/complainant_respondent/note.vue'
    // import notAttend from '@/modules/complainant_respondent/notAttendForm.vue'

    export default {
        components: {
            viewForm,
            replyForm,
            note,
            // notAttend,
        },
        props:
            {
                respondentProps: Array,
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Create Incident Report',
                            reply: 'Incident Report Form',
                            view: 'View Incident Report',
                            // notAttend: 'Suggested Date Form',
                            note: 'Notes',
                            viewBtn: 'View',
                            replyBtn: 'Reply',
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
                    'reported',
                    // 'complainant',
                    'respondent',
                    'hrbp',
                    'offense',
                    'incident_report.date_admin_hearing',
                    'incident_report.time_admin_hearing',
                    'incident_report.meeting_place',

                    'ageing'
                ],
                rows:  [],
                options: {
                    headings: {
                        'ir_number': 'IR Number',
                        'complainant.date_incident_start': 'Date Incident',
                        'complainant.offense': 'Offense',
                        'incident_report.date_admin_hearing': 'Date Hearing',
                        'incident_report.time_admin_hearing': 'Time Hearing',
                        'incident_report.meeting_place': 'Meeting Place',
                    },
                    sortable: [
                        'ir_number',
                        'reported',
                        'respondent',
                        'complainant.date_incident_start',
                        'offense',
                        'incident_report.date_admin_hearing',
                        'incident_report.time_admin_hearing',
                        'incident_report.meeting_place',
                    ],
                    filterable: [
                        'created_at',
                        'respondent_user.first_name',
                        'respondent_user.last_name',
                        'ir_number',
                        'complainant.date_incident_start',
                        'complainant.offense.offense',
                        'incident_report.date_admin_hearing',
                    ],
                },
                headerName: '',
                openModal: false,
                respondent_data: {
                    res_ir_id: '',
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
                    res_offense_description: '',
                    res_attachments: '',
                    res_witness: '',
                    res_invite: '',
                    res_notes: '',
                    res_respondent_response: '',
                    res_respondent_hrbp_acknowledge_response: '',
                    res_incident_report: '',
                    res_remarks: '',
                    res_status: this.$constants.Status,
                    res_first_offense: '',
                    res_second_offense: '',
                    res_third_offense: '',
                    res_fourth_offense: '',
                    res_fifth_offense: '',
                    res_sixth_offense: '',
                    res_seventh_offense: '',
                    // res_supervisor_approval: '',
                },
                note_data: {
                   complainant_id: '',
                   comp_complainant_user_id: '',
                   comp_respondent_user_id: '',
                   comp_noted_by: '',
                   comp_ir_number: '',
                },
                openAction: '',
                hearing_data: {
                    going: '',
                    complainant_id: '',
                    respondent_id: '',
                    ir_number: '',
                    scheduled_admin_date: '',
                    scheduled_admin_time: '',
                    scheduled_admin_place: '',
                    requestor: '',
                    requestor_user_id: '',
                    reason: '',
                    suggested_date: '',
                    suggested_time: '',
                    suggested_place: '',
                    hr_approval: '',
                },
                current_user: this.$ep.user,
            }
        },

        mounted() {
            this.$emit("update", this.updatePagination);
        },

        created(){
            this.getList();

            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },

        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Default_API.get("/employee/inprogress")
                .then(response => {
                    this.rows = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            viewReplyForm: function (props_row, action) {
                this.openModal = true;

                if (props_row) {
                    if (action == "view") {
                        this.openAction = "view";
                        this.headerName = this.labels.view;
                        this.respondent_data.res_incident_report = props_row.incident_report;
                        this.respondent_data.res_respondent_response = props_row.response;
                        this.respondent_data.res_respondent_hrbp_acknowledge_response = props_row.hrbp_acknowledge_response;
                        this.respondent_data.res_status = props_row.status_id;

                    // if(props_row.supervisor_approval) {
                    //     console.log("viewReplyForm supervisor_approval2==");
                    //     console.log(props_row.supervisor_approval.approver_status);
                    //     this.respondent_data.res_supervisor_approval = props_row.supervisor_approval.approver_status;
                    // }

                    } else if (action == "reply") {
                        this.openAction = "reply";
                        this.headerName = this.labels.reply;
                        this.respondent_data.res_respondent_response = props_row.response;
                    }

                    this.respondent_data.res_id = props_row.id;
                    this.respondent_data.res_ir_id = props_row.ir_id;
                    this.respondent_data.respondent_user_id = props_row.respondent_user_id;
                    this.respondent_data.res_reported = props_row.created_at;
                    this.respondent_data.res_ir_number = props_row.ir_number;

                    if (props_row.complainant.offense) {
                        this.respondent_data.res_category = props_row.complainant.offense.category.name;
                        this.respondent_data.res_offense = props_row.complainant.offense.offense;
                        this.respondent_data.res_gravity = props_row.complainant.offense.gravity.gravity;
                        this.respondent_data.res_offense_description = props_row.complainant.offense.description;
                        this.respondent_data.res_prescriptive_period = props_row.complainant.offense.gravity.prescriptive_period;

                    } else if (props_row.complainant.attendancepointssystem) {
                        this.respondent_data.res_category = "Attendance";
                        this.respondent_data.res_offense = props_row.complainant.attendancepointssystem.type_infraction;
                        this.respondent_data.res_gravity
                        = "Attendance Points - " +
                        props_row.complainant.attendancepointssystem.attendancepoint.attendance_points;
                        this.respondent_data.res_offense_description
                        = props_row.complainant.attendancepointssystem.definition;
                        this.respondent_data.res_prescriptive_period = '';
                    }

                    this.respondent_data.res_date_incident_start = props_row.complainant.date_incident_start;
                    this.respondent_data.res_date_incident_end = props_row.complainant.date_incident_end;
                    this.respondent_data.res_description = props_row.complainant.description
                    this.respondent_data.res_remarks = props_row.complainant.remarks
                    this.respondent_data.res_attachments = props_row.complainant.attachments

                    if(props_row.complainant.complainant_user){
                        this.respondent_data.res_complainant = props_row.complainant.complainant_user.first_name
                                                            +" "+props_row.complainant.complainant_user.last_name;
                    }

                    if (props_row.progression_offense) {
                        if (props_row.progression_offense.respondent_first_offense) {
                            this.respondent_data.res_first_offense = props_row.progression_offense.respondent_first_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_second_offense) {
                            this.respondent_data.res_second_offense = props_row.progression_offense.respondent_second_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_third_offense) {
                            this.respondent_data.res_third_offense = props_row.progression_offense.respondent_third_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_fourth_offense) {
                            this.respondent_data.res_fourth_offense = props_row.progression_offense.respondent_fourth_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_fifth_offense) {
                            this.respondent_data.res_fifth_offense = props_row.progression_offense.respondent_fifth_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_sixth_offense) {
                            this.respondent_data.res_sixth_offense = props_row.progression_offense.respondent_sixth_offense.complainant.offense.offense;

                        } else if (props_row.progression_offense.respondent_seventh_offense) {
                            this.respondent_data.res_seventh_offense = props_row.progression_offense.respondent_seventh_offense.complainant.offense.offense;
                        }
                    }

                    if(props_row.notes){
                       this.respondent_data.res_notes = props_row.complainant.notes.notes;
                    }

                    if(props_row.complainant.witness_user){
                        this.respondent_data.res_witness = props_row.complainant.witness_user;
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
            // eventGenerateNte: function(complainant_id, respondent_user) {
            //     this.$swal({
            //         title: 'Are you sure you want to download NTE?',
            //         text: 'You are require to sign the document.',
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: 'Yes',
            //         cancelButtonText: 'No, cancel!',
            //         confirmButtonClass: 'btn btn-success',
            //         cancelButtonClass: 'btn btn-danger',
            //     })
            //         .then((result) => {
            //             if (result.value) {
            //                 window.location.href = "/api/admin/incidentreport/generate/nte/"+complainant_id+"/"+respondent_user;
            //                 this.$success('Generate NTE downloaded successfully!');
            //             } else if (result.dismiss === this.$swal.DismissReason.cancel) {
            //                 this.$failure('Generate NTE has been cancelled');
            //             }
            //         })
            //         .catch(error => {
            //             this.globalErrorHandling(error);
            //         });
            // },
            // eventGenerateIrr: function(complainant_id, respondent_user) {
            //     this.$swal({
            //         title: 'Are you sure you want to download IRR?',
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: 'Yes',
            //         cancelButtonText: 'No, cancel!',
            //         confirmButtonClass: 'btn btn-success',
            //         cancelButtonClass: 'btn btn-danger',
            //     })
            //         .then((result) => {
            //             if (result.value) {
            //                 window.location.href = "/api/admin/incidentreport/generate/irr/"
            //                 + complainant_id  + "/" + respondent_user;
            //                 this.$success('Generate Case Closure downloaded successfully!');
            //             } else if (result.dismiss === this.$swal.DismissReason.cancel) {
            //                 this.$failure('Generate Case Closure has been cancelled');
            //             }
            //         })
            //         .catch(error => {
            //             this.globalErrorHandling(error);
            //         });
            // },
            // eventAttend: function (props_row) {

            //     let date_options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            //     let date_admin  = new Date(props_row.incident_report.date_admin_hearing);

            //     let titleIntro = '';
            //     if (props_row.respondent_hearing && props_row.respondent_hearing.going == "Pending") {

            //         if (props_row.respondent_hearing.hr_approval == 'Approve') {
            //             titleIntro = 'HR Approve your suggest, Are you going?';
            //         } else {
            //             titleIntro = 'HR Disapprove your suggest, Are you going?';
            //         }

            //     } else {
            //         titleIntro = 'Are you going in Admin Hearing?';
            //     }

            //     this.$swal({
            //         title: titleIntro,
            //         html: '<small><i>For follow ups and additional notes, please leave a note ' +
            //         'to your HR Business Partner using the Note option </i></small><br><br>' +
            //         '<b>Dated:</b> ' + date_admin.toLocaleDateString("en-US", date_options)
            //         + "<br>" +
            //         '<b>Time:</b> ' + props_row.incident_report.time_admin_hearing
            //         + "<br>" +
            //         '<b>Meeting Place:</b> ' + props_row.incident_report.meeting_place,
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: 'Yes',
            //         cancelButtonText: 'No',
            //         confirmButtonClass: 'btn btn-success',
            //         cancelButtonClass: 'btn btn-danger',
            //     })
            //         .then((result) => {
            //             if (result.value) {

            //                 if (props_row.respondent_hearing && props_row.respondent_hearing.going == "Pending") {

            //                     this.$constants.Default_API.get("/hearing/hr/approval/" +
            //                     props_row.respondent_hearing.id, {
            //                             params: {
            //                                 going: "Yes",
            //                                 hr_approval: props_row.respondent_hearing.hr_approval,
            //                                 ir_id: props_row.respondent_hearing.ir_id,
            //                                 requestor_user_id: props_row.respondent_hearing.requestor_user_id,
            //                                 reason: props_row.respondent_hearing.reason,
            //                                 suggested_date: props_row.respondent_hearing.suggested_date,
            //                                 suggested_time: props_row.respondent_hearing.suggested_time,
            //                                 suggested_place: props_row.respondent_hearing.suggested_place,
            //                             }
            //                         })
            //                         .then(response => {
            //                             this.$bus.$emit('init_modal', false);
            //                             this.$bus.$emit('updateList');
            //                             return response.data;
            //                         })
            //                         .catch(error => {
            //                             this.globalErrorHandling(error);
            //                         });

            //                 } else {

            //                     this.$constants.Default_API.get("/hearing/create", {
            //                         params: {
            //                             going: "Yes",
            //                             complainant_id: props_row.complainant.id,
            //                             respondent_id: props_row.id,
            //                             ir_id: props_row.incident_report.id,
            //                             scheduled_admin_date: props_row.incident_report.date_admin_hearing,
            //                             scheduled_admin_time: props_row.incident_report.time_admin_hearing,
            //                             scheduled_admin_place: props_row.incident_report.meeting_place,
            //                             requestor: "Respondent",
            //                             requestor_user_id: props_row.respondent_user.id,
            //                             hr_approval: "Confirmed"
            //                         }
            //                     }).then(response => {
            //                         this.hearing = response.data;
            //                         this.$success('Success: See you in Admin Hearing!');
            //                     })
            //                     .catch(error => {
            //                         this.globalErrorHandling(error);
            //                     });
            //                 }
            //                 this.getList();

            //             } else if (result.dismiss === this.$swal.DismissReason.cancel) {
            //                 this.openModal = true;
            //                 this.openAction = "notAttend";
            //                 this.headerName = this.labels.notAttend;
            //                 this.hearing_data.ir_id = props_row.incident_report.id;
            //                 this.hearing_data.requestor = "Respondent";
            //                 this.hearing_data.requestor_user_id = props_row.respondent_user.id;
            //                 this.hearing_data.respondent_id = props_row.respondent_hearing.id;
            //             }
            //         })
            //         .catch(error => {
            //             this.globalErrorHandling(error);
            //         });
            // }
        }
    }
</script>
