<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Recruitment.csv"
        >
        <i class="fa fa-download"></i>
            Export Recruitment CSV Format
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
                        "Recruitment Productivity Number of Hires Target": ' ',
                        "Recruitment Productivity Number of Hires Actual Rating": ' ',
                        "Recruitment Productivity Number of Hires Weight of KPI": ' ',
                        "Recruitment Productivity Number of Hires Rating": ' ',
                        "Recruitment Productivity Number of Hires Comments": ' ',
                        "Recruitment Productivity Time to Fill Target": ' ',
                        "Recruitment Productivity Time to Fill Actual Rating": ' ',
                        "Recruitment Productivity Time to Fill Weight of KPI": ' ',
                        "Recruitment Productivity Time to Fill Rating": ' ',
                        "Recruitment Productivity Time to Fill Comments": ' ',
                        'Work Ethics Number of Escalation Target': ' ',
                        'Work Ethics Number of Escalation Actual Rating': ' ',
                        'Work Ethics Number of Escalation Weight of KPI': ' ',
                        'Work Ethics Number of Escalation Rating': ' ',
                        'Work Ethics Number of Escalation Comments': ' ',
                        'Work Ethics No NTE Target': ' ',
                        'Work Ethics No NTE Actual Rating': ' ',
                        'Work Ethics No NTE Weight of KPI': ' ',
                        'Work Ethics No NTE Rating': ' ',
                        'Work Ethics No NTE Comments': ' ',
                        'Recruitment Satisfaction Survey Target': ' ',
                        'Recruitment Satisfaction Survey Actual Rating': ' ',
                        'Recruitment Satisfaction Survey Weight of KPI': ' ',
                        'Recruitment Satisfaction Survey Rating': ' ',
                        'Recruitment Satisfaction Survey Comments': ' ',
                        'Total': ' ',
                        'Development Plan': ' ',
                        'Strenghts': ' ',
                        "Immediate Manager's Feedback": ' ',
                        "Employee's Acknowledgement": ' ',
                    },
                ]
            };
        },
    }
</script>
