<template>
    <div>
    <loading-modal v-if="isLoading" />
    <the-ibox title="List of Case Closure">
        <v-client-table :data="rows" :columns="columns" :options="options"/>
    </the-ibox>
    </div>
</template>
<script>
    export default {

        data: function () {
            return {
                isLoading: false,
                columns: ['name'],
                rows:  [],
                options: {
                    sortable: ['name'],
                    filterable: ['name']
                },
                openModal: false,
            }
        },

        created(){
            this.getList();
        },

        methods:{
            getList: function() {
                this.isLoading = true;
                this.$constants.Coc_API.get("/incidentreportresolution")
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
        }
    }
</script>
