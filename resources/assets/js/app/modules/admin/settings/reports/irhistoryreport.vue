<template>
    <div>
        <loading-modal-report v-if="isLoading" />
        <json-excel
            class   = "btn btn-success"
            :fetch  = "fetchIRHistory"
            :fields = "json_fields_irhistory"
            name    = "IR History Report.xls"
        >
        Export IR History Quarterly Report
        </json-excel>
    </div>
</template>
<script>

export default {

    data: function () {
        return {
            isLoading: false,
            json_fields_irhistory: {
                'IR Number' : {
                    field: 'ir_number',
                    callback: (value) => {
                        return value ? `#${value}` : 'No IR Number';
                    }
                },
                'Employee Number' : 'employee_id',
                'Last Name' : 'last_name',
                'First Name' : 'first_name',
                'Position' : 'position',
                'Site' : 'site',
                'HRBP' : 'hrbp',
                'DM' : 'dm',
                'Immediate Supervisor' : 'immediate_supervisor',
                'Date of Offense' : 'date_of_offense',
                'NTE Request' : 'nte_request',
                'Date NTE Draft' : 'date_nte_draft',
                'COC Provision' : 'coc_provision',
                'Description of the Offense' : 'description_of_the_offense',
                'Gravity' : 'gravity',
                'NTE Date Served' : 'nte_date_served',
                'Date HR Received' : 'date_hr_received',
                'Date Admin Hearing' : 'date_admin_hearing',
                'DA Imposed' : 'da_imposed',
                'Date DA' : 'date_da',
                'Stage of the Case' : 'stage_of_the_case',
                'Notes for Case' : 'notes_for_case',
                'Case Status' : 'case_status',
                'Ageing' : 'ageing',
                'Days Exceeded TAT' : 'days_exceeded_tat',
                'TAT Status' : 'tat_status',
                'Year NTE Draft' : 'year_nte_draft',
                'Month NTE Draft' : 'month_nte_draft',
                'Week' : 'week',
                'Quarter' : 'quarter',
                'Rank' : 'rank',
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
        async fetchIRHistory(){
            this.isLoading = true
            const response = await this.$constants.Admin_API.get("/irhistory/export_irhist");
            console.log("json_fields_irhistory response==", response);
            this.isLoading = false
            return response.data;
        },
    }
}
</script>

