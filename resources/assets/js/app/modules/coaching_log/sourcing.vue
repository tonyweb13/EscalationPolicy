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
                        "Sourcing Productivity Number of ShowUp Target": ' ',
                        "Sourcing Productivity Number of ShowUp Actual Rating": ' ',
                        "Sourcing Productivity Number of ShowUp Weight of KPI": ' ',
                        "Sourcing Productivity Number of ShowUp Rating": ' ',
                        "Sourcing Productivity Number of ShowUp Comments": ' ',
                        "Sourcing Productivity Time to Fill Target": ' ',
                        "Sourcing Productivity Time to Fill Actual Rating": ' ',
                        "Sourcing Productivity Time to Fill Weight of KPI": ' ',
                        "Sourcing Productivity Time to Fill Rating": ' ',
                        "Sourcing Productivity Time to Fill Comments": ' ',
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
                        "Sourcing Initiative Target": ' ',
                        "Sourcing Initiative Actual Rating": ' ',
                        "Sourcing Initiative Weight of KPI": ' ',
                        "Sourcing Initiative Rating": ' ',
                        "Sourcing Initiative Comments": ' ',
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
