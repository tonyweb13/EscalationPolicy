<template>
    <div >
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "C&B.csv"
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
                        'Payroll File Target': ' ',
                        'Payroll File Weight': ' ',
                        'Payroll File Rating Scale': ' ',
                        'Payroll File Final Score': ' ',
                        'Timekeeping Target': ' ',
                        'Timekeeping Weight': ' ',
                        'Timekeeping Rating Scale': ' ',
                        'Timekeeping Final Score': ' ',
                        'Processing of Benefits Target': ' ',
                        'Processing of Benefits Weight': ' ',
                        'Processing of Benefits Rating Scale': ' ',
                        'Processing of Benefits Final Score': ' ',
                        'HMO Enrollment Target': ' ',
                        'HMO Enrollment Weight': ' ',
                        'HMO Enrollment Rating Scale': ' ',
                        'HMO Enrollment Final Score': ' ',
                        'Inssuance of Employee Target': ' ',
                        'Inssuance of Employee Weight': ' ',
                        'Inssuance of Employee Rating Scale': ' ',
                        'Inssuance of Employee Final Score': ' ',
                        'Final Pay Processing Target': ' ',
                        'Final Pay Processing Weight': ' ',
                        'Final Pay Processing Rating Scale': ' ',
                        'Final Pay Processing Final Score': ' ',
                        'Total Score': ' ',
                        'Commendation   ': ' ',
                        'Action Plans': ' ',
                    },
                ]
            };
        },
    }
</script>
