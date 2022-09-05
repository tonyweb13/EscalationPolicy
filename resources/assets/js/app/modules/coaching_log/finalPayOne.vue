<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Final Pay.csv"
        >
        <i class="fa fa-download"></i>
            Export Final Pay CSV Format
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
                        'Accuraccy of Computation of Final Pay Target': ' ',
                        'Accuraccy of Computation of Final Pay Weight': ' ',
                        'Accuraccy of Computation of Final Pay Rating Scale': ' ',
                        'Accuraccy of Computation of Final Pay Final Score': ' ',
                        'Timely Processing of Final Pay (bi-weekly) Submission Target': ' ',
                        'Timely Processing of Final Pay (bi-weekly) Submission Weight': ' ',
                        'Timely Processing of Final Pay (bi-weekly) Submission Rating Scale': ' ',
                        'Timely Processing of Final Pay (bi-weekly) Submission Final Score': ' ',
                        'Number of Escalations Target': ' ',
                        'Number of Escalations Weight': ' ',
                        'Number of Escalations Rating Scale': ' ',
                        'Number of Escalations Final Score': ' ',
                        'Total Score': ' ',
                        'Commendation   ': ' ',
                        'Action Plans': ' ',
                    },
                ]
            };
        },
    }
</script>
