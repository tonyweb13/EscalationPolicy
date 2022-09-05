<template>
    <div >
        <json-excel
            ref="onboard"
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Onboard.csv"
        >
        <i class="fa fa-download"></i>
            Export Onboard CSV Format
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
                        'Completion of Requirements Target': ' ',
                        'Completion of Requirements Weight': ' ',
                        'Completion of Requirements Rating Scale': ' ',
                        'Completion of Requirements Final Score': ' ',
                        'Submission of ID Creation Target': ' ',
                        'Submission of ID Creation Weight': ' ',
                        'Submission of ID Creation Rating Scale': ' ',
                        'Submission of ID Creation Final Score': ' ',
                        'Bank Enrollment Target': ' ',
                        'Bank Enrollment Weight': ' ',
                        'Bank Enrollment Rating Scale': ' ',
                        'Bank Enrollment Final Score': ' ',
                        'Number Of Escalation Target': ' ',
                        'Number Of Escalation Weight': ' ',
                        'Number Of Escalation Rating Scale': ' ',
                        'Number Of Escalation Final Score': ' ',
                        'Inssuance of Employee Target': ' ',
                        'Inssuance of Employee Weight': ' ',
                        'Inssuance of Employee Rating Scale': ' ',
                        'Inssuance of Employee Final Score': ' ',
                        'Submission Of by-weekly Reports Processing Target': ' ',
                        'Submission Of by-weekly Reports Processing Weight': ' ',
                        'Submission Of by-weekly Reports Processing Rating Scale': ' ',
                        'Submission Of by-weekly Reports Processing Final Score': ' ',
                        'Total Score': ' ',
                        'Commendation   ': ' ',
                        'Action Plans': ' ',
                    },
                ]
            };
        },
    }
</script>
