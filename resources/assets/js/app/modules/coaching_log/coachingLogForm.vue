<template>
    <div class="row">
        <loading-modal v-if="isLoading" />
        <div class="ibox-content col-lg-12 p-md">
            <div v-if="role != 'Regular User Access'">
                <the-label label="Employee Name" :asterisk="true"  class="col-lg-5 m-t-sm m-b-sm">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="user.employee_name"
                            :options="selectedRespondent"
                            label="text"
                            input-id="coaching_log_user"
                            @input="userChecker(user.employee_name)"
                        />
                        <span class="style-red" id="employeee">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>
                <button
                    type="button"
                    @click.prevent="createCoachingLogForm(user.employee_name)"
                    class="btn btn-info col-lg-2 m-t-lg"
                >
                <i class="fa fa-print"/> Create Coaching Log
                </button>
                <button
                    type="button"
                    @click.prevent="createWeeklyForm(user.employee_name)"
                    style="margin-left:10px"
                    class="btn btn-info col-lg-2 m-t-lg"
                >
                <i class="fa fa-print"/> Create Weekly Performance
                </button>
                <button
                    type="button"
                    @click.prevent="createMonthlyForm(user.employee_name)"
                    style="margin-left:10px"
                    class="btn btn-info col-lg-2 m-t-lg"
                >
                <i class="fa fa-print"/> Create Monthly Performance
                </button>
            </div>

            <the-ibox title="">
                <input-search-clear v-if="role != 'Regular User Access'" @search="eventSearch(searchKey)"
                :disabled="searchClear" @clear="eventSearchClear(searchKey)" >
                    <input type="text"
                            v-if="role != 'Regular User Access'"
                            class="form-control search-custom-input"
                            placeholder="Search Coaching No, Employee No, First Name, Last Name here..."
                            v-model="searchKey"
                            @change='eventSearch(searchKey)'
                        >
                </input-search-clear>

                <v-client-table :data="rows" :columns="columns" :options="options" >
                    <template slot="actions" slot-scope="props">
                        <!-- <button class='btn btn-default btn-xs'>
                            <i class="fa fa-paperclip"></i> Edit
                        </button> -->
                        <span v-if="props.row.created_by_employee_id &&
                        props.row.employee_no == user_employee.employee_no">
                            <button  class='btn btn-primary btn-xs'
                                v-if="!props.row.acknowledge_date"
                                @click="acknowledge(props.row.id)">
                                <i class="fa fa-pencil"></i>
                                Acknowledge
                            </button>
                        </span>
                        <span v-if="role != 'Regular User Access'">
                            <button
                                class='btn btn-primary btn-xs'
                                v-if="props.row.acknowledge_date"
                                :disabled="true">
                                <i class="fa fa-thumbs-up"></i>
                                Acknowledged
                            </button>
                            <button  class='btn btn-primary btn-xs'
                                v-if="!props.row.acknowledge_date"
                                :disabled="true">
                                <i class="fa fa-thumbs-down"></i>
                                Not Acknowledged
                                </button>
                        </span>
                        <button class='btn btn-success btn-xs' v-if="props.row.performance_id"
                        @click="downloadPerformance(props.row.id)" >
                            <i class="fa fa-download"></i>
                            Download
                        </button>
                        <button class='btn btn-success btn-xs' v-if="!props.row.performance_id"
                        @click="download(props.row.id)" >
                            <i class="fa fa-download"></i>
                            Download
                        </button>
                        <button class='btn btn-warning btn-xs'
                            @click="viewEdit(props.row, 'view')">
                            <i class="fa fa-eye"></i>
                            View
                        </button>
                        <span v-if="(role == 'Super Admin Access' || role == 'HR Admin Access')
                        && !props.row.performance_id">
                            <button class='btn btn-primary btn-xs'
                                @click="viewEdit(props.row, 'edit')">
                                <i class="fa fa-pencil"></i>
                                Edit
                            </button>
                        </span>
                        <span v-if="(role != 'Regular User Access') && props.row.performance_id">
                            <button  v-if="!props.row.performance.added_week"
                                class='btn btn-primary btn-xs'
                                @click="viewEdit(props.row, 'add-week')">
                                <i class="fa fa-pencil"></i>
                                Add Week Record
                            </button>
                            <button  v-if="props.row.performance.added_week"
                                class='btn btn-primary btn-xs'
                                disabled="true"
                                @click="viewEdit(props.row, 'add-week')">
                                <i class="fa fa-pencil"></i>
                                Add Week Record
                            </button>
                            <button v-if="user_employee.id == props.row.created_by_employee_id
                            && !props.row.performance.added_week"
                                class='btn btn-primary btn-xs'
                                @click="viewEdit(props.row, 'edit-week')">
                                <i class="fa fa-pencil"></i>
                                Edit Week Record
                            </button>
                        </span>
                    </template>
                    <template slot="employee" slot-scope="props">
                    <div v-if="props.row.user_employee" >
                            {{ props.row.user_employee.first_name }}
                            {{ props.row.user_employee.last_name }}
                        </div>
                    </template>
                    <template slot="supervisor" slot-scope="props">
                    <div v-if="props.row.user_employee
                    && props.row.user_employee.employee_supervisor
                    && props.row.user_employee.employee_supervisor.supervisor_assign" >
                            {{ props.row.user_employee.employee_supervisor.supervisor_assign.first_name }}
                            {{ props.row.user_employee.employee_supervisor.supervisor_assign.last_name }}
                        </div>
                    </template>
                    <template slot="type" slot-scope="props">
                        <div v-if="props.row.performance_id" >
                            Weekly Performance
                        </div>
                        <div v-if="!props.row.performance_id" >
                            <span v-if="props.row.form_type == 0 || props.row.form_type == 1">
                                Monthly Performance</span>
                            <span v-else-if="!props.row.attachment_id">Coaching Log</span>
                        </div>
                    </template>
                    <template slot="form_type" slot-scope="props">
                        <div v-if="props.row.form_type == 0" >Stage One Form</div>
                        <div v-else-if="props.row.form_type == 1" >Stage Two to Five Form</div>
                        <div v-else-if="props.row.form_type == 2" >Admin Form</div>
                        <div v-else-if="props.row.form_type == 3" >HRIS Form</div>
                        <div v-else-if="props.row.form_type == 4" >C&B Form</div>
                        <div v-else-if="props.row.form_type == 5" >Final Pay Form</div>
                        <div v-else-if="props.row.form_type == 6" >Manager Rating HRBP Site Lead Form</div>
                        <div v-else-if="props.row.form_type == 7" >Onboarding Form</div>
                        <div v-else-if="props.row.form_type == 8" >Payroll Form</div>
                        <div v-else-if="props.row.form_type == 9" >Recruitment Form</div>
                        <div v-else-if="props.row.form_type == 10" >Self Rating HRBP Form</div>
                        <div v-else-if="props.row.form_type == 11" >Self Rating HRBP Site Lead  Form</div>
                        <div v-else-if="props.row.form_type == 12" >Sourcing Form</div>
                        <div v-else-if="props.row.form_type == 13" >Recruitment Supervisor Rating Form</div>
                        <div v-else-if="props.row.form_type == 14" >Supervisor Rating HRBP Form</div>
                        <div v-else-if="props.row.form_type == 15" >Training Form</div>
                        <div v-else-if="props.row.performance_id" >Weekly Form</div>
                        <div v-else >Coaching Form</div>
                    </template>
                </v-client-table>

                <v-paginator style="margin-top: -50px; position: absolute;" :options="resource_options"
                :resource_url="resource_url" @update="updateResource" v-show="showPagination" />

            </the-ibox>

            <modal-coaching-log v-if="openModal && (openAction == 'create-weekly'
            || openAction == 'add-week' || openAction == 'edit-week' || openAction == 'add-monthly'
            || openAction == 'edit-monthly-stage-one')" @close="openModal = false">
                <h3 slot="header">{{ headerName }} {{ "to  "+ this.coaching_log.employee_name }}</h3>
                <create-weekly-performance-form v-if="openAction == 'create-weekly'" slot="body"
                :employee_no="splitUserNo(user.employee_name)" />
                <add-week-form v-if="openAction == 'add-week'" slot="body"
                :coachingLogProps="coaching_log" />
                <edit-week-form v-if="openAction == 'edit-week'" slot="body"
                :coachingLogProps="coaching_log" />
                <add-monthly-form v-if="openAction == 'add-monthly'" slot="body"
                :employee_no="splitUserNo(user.employee_name)" />
                <!-- <add-monthly-stage-one-form v-if="openAction == 'edit-monthly-stage-one'"
                slot="body" :coachingLogProps="coaching_log" /> -->
            </modal-coaching-log>

            <modal v-else-if="openModal" @close="openModal = false">
                <h3 slot="header">{{ headerName }} {{ "to  "+ this.coaching_log.employee_name }}</h3>
                <add-form v-if="openAction == 'add'" slot="body"
                :employee_no="splitUserName(user.employee_name)" />
                <view-form v-if="openAction == 'view'" slot="body" :coachingLogProps="coaching_log" />
                <edit-form v-if="openAction == 'edit'" slot="body" :coachingLogProps="coaching_log" />
            </modal>

        </div>
    </div>
</template>

<script>
    import createWeeklyPerformanceForm from '@/modules/coaching_log/createWeeklyPerformanceForm.vue'
    import addWeekForm from '@/modules/coaching_log/addWeekForm.vue'
    import editWeekForm from '@/modules/coaching_log/editWeekForm.vue'
    import addMonthlyForm from '@/modules/coaching_log/addMonthlyForm.vue'
    import addMonthlyStageOneForm from '@/modules/coaching_log/addMonthlyStageOneForm.vue'
    import addForm from '@/modules/coaching_log/addCoachingLogForm.vue'
    import viewForm from '@/modules/coaching_log/viewCoachingLogForm.vue'
    import editForm from '@/modules/coaching_log/editCoachingLogForm.vue'

    export default {
        data: function () {
            return {
                isLoading: false,
                dataForExcel: [],
                role: this.$ep.role,
                user_employee: this.$ep.user,
                user: {
                    employee_name: null,
                },
                forms: '',
                listForms: [],
                coaching_log: {
                    employee_no: '',
                    employee_name: '',
                    findings: "",
                    point_of_disscussion: "",
                    action_plans: "",
                    agents_commitment: "",
                    supervisors_commitment: "",
                    date_start: '',
                    date_end: '',
                    number_occurrence: '',
                    performance: '',
                    offense_id: '',
                    cl_number: '',
                    status: '',
                    received_by: '',
                    issued_by: '',
                    noted_by: '',
                    offense_single: [],
                    offense_multiple: [],
                    others: '',
                    form_type: '',
                },
                selectedRespondent: [],
                columns: [
                    'actions',
                    'cl_number',
                    'employee_no',
                    'employee',
                    'supervisor',
                    'type',
                    'form_type',
                    'acknowledge_date',
                    'created_by',
                ],
                rows:  [],
                options: {
                    headings: {
                        'cl_number': 'Coaching Number',
                        'employee_no': 'Employee Number',
                        'employee' : 'Employee Name',
                        'type': 'Type',
                        'form_type': 'Form Template',
                    },
                    sortable: [],
                    filterable: false,
                },
                openModal: false,
                openAction: "",
                resource_url: '/api/admin/coaching/coaching',
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
                showPagination: true,
                searchClear: true
            }
        },

        components: {
            addForm,
            viewForm,
            editForm,
            createWeeklyPerformanceForm,
            addWeekForm,
            editWeekForm,
            addMonthlyForm,
            addMonthlyStageOneForm
        },

        created(){
                this.eventRespondent();
                this.updateResource(this.rows);
                this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
                this.user.employee_name = null;
            });
        },

        methods: {
            updateResource(data){
                this.isLoading = true
                this.rows = data
                this.isLoading = false
            },
            eventSearch: function (searchKey) {
                if (searchKey) {
                    this.searchClear = false
                    this.isLoading = true
                    this.showPagination = false
                    this.$constants.Coaching_API.get("/search_coaching/"+searchKey)
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
            eventSearchClear: function (searchKey) {
                if (searchKey) {
                    location.reload();
                }
            },
            acknowledge: function(data) {
                this.$swal({
                    title: 'Are you sure you want to Acknowledge?',
                    text: "Once acknowledge, you will not be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Acknowledge it!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                .then((result) => {
                    if (result.value) {
                        this.$constants.Coaching_API.get('/acknowledge/'+data)
                        .then(response => {
                            if (response.data.isValid) {
                                this.$success('Acknowledged!')
                                window.location.href = '/employee/coachinglog'
                            } else {
                               this.$failure('Acknowledge has been cancelled! Error Occured!')
                            }
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$success('Acknowledge has been cancelled!')
                    }
                })

            },
            viewEdit: function (props_row, button_action) {
                this.openModal = true
                this.employeeName(props_row.employee_no);
                if(props_row){
                    if (button_action == "view") {
                        this.headerName = "View";
                        this.openAction = "view";
                        this.coaching_log.id = props_row.id;
                        this.coaching_log.employee_no = props_row.employee_no;
                        this.coaching_log.findings = props_row.findings;
                        this.coaching_log.point_of_disscussion = props_row.point_of_disscussion;
                        this.coaching_log.action_plans = props_row.action_plans;
                        this.coaching_log.agents_commitment = props_row.agents_commitment;
                        this.coaching_log.supervisors_commitment = props_row.supervisors_commitment;
                        this.coaching_log.date_start = props_row.date_start;
                        this.coaching_log.date_end = props_row.date_end;
                        this.coaching_log.number_occurrence = props_row.number_occurrence;
                        this.coaching_log.offense_id = props_row.offense_id;
                        this.coaching_log.cl_number = props_row.cl_number;
                        this.coaching_log.status = props_row.status;
                        this.coaching_log.received_by = props_row.received_by;
                        this.coaching_log.issued_by = props_row.issued_by;
                        this.coaching_log.noted_by = props_row.noted_by;
                        this.coaching_log.offense_multiple = props_row.offense_multiple
                        ? props_row.offense_multiple : null;
                        this.coaching_log.offense_single = props_row.offense_single
                        ? props_row.offense_single : null;
                        this.coaching_log.others = props_row.others ? props_row.others : null;

                    } else if (button_action == "edit") {
                        this.headerName = "Edit";
                        this.openAction = "edit";
                        this.coaching_log.id = props_row.id;
                        this.coaching_log.employee_no = props_row.employee_no;
                        this.coaching_log.findings = props_row.findings;
                        this.coaching_log.point_of_disscussion = props_row.point_of_disscussion;
                        this.coaching_log.action_plans = props_row.action_plans;
                        this.coaching_log.agents_commitment = props_row.agents_commitment;
                        this.coaching_log.supervisors_commitment = props_row.supervisors_commitment;
                        this.coaching_log.date_start = props_row.date_start;
                        this.coaching_log.date_end = props_row.date_end;
                        this.coaching_log.number_occurrence = props_row.number_occurrence;
                        this.coaching_log.offense_id = props_row.offense_id;
                        this.coaching_log.cl_number = props_row.cl_number;
                        this.coaching_log.status = props_row.status;
                        this.coaching_log.received_by = props_row.received_by;
                        this.coaching_log.issued_by = props_row.issued_by;
                        this.coaching_log.noted_by = props_row.noted_by;
                        this.coaching_log.offense_multiple = props_row.offense_multiple
                        ? props_row.offense_multiple : null;
                        this.coaching_log.offense_single = props_row.offense_single
                        ? props_row.offense_single : null;
                        this.coaching_log.others = props_row.others ? props_row.others : null;
                        this.coaching_log.form_type = props_row.form_type;
                    } else if (button_action == "edit-week") {
                         this.headerName = "Edit Week";
                         this.openAction = "edit-week";
                         this.coaching_log.id = props_row.id,
                         this.coaching_log.performance = props_row.performance;
                         this.coaching_log.employee_no = props_row.employee_no;
                         this.coaching_log.findings = props_row.findings;
                         this.coaching_log.point_of_disscussion = props_row.point_of_disscussion;
                         this.coaching_log.action_plans = props_row.action_plans;
                         this.coaching_log.agents_commitment = props_row.agents_commitment;
                         this.coaching_log.supervisors_commitment = props_row.supervisors_commitment;
                         this.coaching_log.date_start = props_row.date_start;
                         this.coaching_log.date_end = props_row.date_end;
                         this.coaching_log.number_occurrence = props_row.number_occurrence;
                         this.coaching_log.offense_id = props_row.offense_id;
                         this.coaching_log.cl_number = props_row.cl_number;
                         this.coaching_log.status = props_row.status;
                         this.coaching_log.received_by = props_row.received_by;
                         this.coaching_log.issued_by = props_row.issued_by;
                         this.coaching_log.noted_by = props_row.noted_by;
                         this.coaching_log.offense_multiple = props_row.offense_multiple
                         ? props_row.offense_multiple : null;
                         this.coaching_log.offense_single = props_row.offense_single
                         ? props_row.offense_single : null;
                         this.coaching_log.others = props_row.others ? props_row.others : null;
                    } else if (button_action == "add-week") {
                         this.headerName = "Add Week";
                         this.openAction = "add-week";
                         this.coaching_log.id = props_row.id,
                         this.coaching_log.performance = props_row.performance;
                         this.coaching_log.employee_no = props_row.employee_no;
                         this.coaching_log.findings = props_row.findings;
                         this.coaching_log.point_of_disscussion = props_row.point_of_disscussion;
                         this.coaching_log.action_plans = props_row.action_plans;
                         this.coaching_log.agents_commitment = props_row.agents_commitment;
                         this.coaching_log.supervisors_commitment = props_row.supervisors_commitment;
                         this.coaching_log.date_start = props_row.date_start;
                         this.coaching_log.date_end = props_row.date_end;
                         this.coaching_log.number_occurrence = props_row.number_occurrence;
                         this.coaching_log.offense_id = props_row.offense_id;
                         this.coaching_log.cl_number = props_row.cl_number;
                         this.coaching_log.status = props_row.status;
                         this.coaching_log.received_by = props_row.received_by;
                         this.coaching_log.issued_by = props_row.issued_by;
                         this.coaching_log.noted_by = props_row.noted_by;
                         this.coaching_log.offense_multiple = props_row.offense_multiple
                         ? props_row.offense_multiple : null;
                         this.coaching_log.offense_single = props_row.offense_single
                         ? props_row.offense_single : null;
                         this.coaching_log.others = props_row.others ? props_row.others : null;
                    }

                }
            },

            employeeName: function (employee) {
                this.$constants.Coaching_API.get("/selected_employee/"+employee)
                .then(response => {
                    this.coaching_log.employee_name = response.data.last_name + ", "
                     + response.data.first_name;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },

            userChecker: function (user) {
                if (user) {
                     document.getElementById("employeee").textContent = ""
                }
            },

            splitUserName: function (data) {
                let employee_no = data.text.split(" ");
                return employee_no[employee_no.length - 1]
            },
            splitUserNo: function (data) {
                let employee_no = data.text.split(" ");
                return employee_no[employee_no.length - 1]
            },

            download: function (data) {
            	window.location.href = '/api/admin/coaching/create_coaching_form/' + data;
            },

            downloadPerformance: function (data) {
            	window.location.href = '/api/admin/coaching/create_weekly_performce_form/' + data;
            },

            createCoachingLogForm: function (data) {
                if (data) {
                    this.openModal = true;
                    this.openAction = "add";
                    this.headerName = "Create Coaching ";
                    this.coaching_log.employee_name = this.user.employee_name.text;
                } else {
                    if (!data) {
                        document.getElementById("employeee").textContent = "Field Required"
                    }
                }
            },

            createWeeklyForm: function (data) {
                if (data) {
                    this.openModal = true;
                    this.openAction = "create-weekly";
                    this.headerName = "Create Weekly Performance ";
                    this.coaching_log.employee_name = this.user.employee_name.text;
               } else {
               	if (!data) {
               	     document.getElementById("employeee").textContent = "Field Required"
               	}
               }
            },

            createMonthlyForm: function (data) {
                if (data) {
                    this.openModal = true;
                    this.openAction = "add-monthly";
                    this.headerName = "Create Monthly Performance ";
                    this.coaching_log.employee_name = this.user.employee_name.text;
                } else {
                    if (!data) {
                         document.getElementById("employeee").textContent = "Field Required"
                    }
                }
            },

            getList: function() {
                this.isLoading = true
                this.$constants.Coaching_API.get("/coaching")
                .then(response => {
                    this.rows = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },

            eventRespondent(){
                this.$constants.Coaching_API.get("/dropdown/users_with_employee_no")
                    .then(response => {
                    this.selectedRespondent = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        }
    }

</script>
