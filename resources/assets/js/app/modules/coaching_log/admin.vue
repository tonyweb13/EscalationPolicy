<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Admin.csv"
        >
        <i class="fa fa-download"></i>
            Export Admin CSV Format
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
                        "Attendance Target": ' ',
                        "Attendance Actual Rating": ' ',
                        "Attendance Weight of KPI": ' ',
                        "Attendance Rating": ' ',
                        "Attendance Comments": ' ',
                        "Punctuality Target": ' ',
                        "Punctuality Actual Rating": ' ',
                        "Punctuality Weight of KPI": ' ',
                        "Punctuality Rating": ' ',
                        "Punctuality Comments": ' ',
                        "Process Knowledge Target": ' ',
                        "Process Knowledge Actual Rating": ' ',
                        "Process Knowledge Weight of KPI": ' ',
                        "Process Knowledge Rating": ' ',
                        "Process Knowledge Comments": ' ',
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
