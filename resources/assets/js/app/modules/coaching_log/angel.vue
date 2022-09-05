<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Angel.csv"
        >
        <i class="fa fa-download"></i>
            Export Angel CSV Format
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
                        'Timely And Accuraccy of On-Boarding Target': ' ',
                        'Timely And Accuraccy of On-Boarding Weight': ' ',
                        'Timely And Accuraccy of On-Boarding Rating Scale': ' ',
                        'Timely And Accuraccy of On-Boarding Final Score': ' ',
                        'Timely and Accuracy of Updating in Maserfile Target': ' ',
                        'Timely and Accuracy of Updating in Maserfile Weight': ' ',
                        'Timely and Accuracy of Updating in Maserfile Rating Scale': ' ',
                        'Timely and Accuracy of Updating in Maserfile Final Score': ' ',
                        'Submission of weekly reports Target': ' ',
                        'Submission of weekly reports Weight': ' ',
                        'Submission of weekly reports Rating Scale': ' ',
                        'Submission of weekly reports Final Score': ' ',
                        'Final Pay Concerns Target': ' ',
                        'Final Pay Concerns Weight': ' ',
                        'Final Pay Concerns Rating Scale': ' ',
                        'Final Pay Concerns Final Score': ' ',
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
