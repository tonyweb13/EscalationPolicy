<template>
    <div>
        <loading-modal-report v-if="isLoading" />
        <json-excel
            class   = "btn btn-warning"
            :fetch  = "fetchCoachingLog"
            :fields = "json_fields_coaching"
            name    = "Coaching Log Report.xls"
        >
        Export Coaching Log Report
        </json-excel>
    </div>
</template>
<script>

export default {

    data: function () {
        return {
            isLoading: false,
            json_fields_coaching: {
                'CL Number' : {
                    field: 'cl_number',
                    callback: (value) => {
                        return value ? `#${value}` : '';
                    }
                },
                'Employee Number' : 'employee_no',
                'Employee Name' : {
                    field: 'user_employee',
                    callback: (value) => {
                        return value ? value.first_name + ' ' + value.last_name : '';
                    }
                },
                'HRBP' : {
                    field: 'hrbp_coaching.hrbp_user',
                    callback: (value) => {
                        return value ? value.first_name + ' ' + value.last_name: '';
                    }
                },
                'Site' : 'hrbp_coaching.site_office.name',
                'Offense' : 'offense.offense',
                'Type' : 'performance',
                'Form Template' : 'form_type',
                'Findings' : 'findings',
                'Point of Disscussion' : 'point_of_disscussion',
                'Action Plans' : 'action_plans',
                'Agents Commitment' : 'agents_commitment',
                'Supervisors Commitment' : 'supervisors_commitment',
                'Date Start' : 'date_start',
                'Date End' : 'date_end',
                'No. Occurrence' : 'number_occurrence',
                'Others' : 'others',
                'Acknowledge_date' : 'acknowledge_date',
                'Performance Review' : 'performance_review',
                'Status' : 'status',
                'Received By' : 'received_by',
                'Issued By' : 'issued_by',
                'Noted By' : 'noted_by',
                'Created By' : 'created_by',
                'Date Created' : {
                    field: 'created_at',
                    callback: (value) => {
                        if (value) {
                            return moment(String(value)).format('YYYY-MM-DD')
                        }
                    },
                },
            },
            json_meta: [
                [
                    {
                    key: 'charset',
                    value: 'utf-8',
                    },
                ],
            ],
        }
    },
    methods:{
        async fetchCoachingLog(){
            this.isLoading = true
            const response = await this.$constants.Coaching_API.get("/export_coachinglog");
            this.isLoading = false
            return response.data;
        },
    }
}
</script>

