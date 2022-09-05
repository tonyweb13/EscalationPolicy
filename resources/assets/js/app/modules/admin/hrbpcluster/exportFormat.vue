<template>
    <div class="pull-right" style="margin-top: -55px; margin-right: 320px;">
        <json-excel
            class   = "btn btn-default"
            :data   = "dataForExcel"
            type    = "csv"
            name    = "Export HRBP Format.csv"
        >
        <i class="fa fa-download"></i>
            Export CSV Format
        </json-excel>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                dataForExcel: [
                    {
                        'HRSL Employee No': ' ',
                        'HRSL Email Address': ' ',
                        'HRSL Location': ' ',
                        'HRBP Employee No': ' ',
                        'HRBP Email Address': ' ',
                        'User Employee No': ' ',
                        'Supervisor Employee No': ' ',
                        'Manager Employee No': ' ',
                    },
                ]
            };
        },
    }
</script>
