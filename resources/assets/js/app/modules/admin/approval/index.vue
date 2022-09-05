<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div>
            <the-ibox title="">
                <v-client-table :data="rows" :columns="columns" :options="options">

                    <template slot="actions" slot-scope="props">
                        <button v-if="props.row.status_id != 1 || props.row.status_id != 4"
                            class='btn btn-warning btn-xs'
                            @click="viewReplyForm(props.row, 'view')">
                            <i class="fa fa-eye"></i> {{ labels.viewBtn }}
                        </button>
                    </template>
                    <template slot="reported" slot-scope="props">
                        {{ props.row.created_at | formatDate }}
                    </template>
                    <template slot="complainant" slot-scope="props">
                        {{ props.row.complainant.complainant_user.first_name }}
                        {{ props.row.complainant.complainant_user.last_name }}
                    </template>
                    <template slot="respondent" slot-scope="props">
                        {{ props.row.respondent_user.first_name }}
                        {{ props.row.respondent_user.last_name }}
                    </template>
                    <template slot="offense" slot-scope="props">
                        {{ props.row.complainant.offense.offense }}
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
                        <span v-if="props.row.complainant.offense.gravity_id == '4'
                        && props.row.ageing > '16'">
                            <b style="color:red">{{ props.row.ageing }} working days</b>
                        </span>
                        <span v-else-if="props.row.complainant.offense.gravity_id != '4'
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
                    <view-form v-if="openAction == 'view'" :respondentViewProps="respondent_data" slot="body" />
                </modal>

            </the-ibox>
        </div>
    </div>
</template>

<script>
    import viewForm from '@/modules/admin/approval/viewForm.vue'

    export default {
        components: {
            viewForm,
        },
        props:
            {
                respondentProps: Array,
                labels: {
                    type: Object,
                    default() {
                        return {
                            view: 'Incident Report',
                            viewBtn: 'View',
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
                    'complainant',
                    'respondent',
                    'hrbp',
                    'offense',
                    'incident_report.date_admin_hearing',
                    'incident_report.time_admin_hearing',
                    'incident_report.meeting_place',
                    'status_id',
                    'ageing',
                    'approver_status',
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
                        'status_id': 'Status',
                        'approver_status': 'Approver Status',
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
                    res_incident_report: '',
                    res_status: this.$constants.Status,
                    res_first_offense: '',
                    res_second_offense: '',
                    res_third_offense: '',
                    res_fourth_offense: '',
                    res_fifth_offense: '',
                    res_sixth_offense: '',
                    res_seventh_offense: '',
                    res_respondent_user: '',
                },
                openAction: ''
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
                this.$constants.Admin_API.get("/approval/list/nte")
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
                        this.headerName = props_row.respondent_user.first_name + " " +
                        props_row.respondent_user.last_name + " - " + this.labels.view;
                        this.respondent_data.res_incident_report = props_row.incident_report;
                        this.respondent_data.res_respondent_response = props_row.response;
                        this.respondent_data.res_status = props_row.status_id;
                    }

                    this.respondent_data.ir_id = props_row.ir_id;
                    this.respondent_data.respondent_user_id = props_row.respondent_user_id;
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
                    this.respondent_data.res_description = props_row.complainant.description

                    if(props_row.complainant.complainant_user){
                        this.respondent_data.res_complainant = props_row.complainant.complainant_user.first_name
                                                            +" "+props_row.complainant.complainant_user.last_name;
                    }

                    if(props_row.respondent_user){
                        this.respondent_data.res_respondent_user = props_row.respondent_user.first_name
                                                            +" "+props_row.respondent_user.last_name;
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

                    if(props_row.witness_user){
                        this.respondent_data.res_witness = props_row.complainant.witness_user.first_name
                                                            +" "+props_row.complainant.witness_user.last_name;
                    }
                }
            }
        }
    }
</script>

