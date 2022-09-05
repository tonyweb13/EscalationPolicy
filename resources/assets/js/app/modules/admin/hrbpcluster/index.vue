<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="List of HRBP Cluster">

            <!-- <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> -->
            <export-format v-if="role == 'HR Access' || role == 'HR Admin Access' || role == 'Super Admin Access'" />
            <div class="pull-right" style="margin-top: -55px; margin-right: 130px;">
                <json-excel
                    class   = "btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    name    = "HRBP cluster offense.xls"
                    v-if="rule.hrbp_cluster.child_rules.add.is_enable"
                >
                <i class="fa fa-file-excel-o"></i>
                Export HRBP Cluster
                </json-excel>
            </div>
            <button-add @add="addEditRow()" thingToAdd="Cluster"
            v-if="rule.hrbp_cluster.child_rules.edit.is_enable"/>

                <mass-upload v-if="role == 'HR Access' || role == 'HR Admin Access' || role == 'Super Admin Access'"/>

                <v-client-table :data="rows" :columns="columns" :options="options">
                    <template slot="hrsl_employee_no" slot-scope="props">
                        <span v-if="props.row.hrsl_user">
                            {{ props.row.hrsl_user.employee_no }}
                        </span>
                    </template>
                    <template slot="hrsl" slot-scope="props">
                        <span v-if="props.row.hrsl_user">
                            {{ props.row.hrsl_user.first_name }}
                            {{ props.row.hrsl_user.last_name }}
                        </span>
                    </template>
                    <template slot="hrbp_employee_no" slot-scope="props">
                        <span v-if="props.row.hrbp_user">
                            {{ props.row.hrbp_user.employee_no }}
                        </span>
                    </template>
                    <template slot="hrbp" slot-scope="props">
                        <span v-if="props.row.hrbp_user">
                            {{ props.row.hrbp_user.first_name }}
                            {{ props.row.hrbp_user.last_name }}
                        </span>
                    </template>
                    <template slot="employee_no" slot-scope="props">
                        <span v-if="props.row.employee_current">
                            {{ props.row.employee_current.employee_no }}
                        </span>
                    </template>
                    <template slot="employee" slot-scope="props">
                        <span v-if="props.row.employee_current">
                            {{ props.row.employee_current.first_name }}
                            {{ props.row.employee_current.last_name }}
                        </span>
                    </template>
                    <template slot="designation" slot-scope="props">
                        <span v-if="props.row.employee_current && props.row.employee_current.designation">
                            {{ props.row.employee_current.designation.name }}
                        </span>
                    </template>

                    <template slot="teamlead" slot-scope="props">
                        <span v-if="props.row.supervisor">
                            {{ props.row.supervisor.first_name }}
                            {{ props.row.supervisor.last_name }}
                        </span>
                    </template>

                    <template slot="manager" slot-scope="props">
                    <span v-if="props.row.dm">

                    {{ props.row.dm.first_name }}
                    {{ props.row.dm.last_name }}

                    </span>
                    </template>

                    <template slot="actions" slot-scope="props">
                        <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)">
                            <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                        <!-- <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)">
                            <i class="fa fa-remove"></i> {{ labels.deleteBtn }}</button> -->
                    </template>
                </v-client-table>

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit v-if="openAction == 'addEdit'" :clusterProps="cluster_data" slot="body" />
            </modal>
        </the-ibox>
    </div>
</template>
<script>
    import addEdit from '@/modules/admin/hrbpcluster/addEdit.vue'
    import exportFormat from '@/modules/admin/hrbpcluster/exportFormat.vue'
    import massUpload from '@/modules/admin/hrbpcluster/massUpload.vue'

    export default {
        components: {
            addEdit,
            exportFormat,
            massUpload
        },
        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Add Cluster Offense',
                            edit: 'Edit Cluster Offense',
                            editBtn: 'Edit',
                            deleteBtn: 'Archive'
                        }
                    }
                },

            },
        data: function () {
            return {
                rule: this.$ep.rule,
                role: this.$ep.role,
                isLoading: false,
                columns: this.columns,
                openAction: '',
                rows:  [],
                options: {
                    headings: {
                        'site_office.name': 'HRSL Location',
                        'hrsl': 'HR Site Leader',
                        'hrsl_user.email_address': 'HR Site Leader Email',
                        'hrbp': 'HRBP Assign',
                        'hrbp_user.email_address': 'HRBP Email',
                        'teamlead': 'Supervisor',
                        'manager': 'Manager',
                    },
                    sortable: [
                        'site_office.name',
                        'hrsl',
                        'hrbp',
                        'employee',
                        'designation',
                        'teamlead',
                        'manager'
                    ],
                    filterable: [
                        'site_office.name',
                        'hrsl_user.first_name',
                        'hrsl_user.last_name',
                        'hrbp_user.first_name',
                        'hrbp_user.last_name',
                        'employee_current.first_name',
                        'employee_current.last_name',
                        'employee_current.designation',
                        'employee_current.employee_no',
                        'hrsl_user.email_address',
                        'hrsl_user.employee_no',
                        'hrbp_user.employee_no',
                        'dm.first_name',
                        'dm.last_name',
                        'supervisor.first_name',
                        'supervisor.last_name'
                    ],
                },
                headerName: '',
                openModal: false,
                cluster_data: {
                    cluster_id: '',
                    hrsl_employee_no: '',
                    hrsl_email_address: '',
                    hrbp_employee_no: '',
                    hrbp_email_address: '',
                    hrsl_office_location_id: '',
                    hrsl_name: '',
                    hrsl_office_location_name: '',
                    user_employee_no: '',
                    employee_name: '',
                    supervisor_employee_no: '',
                    supervisor_name: '',
                    manager_employee_no: '',
                    manager_name: '',
                },
                json_fields: {
                    'HR Site Leader': 'hrsl_employee_no',
                    'HR Site Leader Email Address': 'hrsl_email_address',
                    'HRBP Employee No.': 'hrbp_employee_no',
                    'HRBP Email Address': 'hrbp_email_address',
                    'HRBP Office Location': 'hrsl_office_location_id',
                    'Team Lead Employee No.': 'user_employee_no'
                },
                json_data: [],
                json_meta: [
                    [
                        {
                            'key': 'charset',
                            'value': 'utf-8'
                        }
                    ]
                ],
                upload_data: {}
            }
        },
        created(){
            this.columns = (this.rule.hrbp_cluster.child_rules.edit.is_enable
            || this.rule.hrbp_cluster.child_rules.archive.is_enable) ?
                [
                    'actions',
                    'site_office.name',
                    'hrsl_employee_no',
                    'hrsl',
                    'hrsl_email_address',
                    'hrbp_employee_no',
                    'hrbp',
                    'hrbp_email_address',
                    'employee_no',
                    'employee',
                    'designation',
                    'teamlead',
                    'manager'
                ] :
                [
                    'site_office.name',
                    'hrsl_employee_no',
                    'hrsl',
                    'hrsl_email_address',
                    'hrbp_employee_no',
                    'hrbp',
                    'hrbp_email_address',
                    'employee_no',
                    'employee',
                    'designation',
                    'teamlead',
                    'manager'
                ];

            this.getList();
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            eventModalClose: function () {
                this.cluster_data = {
                    hrsl_employee_no: '',
                    hrsl_email_address: '',
                    hrbp_employee_no: '',
                    hrbp_email_address: '',
                    hrsl_office_location_id: '',
                    hrsl_name: '',
                    hrsl_office_location_name: '',
                    user_employee_no: '',
                    employee_name: '',
                    supervisor_employee_no: '',
                    supervisor_name: '',
                    manager_employee_no: '',
                    manager_name: '',
                };
            },
            getList: function() {
                this.isLoading = true
                this.$constants.Admin_API.get("/hrbp/cluster")
                    .then(response => {
                        this.rows = response.data;
                        this.json_data = this.rows;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            addEditRow: function (props_row) {
                this.openModal = true;
                this.openAction = 'addEdit'
                if(props_row){
                    this.headerName = this.labels.edit;
                    this.cluster_data.cluster_id = props_row.id;

                    if (props_row.site_office) {
                        this.cluster_data.hrsl_office_location_id = props_row.site_office.id;
                        this.cluster_data.hrsl_office_location_name = props_row.site_office.name;
                    }

                    if (props_row.hrsl_user) {
                        this.cluster_data.hrsl_employee_no = props_row.hrsl_employee_no;
                        this.cluster_data.hrsl_email_address = props_row.hrsl_user.email_address
                        this.cluster_data.hrsl_name = props_row.hrsl_user.first_name + " " +
                                                      props_row.hrsl_user.last_name;
                    }

                    if (props_row.hrbp_user) {
                        this.cluster_data.hrbp_employee_no = props_row.hrbp_employee_no;
                        this.cluster_data.hrbp_email_address = props_row.hrbp_user.email_address

                        this.cluster_data.hrbp_name = props_row.hrbp_user.first_name + " " +
                                                      props_row.hrbp_user.last_name;
                    }

                    if (props_row.employee_current) {
                        this.cluster_data.user_employee_no = props_row.employee_current.employee_no;

                        this.cluster_data.employee_name = props_row.employee_current.first_name + " " +
                        props_row.employee_current.last_name;
                    }

                    if (props_row.cluster_alignment && props_row.cluster_alignment.supervisor_assign) {
                        this.cluster_data.supervisor_employee_no =
                        props_row.cluster_alignment.supervisor_assign.employee_no;

                        this.cluster_data.supervisor_name =
                        props_row.cluster_alignment.supervisor_assign.first_name + " " +
                        props_row.cluster_alignment.supervisor_assign.last_name;
                    }

                    if (props_row.cluster_alignment && props_row.cluster_alignment.supervisor_assign
                        && props_row.cluster_alignment.supervisor_assign.employee_supervisor
                        && props_row.cluster_alignment.supervisor_assign.employee_supervisor.supervisor_assign) {
                        this.cluster_data.manager_employee_no =
                        props_row.cluster_alignment.supervisor_assign.employee_supervisor.supervisor_assign.employee_no;

                        this.cluster_data.manager_name =
                        props_row.cluster_alignment.supervisor_assign.employee_supervisor.supervisor_assign.first_name
                        + " " +
                        props_row.cluster_alignment.supervisor_assign.employee_supervisor.supervisor_assign.last_name
                    }

                } else {
                    this.headerName = this.labels.add;
                    this.eventModalClose();
                }
            },
            eventMassUpload: function () {
                this.openModal = true;
                this.headerName = "HRBP Mass Upload";
                this.openAction = 'massUpload';
                // this.upload_data = {}
            }
        }
    }
</script>
