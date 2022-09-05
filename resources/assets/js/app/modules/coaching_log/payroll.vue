<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Payroll.csv"
        >
        <i class="fa fa-download"></i>
            Export Payroll CSV Format
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
                        'Evaluation Period': ' ',
                        'Name Of Evaluator': ' ',
                        'Initiative Target': ' ',
                        'Initiative Weight': ' ',
                        'Initiative Rating Scale': ' ',
                        'Initiative Final Score': ' ',
                        'Work Ethics Target': ' ',
                        'Work Ethics Weight': ' ',
                        'Work Ethics Rating Scale': ' ',
                        'Work Ethics Final Score': ' ',
                        'Responsiveness Target': ' ',
                        'Responsiveness Weight': ' ',
                        'Responsiveness Rating Scale': ' ',
                        'Responsiveness Final Score': ' ',
                        'Attendance Reliability Target': ' ',
                        'Attendance Reliability Weight': ' ',
                        'Attendance Reliability Rating Scale': ' ',
                        'Attendance Reliability Final Score': ' ',
                        'Payroll Reports Target': ' ',
                        'Payroll Reports Weight': ' ',
                        'Payroll Reports Rating Scale': ' ',
                        'Payroll Reports Final Score': ' ',
                        'Government Remittance Target': ' ',
                        'Government Remittance Weight': ' ',
                        'Government Remittance Rating Scale': ' ',
                        'Government Remittance Final Score': ' ',
                        'Number Of Escalation Target': ' ',
                        'Number Of Escalation Weight': ' ',
                        'Number Of Escalation Rating Scale': ' ',
                        'Number Of Escalation Final Score': ' ',
                        'Total Score': ' ',
                        'Commendation   ': ' ',
                        'Action Plans': ' ',
                    },
                ]
            };
        },
    }
</script>
