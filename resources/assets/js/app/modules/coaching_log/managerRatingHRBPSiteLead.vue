<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Manager Rating HRBP Site Lead.csv"
        >
        <i class="fa fa-download"></i>
            Export Manager Rating HRBP Site Lead CSV Format
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
                        'Work Ethics Target': ' ',
                        'Work Ethics Weight': ' ',
                        'Work Ethics Rating Scale': ' ',
                        'Work Ethics Final Score': ' ',
                        'Leadership Target': ' ',
                        'Leadership Weight': ' ',
                        'Leadership Rating Scale': ' ',
                        'Leadership Final Score': ' ',
                        'Attendance Reliability Target': ' ',
                        'Attendance Reliability Weight': ' ',
                        'Attendance Reliability Rating Scale': ' ',
                        'Attendance Reliability Final Score': ' ',
                        'Monthly Data Analysis Target': ' ',
                        'Monthly Data Analysis Weight': ' ',
                        'Monthly Data Analysis Rating Scale': ' ',
                        'Monthly Data Analysis Final Score': ' ',
                        'Monthly Site Satisfaction Ration Target': ' ',
                        'Monthly Site Satisfaction Ration Weight': ' ',
                        'Monthly Site Satisfaction Ration Rating Scale': ' ',
                        'Monthly Site Satisfaction Ration Final Score': ' ',
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
