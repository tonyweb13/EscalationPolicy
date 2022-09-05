<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">
            <div class="pull-right" style="margin-top: -55px;">
                <json-excel
                    class   = "btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    name    = "Occupational Safety and Health Report.xls"
                >
                <i class="fa fa-download"></i>
                Export Occupational Safety and Health
                </json-excel>
            </div>
            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="user_id" slot-scope="props">
                    {{ props.row.employee_user.first_name }} {{ props.row.employee_user.last_name }}
                </template>
                <template slot="employee_no" slot-scope="props">
                    {{ props.row.employee_user.employee_no }}
                </template>
                <template slot="designation" slot-scope="props"
                v-if="props.row.employee_user.designation">
                    {{ props.row.employee_user.designation.name }}
                </template>
        </v-client-table>
    </the-ibox>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                rule: this.$ep.rule,
                isLoading: false,
                columns: [
                    'user_id',
                    'employee_no',
                    'designation',
                    'date_finish',
                ],
                rows:  [],
                options: {
                headings: {
                        'user_id': 'Employee Name',
                    },
                    sortable: [
                        'user_id',
                        'employee_no',
                        'designation',
                        'date_finish'
                    ],
                    filterable: [
                        'user_id',
                        'employee_user.first_name',
                        'employee_user.last_name',
                        'employee_user.employee_no',
                        'employee_user.designation.name',
                        'date_finish'
                    ]
                },
                acknowledge_data: {
                    user_id: '',
                    date_finish: ''
                },
                json_fields: {
                    'Employee Name' : {
                        field: 'employee_user',
                        callback: (value) => {
                            return value.first_name + ' ' + value.last_name;
                        }
                    },
                    'Employee Number' : {
                        field: 'employee_user',
                        callback: (value) => {
                            return value.employee_no;
                        }
                    },
                    'Designation' : {
                        field: 'employee_user',
                        callback: (value) => {
                            return value.designation ? value.designation.name : '';
                        }
                    },
                    'Date Finish': 'date_finish'
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
            }
        },
        created() {
            this.getList();
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Default_API.get("/osh/list")
                .then(response => {
                    this.rows = response.data;
                    this.json_data = this.rows;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        }
    }
</script>
