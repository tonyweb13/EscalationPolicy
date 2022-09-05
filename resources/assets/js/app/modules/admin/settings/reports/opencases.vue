<template>
    <div>
        <loading-modal-report v-if="isLoading" />
        <json-excel
            class   = "btn btn-primary"
            :fetch  = "fetchOpen"
            :fields = "json_fields_open"
            name    = "Open Cases Report.xls"
        >
        Export Open Cases Report
        </json-excel>
    </div>
</template>
<script>

export default {

    data: function () {
        return {
            isLoading: false,
            json_fields_open: {
                'Status' : {
                    field: 'status_id',
                    callback: (value) => {
                        if (value == 1) {
                            return 'New';
                        } else if (value == 2) {
                            return 'In Progress';
                        } else if (value == 3) {
                            return 'On Hold'
                        }
                    }
                },
	            'IR Number' : {
                    field: 'ir_number',
                    callback: (value) => {
                        return value ? `#${value}` : 'No IR Number';
                    }
                },
                'Reported' : 'complainant.created_at',
                'Complainant' : {
                    field: 'complainant.complainant_user',
                    callback: (value) => {
                        return value.first_name + ' ' + value.last_name;
                    }
                },
                'Employee No.' : 'respondent_user.employee_no',
                'Respondent' : {
                    field: 'respondent_user',
                    callback: (value) => {
                        return value.first_name + ' ' + value.last_name;
                    }
                },
                'Designation' : 'respondent_user.designation.name',
                'Site' : 'respondent_user.office_location.name',
                'Supervisor' : {
                    field: 'respondent_user.employee_supervisor.supervisor_assign',
                    callback: (value) => {
                        return value ? value.first_name + ' ' + value.last_name: '';
                    }
                },
                'Response' : 'response',
                'Category' : 'complainant.offense.category.name',
                'Gravity' : 'complainant.offense.gravity.gravity',
                'Prescription Period' : 'complainant.offense.gravity.prescriptive_period',
                'Offense' : 'complainant.offense.offense',
                'HRBP' : {
                    field: 'hrbp_user',
                    callback: (value) => {
                        return value.first_name + ' ' + value.last_name;
                    }
                },
                'HRBP Acknowledge' : {
                    field: 'hrbp_acknowledge_response',
                    callback: (value) => {
                        if (value == 1) {
                            return 'Yes';
                        } else {
                            return 'No';
                        }
                    }
                },
                'Generate NTE / Invalid IR' : {
                    field: 'incident_report.is_generate_nte_invalid_ir',
                    callback: (value) => {
                        if (value == 1) {
                            return 'Generate NTE';
                        } else if (value == 2) {
                            return 'Invalid IR';
                        } else if (value == 3) {
                            return 'In Review'
                        } else if (value == 4) {
                            return 'Generate NTE with PS'
                        } else {
                            return ''
                        }
                    }
                },
                'Date NTE Serve' : {
                    field: 'incident_report.date_nte_serve',
                    callback: (value) => {
                        return value ? value : '';
                    }
                },
                'Date Admin Hearing' : {
                    field: 'incident_report.date_admin_hearing',
                    callback: (value) => {
                        return value ? value : '';
                    }
                },
                'Time Admin Hearing' : {
                    field: 'incident_report.time_admin_hearing',
                    callback: (value) => {
                        return value ? value : '';
                    }
                },
                'Meeting Place' : {
                    field: 'incident_report.meeting_place',
                    callback: (value) => {
                        return value ? value : '';
                    }
                },
                'Remarks' : {
                    field: 'remarks',
                    callback: (value) => {
                        return value ? value : '';
                    }
                },
                'TAT' : 'TAT',
                'Ageing' : 'ageing',
            },
        }
    },
    methods:{
        async fetchOpen(){
            this.isLoading = true
            const response = await this.$constants.Admin_API.get("/open/cases/export_open");
            console.log("json_fields_open response==", response);
            this.isLoading = false
            return response.data;
        },
    }
}
</script>

