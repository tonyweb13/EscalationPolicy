<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div>
            <the-ibox title="">
                <v-client-table :data="rows" :columns="columns" :options="options">
                    <template v-if="props.row.employee_user" slot="employee_no" slot-scope="props">
                        <span > {{props.row.employee_user.employee_no}} </span>
                    </template>
                    <template v-if="props.row.employee_user" slot="employee" slot-scope="props">
                        <span > {{props.row.employee_user.first_name}} {{props.row.employee_user.last_name}} </span>
                    </template>
                    <template v-if="props.row.attendancepointssystem" slot="offense" slot-scope="props">
                        <span > {{props.row.attendancepointssystem.type_infraction}} </span>
                    </template>
                    <template v-if="props.row.first_respondent && props.row.employee_user"
                        slot="first_offense_occurence" slot-scope="props">
                        <span >
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'First',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.first_respondent)">
                                    {{props.row.first_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                    <template v-if="props.row.second_respondent && props.row.employee_user"
                        slot="second_offense_occurence" slot-scope="props">
                        <span >
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'Second',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.second_respondent)">
                                    {{props.row.second_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                    <template v-if="props.row.third_respondent && props.row.employee_user"
                        slot="third_offense_occurence" slot-scope="props">
                        <span >
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'Third',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.third_respondent)">
                                    {{props.row.third_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                    <template v-if="props.row.fourth_respondent && props.row.employee_user"
                        slot="fourth_offense_occurence" slot-scope="props">
                        <span >
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'Fourth',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.fourth_respondent)">
                                    {{props.row.fourth_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                    <template v-if="props.row.fifth_respondent && props.row.employee_user"
                        slot="fifth_offense_occurence" slot-scope="props">
                        <span v-if="props.row.fifth_respondent">
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'Fifth',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.fifth_respondent)">
                                    {{props.row.fifth_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                    <template v-if="props.row.sixth_respondent && props.row.employee_user"
                        slot="sixth_offense_occurence" slot-scope="props">
                        <span v-if="props.row.sixth_respondent">
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'Sixth',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.sixth_respondent)">
                                    {{props.row.sixth_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                    <template v-if="props.row.seventh_respondent && props.row.employee_user"
                        slot="seventh_offense_occurence" slot-scope="props">
                        <span v-if="props.row.seventh_respondent">
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'Seventh',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.seventh_respondent)">
                                    {{props.row.seventh_respondent.ir_number}}
                            </button>
                        </span>
                    </template>
                </v-client-table>

                <modal v-if="openModal" @close="openModal = false;">
                    <h3 slot="header">{{ headerName }} - {{occurrence}}</h3>
                    <view-offense :complainantProps="complainant_data" slot="body" />
                </modal>
            </the-ibox>
        </div>
    </div>
</template>
<script>
    import viewOffense from '@/modules/admin/progressionoffense/attendancepoints/viewOffense.vue'

    export default {
        components: {
            viewOffense,
        },
        props: {
            categoriesProps: Number,
        },
        data: function () {
            return {
                isLoading: false,
                columns: [
                    'employee_no',
                    'employee',
                    'offense',
                    'first_offense_occurence',
                    'second_offense_occurence',
                    'third_offense_occurence',
                    'fourth_offense_occurence',
                    'fifth_offense_occurence',
                    'sixth_offense_occurence',
                    'seventh_offense_occurence',
                ],
                rows:  [],
                options: {
                    sortable: [
                        'employee_no',
                        'employee',
                    ],
                    filterable: [
                        'employee_user.employee_no',
                        'employee_user.first_name',
                        'employee_user.last_name',
                    ],
                    pagination: { chunk:10 },
                },
                headerName: '',
                openModal: false,
                complainant_data: {
                    complainant_id: '',
                    ir_number: '',
                    reported: '',
                    respondent_response: '',
                    date_incident_start: '',
                    respondent: '',
                    remarks: '',
                    date_admin_hearing: '',
                    is_under_investigation: '',
                    irr_id: '',
                    invite_user_id: '',
                    type_invite: '',
                    ageing: '',
                    complainant: '',
                    offense: '',
                    category: '',
                    gravity: '',
                    prescriptive_period: '',
                    description: '',
                    offense_description: '',
                    attachments: '',
                    witness: '',
                    status: '',
                    incident_report: '',
                    action: '',
                    respondent_user_id: '',
                    offense_id: '',
                    first_offense: '',
                    second_offense: '',
                    third_offense: '',
                    fourth_offense: '',
                    fifth_offense: '',
                    sixth_offense: '',
                    seventh_offense: '',
                    nte_upload: '',
                },
            }
        },

        created(){
            this.$bus.$on('attendancePoints', this.getList);
            if (this.$route.hash == '#attendance-with-points') {
                this.getList();
            }
            if (this.$route.hash == '') {
                this.getList();
            }
        },

        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Progression_API.get("/attendancepoints/attendance")
                .then(response => {
                    this.rows = response.data;
                    console.log('THIS.ROWS', this.rows)
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventView: function (occurrence, first_name, last_name, category_occurrence) {
                this.openModal = true;
                this.headerName = "View IR: #" + category_occurrence.ir_number;
                this.occurrence = occurrence+" Offense";
                //for complanantClosed.vue props
                this.complainant_data.respondent =  first_name + " " + last_name;
                this.complainant_data.respondent_response = category_occurrence.response;
                this.complainant_data.reported = category_occurrence.created_at;
                this.complainant_data.date_incident_start = category_occurrence.complainant.date_incident_start;
                this.complainant_data.complainant = category_occurrence.complainant.complainant_user.first_name
                                               + " " +  category_occurrence.complainant.complainant_user.last_name;
                this.complainant_data.description = category_occurrence.complainant.description;
                this.complainant_data.attachments = category_occurrence.complainant.attachments_id;
                this.complainant_data.remarks = category_occurrence.complainant.remarks;
                this.complainant_data.witness = category_occurrence.complainant.witness_user;

                this.complainant_data.offense = category_occurrence.complainant.attendancepointssystem.type_infraction;
                this.complainant_data.category = category_occurrence.complainant.attendancepointssystem.category.name;
                this.complainant_data.gravity =
                    category_occurrence.complainant.attendancepointssystem.attendancepoint.attendance_points;
                this.complainant_data.offense_description =
                    category_occurrence.complainant.attendancepointssystem.definition;

                //for incidentClosed.vue props
                this.complainant_data.respondent_id =  category_occurrence.id;
                this.complainant_data.complainant_id = category_occurrence.complainant_id;
                this.complainant_data.incident_report = category_occurrence.incident_report;
                this.complainant_data.status = category_occurrence.status_id;
                this.complainant_data.respondent_user_id = category_occurrence.respondent_user_id;
                this.complainant_data.complainant_user_id = category_occurrence.complainant.complainant_user.id;
                this.complainant_data.offense_id = category_occurrence.complainant.offense_id;
                this.complainant_data.ir_number = category_occurrence.ir_number;
            },
        }
    }
</script>
