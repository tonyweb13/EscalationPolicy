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
                        'Evaluation Period': ' ',
                        'Name Of Evaluator': ' ',
                        'Self Thinking Target': ' ',
                        'Self Thinking Weight': ' ',
                        'Self Thinking Rating Scale': ' ',
                        'Self Thinking Final Score': ' ',
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
                        'Data Analysis Target': ' ',
                        'Data Analysis Weight': ' ',
                        'Data Analysis Rating Scale': ' ',
                        'Data Analysis Final Score': ' ',
                        'HR Intervention Target': ' ',
                        'HR Intervention Weight': ' ',
                        'HR Intervention Rating Scale': ' ',
                        'HR Intervention Final Score': ' ',
                        'Cluster Satisfaction Target': ' ',
                        'Cluster Satisfaction Weight': ' ',
                        'Cluster Satisfaction Rating Scale': ' ',
                        'Cluster Satisfaction Final Score': ' ',
                        'Productivity Target': ' ',
                        'Productivity Weight': ' ',
                        'Productivity Rating Scale': ' ',
                        'Productivity Final Score': ' ',
                        'Total Score': ' ',
                        'Commendation   ': ' ',
                        'Action Plans': ' ',
                    },
                ]
            };
        },
    }
</script>
