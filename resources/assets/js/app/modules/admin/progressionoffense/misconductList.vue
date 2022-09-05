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
                    <template v-if="props.row.offense" slot="offense" slot-scope="props" >
                        <span style="width:250px;"> {{props.row.offense.offense}} </span>
                    </template>
                    <template v-if="props.row.first_respondent && props.row.employee_user"
                        slot="first_offense_occurence" slot-scope="props">
                        <span >
                            <button class='btn btn-link btn-xs'
                            @click="eventView(
                                'First',
                                props.row.employee_user.first_name,
                                props.row.employee_user.last_name,
                                props.row.first_respondent,
                                props.row.offense
                                )">
                                    {{props.row.first_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.first_respondent.incident_report
                            && props.row.first_respondent.incident_report.irr">
                                {{props.row.first_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.first_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.first_occurrence}} )
                            </p>
                            <p v-else-if="props.row.first_occurrence != null" style="font-size:10px;">
                                ( {{props.row.first_occurrence}} )
                            </p>
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
                                props.row.second_respondent,
                                props.row.offense
                                )">
                                    {{props.row.second_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.second_respondent.incident_report
                            && props.row.second_respondent.incident_report.irr">
                                {{props.row.second_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.second_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.second_occurrence}} )
                            </p>
                            <p v-else-if="props.row.first_occurrence == 'cleansed' &&
                            props.row.second_occurrence != null" style="font-size:10px;">
                                ( {{props.row.second_occurrence}} )
                            </p>
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
                                props.row.third_respondent,
                                props.row.offense)">
                                    {{props.row.third_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.third_respondent.incident_report
                            && props.row.third_respondent.incident_report.irr">
                                {{props.row.third_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.third_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.third_occurrence}} )
                            </p>
                            <p v-else-if="props.row.second_occurrence == 'cleansed' &&
                            props.row.third_occurrence != null" style="font-size:10px;">
                                ( {{props.row.third_occurrence}} )
                            </p>
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
                                props.row.fourth_respondent,
                                props.row.offense)">
                                    {{props.row.fourth_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.fourth_respondent.incident_report
                            && props.row.fourth_respondent.incident_report.irr">
                                {{props.row.fourth_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.fourth_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.fourth_occurrence}} )
                            </p>
                            <p v-else-if="props.row.third_occurrence == 'cleansed' &&
                            props.row.fourth_occurrence != null" style="font-size:10px;">
                                ( {{props.row.fourth_occurrence}} )
                            </p>
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
                                props.row.fifth_respondent,
                                props.row.offense)">
                                    {{props.row.fifth_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.fifth_respondent.incident_report
                            && props.row.fifth_respondent.incident_report.irr">
                                {{props.row.fifth_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.fifth_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.fifth_occurrence}} )
                            </p>
                            <p v-else-if="props.row.fourth_occurrence == 'cleansed' &&
                            props.row.fifth_occurrence != null" style="font-size:10px;">
                                ( {{props.row.fifth_occurrence}} )
                            </p>
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
                                props.row.sixth_respondent,
                                props.row.offense)">
                                    {{props.row.sixth_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.sixth_respondent.incident_report
                            && props.row.sixth_respondent.incident_report.irr">
                                {{props.row.sixth_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.sixth_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.sixth_occurrence}} )
                            </p>
                            <p v-else-if="props.row.fifth_occurrence == 'cleansed' &&
                            props.row.sixth_occurrence != null" style="font-size:10px;">
                                ( {{props.row.sixth_occurrence}} )
                            </p>
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
                                props.row.seventh_respondent,
                                props.row.offense)">
                                    {{props.row.seventh_respondent.ir_number}}
                            </button>
                            <p style="font-size:10px;" v-if="props.row.seventh_respondent.incident_report
                            && props.row.seventh_respondent.incident_report.irr">
                                {{props.row.seventh_respondent.incident_report.irr.name}}
                            </p>
                            <p v-if="props.row.seventh_occurrence == 'cleansed'"
                            style="font-size:10px; color:red;">
                                ( {{props.row.seventh_occurrence}} )
                            </p>
                            <p v-else-if="props.row.sixth_occurrence == 'cleansed' &&
                            props.row.seventh_occurrence != null" style="font-size:10px;">
                                ( {{props.row.seventh_occurrence}} )
                            </p>
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
    import viewOffense from '@/modules/admin/progressionoffense/viewOffense.vue'

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
                        'offense',
                        'first_respondent.ir_number',
                        'second_respondent.ir_number',
                        'third_respondent.ir_number',
                        'fourth_respondent.ir_number',
                        'fifth_respondent.ir_number',
                        'sixth_respondent.ir_number',
                        'seventh_respondent.ir_number',
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
                    offense_archived: '',
                    irr_archived: '',
                },
            }
        },

        created(){
            this.$bus.$on('misconduct', this.getList);
            if (this.$route.hash == '#misconduct') {
                this.getList();
            }
        },

        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Progression_API.get("/"+this.categoriesProps)
                .then(response => {
                    this.rows = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventView: function (occurrence, first_name, last_name, category_occurrence, prop_rows_offense) {
                this.openModal = true;
                this.headerName = "View IR: #" + category_occurrence.ir_number;
                this.occurrence = occurrence+" Offense";
                //for complanantClosed.vue props
                this.complainant_data.respondent =  first_name + " " + last_name;
                this.complainant_data.respondent_response = category_occurrence.response;
                this.complainant_data.reported = category_occurrence.created_at;
                this.complainant_data.date_incident_start = category_occurrence.complainant.date_incident_start;
                if (category_occurrence.complainant.complainant_user) {
                this.complainant_data.complainant = category_occurrence.complainant.complainant_user.first_name
                                               + " " +  category_occurrence.complainant.complainant_user.last_name;
                }
                this.complainant_data.description = category_occurrence.complainant.description;
                this.complainant_data.attachments = category_occurrence.complainant.attachments_id;
                this.complainant_data.remarks = category_occurrence.complainant.remarks;
                this.complainant_data.witness = category_occurrence.complainant.witness_user;
                this.complainant_data.respondent_id =  category_occurrence.id;
                this.complainant_data.complainant_id = category_occurrence.complainant_id;
                this.complainant_data.incident_report = category_occurrence.incident_report;
                this.complainant_data.status = category_occurrence.status_id;
                this.complainant_data.respondent_user_id = category_occurrence.respondent_user_id;
                if (category_occurrence.complainant.complainant_user) {
                this.complainant_data.complainant_user_id = category_occurrence.complainant.complainant_user.id;
                }
                this.complainant_data.irr_archived = category_occurrence.irr_archived;
                this.complainant_data.offense_archived = prop_rows_offense.offense_archived;

                this.complainant_data.ir_number = category_occurrence.ir_number;

                if (category_occurrence.complainant.offense) {
                    this.complainant_data.offense = category_occurrence.complainant.offense.offense;
                    this.complainant_data.category = category_occurrence.complainant.offense.category.name;
                    this.complainant_data.gravity = category_occurrence.complainant.offense.gravity.gravity;
                    this.complainant_data.offense_description = category_occurrence.complainant.offense.description;
                    this.complainant_data.offense_id = category_occurrence.complainant.offense_id;
                    this.complainant_data.prescriptive_period =
                    category_occurrence.complainant.offense.gravity.prescriptive_period;
                } else {
                    this.complainant_data.offense = prop_rows_offense.offense;
                    this.complainant_data.category = prop_rows_offense.category.name;
                    this.complainant_data.gravity = prop_rows_offense.gravity.gravity;
                    this.complainant_data.offense_description = prop_rows_offense.description;
                    this.complainant_data.prescriptive_period = prop_rows_offense.gravity.prescriptive_period;
                }
                console.log(prop_rows_offense);
            },
        }
    }
</script>
