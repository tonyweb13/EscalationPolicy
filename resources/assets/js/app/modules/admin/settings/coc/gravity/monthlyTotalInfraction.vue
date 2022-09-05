<template>
    <div style="margin-top:10px">
		<v-client-table :data="rows" :columns="columns" :options="options" >
            <template slot="gravity" slot-scope="props">
                <div style="width: 90px;">{{ props.row.gravity.gravity }}</div>
            </template>
        </v-client-table>
    </div>
</template>
<script>

    export default {
		data: function () {
            return {
                columns: ['employee_no', 'last_name', 'first_name', 'grave', 'major', 'minor', 'serious', 'grand_total'],
                rows: [],
                options: {
                    sortable: ['employee_no', 'last_name', 'first_name', 'grave', 'major', 'minor', 'serious', 'grand_total'],
                    filterable: ['employee_no', 'last_name', 'first_name', 'grave', 'major', 'minor', 'serious', 'grand_total']
                },
            }
        },

        created() {
            if (this.$route.hash == '#monthly-total-infraction') {
                this.getList();
            }
            this.$bus.$on('monthlyTableClick', this.getList);
        },

        methods: {
            getList() {
                this.$constants.Coc_API.get("/monthly_total_infraction")
    			.then(response => {
                    this.rows = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        }
    }
</script>
