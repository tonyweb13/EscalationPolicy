<template>
    <div class="pull-right" >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Export Stage One Format.csv"
        >
        <i class="fa fa-download"></i>
            Export C&B CSV Format
        </json-excel>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                dataForExcel: [
                    {
                        'Employee ID': ' ',
                        'Name': ' ',
                        'Evaluation Period': ' ',
                        'Performance Period Covered': ' ',
                        'Position Title': ' ',
                        'Department': ' ',
                        "Recruitment Team's Productivity Support Hiring Target": ' ',
                        "Recruitment Team's Productivity Support Hiring Actual Rating": ' ',
                        "Recruitment Team's Productivity Support Hiring Weight of KPI": ' ',
                        "Recruitment Team's Productivity Support Hiring Rating": ' ',
                        "Recruitment Team's Productivity Support Hiring Comments": ' ',
                        "Recruitment Team's Productivity Agent Hiring Target": ' ',
                        "Recruitment Team's Productivity Agent Hiring Actual Rating": ' ',
                        "Recruitment Team's Productivity Agent Hiring Weight of KPI": ' ',
                        "Recruitment Team's Productivity Agent Hiring Rating": ' ',
                        "Recruitment Team's Productivity Agent Hiring Comments": ' ',
                        'People Development Target': ' ',
                        'People Development Actual Rating': ' ',
                        'People Development Weight of KPI': ' ',
                        'People Development Rating': ' ',
                        'People Development Comments': ' ',
                        'Work Ethics Number of Escalation Target': ' ',
                        'Work Ethics Number of Escalation Actual Rating': ' ',
                        'Work Ethics Number of Escalation Weight of KPI': ' ',
                        'Work Ethics Number of Escalation Rating': ' ',
                        'Work Ethics Number of Escalation Comments': ' ',
                        'Work Ethics infractions in the code of conduct Target': ' ',
                        'Work Ethics infractions in the code of conduct Actual Rating': ' ',
                        'Work Ethics infractions in the code of conduct Weight of KPI': ' ',
                        'Work Ethics infractions in the code of conduct Rating': ' ',
                        'Work Ethics infractions in the code of conduct Comments': ' ',
                        "Recruitment Recruitment Team's Satisfaction Survey Target": ' ',
                        "Recruitment Recruitment Team's Satisfaction Survey Actual Rating": ' ',
                        "Recruitment Recruitment Team's Satisfaction Survey Weight of KPI": ' ',
                        "Recruitment Recruitment Team's Satisfaction Survey Rating": ' ',
                        "Recruitment Recruitment Team's Satisfaction Survey Comments": ' ',
                        'Total': ' ',
                        'Development Plan': ' ',
                        'Weaknesses': ' ',
                        'Strenghts': ' ',
                        "IMMEDIATE MANAGER'S FEEDBACK": ' ',
                        "EMPLOYEE'S ACKNOWLEDGEMENT": ' ',
                    },
                ]
            };
        },
    }
</script>
