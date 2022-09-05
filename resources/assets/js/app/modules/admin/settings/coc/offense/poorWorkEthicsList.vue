<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div v-if="categoriesProps">
            <div class="pull-left" style="">
                <h4>Poor Work Ethics</h4>
            </div>
            <button-add
                @add="addEditRow()"
                thingToAdd="Offense"
                style="margin-top:0px !important; margin-left:10px; margin-bottom: 5px"
                v-if="offense_data.category_rule.add.is_enable"
            />
            <hr style="width:100%"/>
                <v-client-table :data="rows" :columns="columns" :options="options">
                        <template slot="actions" slot-scope="props" >
                            <div>
                                <button
                                    class='btn btn-primary btn-xs'
                                    @click="addEditRow(props.row)"
                                    v-if="offense_data.category_rule.edit.is_enable"
                                >
                                    <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                                <button
                                    class='btn btn-danger btn-xs'
                                    @click="deleteRow(props.row)"
                                    v-if="offense_data.category_rule.archive.is_enable"
                                >
                                    <i class="fa fa-remove"></i> {{ labels.deleteBtn }}
                                </button>
                            </div>
                        </template>
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

                        <template slot="first_instance" slot-scope="props">
                            <div style="width: auto;" v-html="(props.row.instance && props.row.instance.first_instance) ? props.row.instance.first_instance.name : ''"/>
                        </template>
                        <template slot="second_instance" slot-scope="props">
                           <div style="width: auto;" v-html="(props.row.instance && props.row.instance.second_instance) ? props.row.instance.second_instance.name : ''"/>
                        </template>
                        <template slot="third_instance" slot-scope="props">
                            <div style="width: auto;" v-html="(props.row.instance && props.row.instance.third_instance) ? props.row.instance.third_instance.name : ''"/>
                        </template>
                        <template slot="fourth_instance" slot-scope="props">
                            <div style="width: auto;" v-html="(props.row.instance && props.row.instance.fourth_instance) ? props.row.instance.fourth_instance.name : ''"/>
                        </template>
                        <template slot="fifth_instance" slot-scope="props">
                            <div style="width: auto;" v-html="(props.row.instance && props.row.instance.fifth_instance) ? props.row.instance.fifth_instance.name : ''"/>
                        </template>
                        <template slot="sixth_instance" slot-scope="props">
                            <div style="width: auto;" v-html="(props.row.instance && props.row.instance.sixth_instance) ? props.row.instance.sixth_instance.name : ''"/>
                        </template>
                        <template slot="seventh_instance" slot-scope="props">
                            <div style="width: auto;" v-html="(props.row.instance && props.row.instance.seventh_instance) ? props.row.instance.seventh_instance.name : ''"/>
                        </template>
                </v-client-table>

            <modal v-if="openModal" @close="openModal = false">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit :offenseProps="offense_data" slot="body" />
            </modal>
        </div>
    </div>
</template>
<script>
    import addEdit from '@/modules/admin/settings/coc/offense/addEdit.vue'

    export default {
        components: {
            addEdit
        },
        props: {
            categoriesProps: Object,
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Add Offense',
                            edit: 'Edit Offense',
                            editBtn: 'Edit',
                            deleteBtn: 'Archive'
                    }
                }
            },
        },
        data: function () {
            return {
                isLoading: false,
                columns: this.columns,
                rows:  [],
                options: {
                    headings: {
                        'prescriptive': 'Prescriptive Period',
                    },
                    sortable: ['offense', 'gravity', 'prescriptive', 'description'],
                    filterable: ['offense', 'gravity', 'prescriptive', 'description']
                },
                headerName: '',
                openModal: false,
                offense_data: {
                    off_description: '',
                    off_offense: '',
                    off_gravity: '',
                    off_id: '',
					category_id: '',
					category_name: '',
                    first_instance: '',
                    second_instance: '',
                    third_instance: '',
                    fourth_instance: '',
                    fifth_instance: '',
                    sixth_instance: '',
                    seventh_instance: '',
                },
                categoryTitle: '',
                categoryName: '',
                categoryID: '',
            }
        },
        mounted() {
            this.$emit("update", this.updatePagination);
        },
        created(){
            this.offense_data.category_rule = this.categoriesProps.category_rule;
            this.offense_data.category_id = this.categoriesProps.category_id;
            if (this.$route.hash == '#poor-work-ethics') {
                this.getList();
            }
            this.$bus.$on('categoryList7', this.getList);
            this.columns = (this.offense_data.category_rule.edit.is_enable || this.offense_data.category_rule.archive.is_enable) ?
                [
                    'actions',
                    'offense',
                    'gravity',
                    'prescriptive',
                    'description',
                    'first_instance',
                    'second_instance',
                    'third_instance',
                    'fourth_instance',
                    'fifth_instance',
                    'sixth_instance',
                    'seventh_instance'
                ] :
                [
                    'offense',
                    'gravity',
                    'prescriptive',
                    'description',
                    'first_instance',
                    'second_instance',
                    'third_instance',
                    'fourth_instance',
                    'fifth_instance',
                    'sixth_instance',
                    'seventh_instance'
                ];
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Coc_API.get("/offense/category/"+this.offense_data.category_id)
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                        if(this.rows[1].category.name){
                            this.categoryTitle = "List of Offenses for "+this.rows[1].category.name;
                            this.categoryName = this.rows[1].category.name;
                            this.categoryID = this.rows[1].category.id;
                        }

                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            addEditRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = this.labels.edit;
                    this.offense_data.off_id = props_row.id;
                    this.offense_data.category_name = this.categoryName;
                    this.offense_data.category_id = this.categoryID;
                    this.offense_data.off_description = props_row.description;
                    this.offense_data.off_offense = props_row.offense;
                    this.offense_data.off_gravity = props_row.gravity.gravity + " - " +
                                                    props_row.gravity.prescriptive_period;
                    if (props_row.instance) {
                        this.offense_data.first_instance = props_row.instance.first_instance_coc_id
                        this.offense_data.second_instance = props_row.instance.second_instance_coc_id
                        this.offense_data.third_instance = props_row.instance.third_instance_coc_id
                        this.offense_data.fourth_instance = props_row.instance.fourth_instance_coc_id
                        this.offense_data.fifth_instance = props_row.instance.fifth_instance_coc_id
                        this.offense_data.sixth_instance = props_row.instance.sixth_instance_coc_id
                        this.offense_data.seventh_instance = props_row.instance.seventh_instance_coc_id
                    } else {
                        this.offense_data.first_instance = null
                        this.offense_data.second_instance = ''
                        this.offense_data.third_instance = ''
                        this.offense_data.fourth_instance = ''
                        this.offense_data.fifth_instance = ''
                        this.offense_data.sixth_instance = ''
                        this.offense_data.seventh_instance = ''
                    }
                } else {
                    this.headerName = this.labels.add;
                    this.offense_data.category_name = this.categoryName;
                    this.offense_data.category_id = this.categoryID;
                    this.offense_data.off_description = '';
                    this.offense_data.off_offense = '';
                    this.offense_data.off_id = '';
                    this.offense_data.off_gravity = '';
                    this.offense_data.prescriptive_period = '';
                    this.offense_data.first_instance = null
                    this.offense_data.second_instance = ''
                    this.offense_data.third_instance = ''
                    this.offense_data.fourth_instance = ''
                    this.offense_data.fifth_instance = ''
                    this.offense_data.sixth_instance = ''
                    this.offense_data.seventh_instance = ''
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete this offense: ' + props_row.offense + ' ?',
                    text: "Once deleted, you will not be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            this.$constants.Coc_API.delete("/offense/" + props_row.id)
                                .then(response => {
                                    response.data;
                                    this.getList();
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                            this.$success('Offense: ' + props_row.offense + ' has been deleted!')
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Offense: ' + props_row.offense +' has been cancelled')
                        }
                    })
            },
        }
    }
</script>
