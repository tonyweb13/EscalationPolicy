<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div v-if="categoriesProps">
            <the-ibox :title="categoryTitle">
                <v-client-table :data="rows" :columns="columns" :options="options">
                    <template slot="offense" slot-scope="props">
                       <div style="width: 150px;" v-html="props.row.offense"/>
                    </template>
                    <template slot="gravity" slot-scope="props">
                       <div style="width: 80px;">{{ props.row.gravity.gravity }}</div>
                    </template>
                    <template slot="prescriptive" slot-scope="props">
                       <div style="width: 100px;">{{ props.row.gravity.prescriptive_period }}</div>
                    </template>
                    <template slot="description" slot-scope="props">
                       <div v-html="props.row.description"/>
                    </template>
                </v-client-table>
            </the-ibox>
        </div>
    </div>
</template>
<script>

    export default {
        props: {
            categoriesProps: Number,
        },
        data: function () {
            return {
                isLoading: false,
                columns: ['offense', 'gravity', 'prescriptive', 'description'],
                rows:  [],
                options: {
                    headings: {
                        'prescriptive': 'Prescriptive Period',
                    },
                    sortable: ['offense', 'gravity', 'prescriptive', 'description'],
                    filterable: ['offense', 'gravity', 'prescriptive', 'description']
                },
                categoryTitle: '',
            }
        },
        mounted() {
            this.$emit("update", this.updatePagination);
        },
        created(){
            this.getList();
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Coc_API.get("/offense/category/"+this.categoriesProps)
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                        if(this.rows[1].category.name){
                            this.categoryTitle = "List of Offenses for "+this.rows[1].category.name;
                        }
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
        }
    }
</script>
