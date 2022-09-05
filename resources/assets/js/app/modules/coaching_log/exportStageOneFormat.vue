<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Export Stage One Format.csv"
        >
        <i class="fa fa-download"></i>
            Export Stage One CSV Format
        </json-excel>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                dataForExcel: [
                    {
                        'Stage of KPI Score Card': ' ',
                        'Employee ID': ' ',
                        'Loan Amount Target': ' ',
                        'Loan Amount Rating EOM': ' ',
                        'Loan Amount Rating W2': ' ',
                        'Loan Amount Rating W3': ' ',
                        'Loan Amount Rating W4': ' ',
                        'Loan Amount Computation': ' ',
                        'Loan Amount Comment': ' ',
                        'Knowledge Target': ' ',
                        'Knowledge Rating': ' ',
                        'Knowledge Computation': ' ',
                        'Knowledge Comment': ' ',
                        'QA Score Target': ' ',
                        'QA Score Rating': ' ',
                        'QA Score Computation': ' ',
                        'QA Score Comment': ' ',
                        'Coaching Log Target': ' ',
                        'Coaching Log Rating': ' ',
                        'Coaching Log Computation': ' ',
                        'Coaching Log Comment': ' ',
                        'Attendance Reliability Target': ' ',
                        'Attendance Reliability Rating': ' ',
                        'Attendance Reliability Computation': ' ',
                        'Attendance Reliability Comment': ' ',
                        'Strengths': ' ',
                        'Weakness': ' ',
                        'Action Plans': ' ',
                    },
                ]
            };
        },
    }
</script>
