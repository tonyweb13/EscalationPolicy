<template>
    <div>
        <loading-modal v-if="isLoading" />

        <input-search v-if="role != 'Regular User Access'" @search="eventSearch(searchKey)" >
            <input type="text"
                    v-if="role != 'Regular User Access'"
                    class="form-control search-custom-input"
                    placeholder="Type Employee ID or First Name or Last Name here..."
                    v-model="searchKey"
                    @change='eventSearch(searchKey)'
                >
        </input-search>

        <v-client-table :data="rows" :columns="columns" :options="options" >
            <template slot="action" slot-scope="props">
                <div style="width:10%">
                    <button class="center-block btn btn-warning btn-xs"  @click="viewRow(props.row, 'view')">
                        View
                    </button>
                </div>
            </template>
            <template slot="offense" slot-scope="props">
                <div style="width:200px;">
                    {{ props.row.description_of_the_offense }}
                </div>
            </template>
        </v-client-table>

            <v-paginator style="margin-top: -50px; position: absolute;" :options="resource_options"
            :resource_url="resource_url" @update="updateResource"  v-show="showPagination" />

        <modal v-if="openModal" @close="openModal = false">
            <h3 slot="header">View Close Details</h3>
            <view-form-close :closedProps="closed_data" slot="body" />
        </modal>
    </div>
</template>
<script>
    import viewFormClose from '@/modules/admin/incidentreport/viewFormClose.vue'

    export default {
        components: {
            viewFormClose,
		},
        data: function () {
            return {
                isLoading: false,
                role: this.$ep.role,
                columns: [
                    'action',
                    'employee_id',
                    'last_name',
                    'first_name',
                    'position',
                    'site',
                    'dm',
                    'immediate_supervisor',
                    'date_of_offense',
                    'coc_provision',
                    'offense',
                    'gravity',
                    'case_status',
                    'year_nte_draft',
                    'month_nte_draft',
                ],
                rows:  [],
                options: {
                    sortable: [
                        'employee_id',
                        'last_name',
                        'first_name',
                        'position',
                        'site',
                        'dm',
                        'immediate_supervisor',
                        'date_of_offense',
                        'coc_provision',
                        'offense',
                        'gravity',
                        'case_status',
                        'year_nte_draft',
                        'month_nte_draft',
                    ],
                    filterable: false,
                },
                openModal: false,
                closed_data: {
                    ageing: '',
                    case_status: '',
                    coc_provision: '',
                    da_imposed: '',
                    date_admin_hearing: '',
                    date_da: '',
                    date_hr_received: '',
                    date_nte_draft: '',
                    date_of_offense: '',
                    days_exceeded_tat: '',
                    description_of_the_offense: '',
                    dm: '',
                    employee_id: '',
                    first_name: '',
                    gravity: '',
                    hrbp: '',
                    id: '',
                    immediate_supervisor: '',
                    last_name: '',
                    month_nte_draft: '',
                    notes_for_case: '',
                    nte_date_served: '',
                    nte_request: '',
                    position: '',
                    quarter: '',
                    rank: '',
                    site: '',
                    stage_of_the_case: '',
                    tat_status: '',
                    week: '',
                    year_nte_draft: '',
                },
                resource_url: '/api/admin/closed',
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
            if (this.$route.hash == '#closed') {
                this.updateResource(this.rows);
            }
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
                    this.$constants.Admin_API.get("/search/closed/"+searchKey)
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
                this.close_data = {
                    ageing: '',
                    case_status: '',
                    coc_provision: '',
                    da_imposed: '',
                    date_admin_hearing: '',
                    date_da: '',
                    date_hr_received: '',
                    date_nte_draft: '',
                    date_of_offense: '',
                    days_exceeded_tat: '',
                    description_of_the_offense: '',
                    dm: '',
                    employee_id: '',
                    first_name: '',
                    gravity: '',
                    hrbp: '',
                    id: '',
                    immediate_supervisor: '',
                    last_name: '',
                    month_nte_draft: '',
                    notes_for_case: '',
                    nte_date_served: '',
                    nte_request: '',
                    position: '',
                    quarter: '',
                    rank: '',
                    site: '',
                    stage_of_the_case: '',
                    tat_status: '',
                    week: '',
                    year_nte_draft: '',
                };
            },
            // getList: function() {
            //     this.isLoading = true
            //     this.$constants.Admin_API.get("/closed")
            //         .then(response => {
            //             this.rows = response.data;
            //             this.isLoading = false
            //         })
            //         .catch(error => {
            //             this.globalErrorHandling(error);
            //         });
            // },
            viewRow: function (props_row, button_action) {
                console.log('PROPS_ROW', props_row)
                this.openModal = true
                this.headerName = "View";
                this.openAction = "view";

                this.closed_data.ageing = props_row.ageing;
                this.closed_data.case_status = props_row.case_status;
                this.closed_data.coc_provision = props_row.coc_provision;
                this.closed_data.da_imposed = props_row.da_imposed;
                this.closed_data.irr_exist = props_row.irr_exist;
                this.closed_data.offense_exist = props_row.offense_exist;
                this.closed_data.date_admin_hearing = props_row.date_admin_hearing;
                this.closed_data.date_da = props_row.date_da;
                this.closed_data.date_hr_received = props_row.date_hr_received;
                this.closed_data.date_nte_draft = props_row.date_nte_draft;
                this.closed_data.date_of_offense = props_row.date_of_offense;
                this.closed_data.days_exceeded_tat = props_row.days_exceeded_tat;
                this.closed_data.description_of_the_offense = props_row.description_of_the_offense;
                this.closed_data.dm = props_row.dm;
                this.closed_data.employee_id = props_row.employee_id;
                this.closed_data.first_name = props_row.first_name;
                this.closed_data.gravity = props_row.gravity;
                this.closed_data.hrbp = props_row.hrbp;
                this.closed_data.id = props_row.id;
                this.closed_data.immediate_supervisor = props_row.immediate_supervisor;
                this.closed_data.last_name = props_row.last_name;
                this.closed_data.month_nte_draft = props_row.month_nte_draft;
                this.closed_data.notes_for_case = props_row.notes_for_case;
                this.closed_data.nte_date_served = props_row.nte_date_served;
                this.closed_data.nte_request = props_row.nte_request;
                this.closed_data.position = props_row.position;
                this.closed_data.quarter = props_row.quarter;
                this.closed_data.rank = props_row.rank;
                this.closed_data.site = props_row.site;
                this.closed_data.stage_of_the_case = props_row.stage_of_the_case;
                this.closed_data.tat_status = props_row.tat_status;
                this.closed_data.week = props_row.week;
                this.closed_data.year_nte_draft = props_row.year_nte_draft;

            },
        }
    }
</script>
