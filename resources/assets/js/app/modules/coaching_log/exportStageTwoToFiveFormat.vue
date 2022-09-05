<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Export Stage Two To Five Format.csv"
        >
        <i class="fa fa-download"></i>
            Export Stage Two To Five CSV Format
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
                        'Loan Amount Rating': ' ',
                        'Loan Amount Computation': ' ',
                        'Loan Amount Comment': ' ',
                        'QA Compliance Target': ' ',
                        'QA Compliance Rating': ' ',
                        'QA Compliance Computation': ' ',
                        'QA Compliance Comment': ' ',
                        'QA Score Target': ' ',
                        'QA Score Rating': ' ',
                        'QA Score Computation': ' ',
                        'QA Score Comment': ' ',
                        'Correction Target': ' ',
                        'Correction Rating': ' ',
                        'Correction Computation': ' ',
                        'Correction Comment': ' ',
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
