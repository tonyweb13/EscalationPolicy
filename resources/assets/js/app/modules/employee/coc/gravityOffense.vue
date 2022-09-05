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
                columns: ['fields','gravity', 'description'],
                rows:  [],
                options: {
                    sortable: ['fields','gravity', 'description'],
                    filterable: ['fields','gravity', 'description']
                },
            }
        },
        created() {
            this.$constants.Coc_API.get("/gravity")
			.then(response => {
                this.rows = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        }
    }
</script>
