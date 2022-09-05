<template>
    <div class="pull-right" style="margin-bottom: 10px;">
        <json-excel
            class   = "btn btn-danger"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "VPS Site Location Format.csv"
        >
        <i class="fa fa-download"></i>
            Download VPS Site Location Format (CSV)
        </json-excel>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                dataForExcel: [
                    {
                        'employee_no': ' ',
                        'vps_office_location': ' ',
                    },
                ]
            };
        },
    }
</script>
