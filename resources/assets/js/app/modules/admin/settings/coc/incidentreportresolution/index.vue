<template>
    <div>
    <loading-modal v-if="isLoading" />
    <the-ibox title="List of Case Closure">
        <button-add @add="addEditRow()" thingToAdd="Case Closure"
        v-if="rule.case_closure.child_rules.add.is_enable" />
        <v-client-table :data="rows" :columns="columns" :options="options">
            <template slot="actions" slot-scope="props">
                <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)"
                v-if="rule.case_closure.child_rules.edit.is_enable">
                <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)"
                v-if="rule.case_closure.child_rules.delete.is_enable">
                <i class="fa fa-remove"></i> {{ labels.deleteBtn }}</button>
            </template>
        </v-client-table>

        <modal v-if="openModal" @close="openModal = false">
            <h3 slot="header">{{ headerName }}</h3>
            <add-edit :correctiveProps="corrective_data" slot="body" />
        </modal>
    </the-ibox>
    </div>
</template>
<script>
    import addEdit from '@/modules/admin/settings/coc/incidentreportresolution/addEdit.vue'

    export default {
        components: {
            addEdit
        },

        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Add Case Closure',
                            edit: 'Edit Case Closure',
                            editBtn: 'Edit',
                            deleteBtn: 'Archive'
                        }
                    }
                },

            },

        data: function () {
            return {
                rule: this.$ep.rule,
                isLoading: false,
                columns: this.columns,
                rows:  [],
                options: {
                    sortable: ['name'],
                    filterable: ['name']
                },
                headerName: '',
                openModal: false,
                corrective_data: {
                    cor_corrective: '',
                    cor_id: '',
                },
            }
        },

        mounted() {
            this.$emit("update", this.updatePagination);
        },

        created(){
            this.columns = (this.rule.case_closure.child_rules.edit.is_enable
            || this.rule.case_closure.child_rules.delete.is_enable) ?
               ['actions', 'name'] : ['name'];

            this.getList();
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },

        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Coc_API.get("/incidentreportresolution")
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },

            addEditRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = this.labels.edit;
                    this.corrective_data.cor_corrective = props_row.name;
                    this.corrective_data.cor_id = props_row.id;
                } else {
                    this.headerName = this.labels.add;
                    this.corrective_data.cor_corrective = '';
                    this.corrective_data.cor_id = '';
                }
            },

            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete ' + props_row.name + ' ?',
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
                            this.$constants.Coc_API.delete("/incidentreportresolution/" + props_row.id)
                                .then(response => {
                                    response.data
                                    this.getList()
                                    this.$success(props_row.name + ' has been deleted!')
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure(props_row.name + ' has been cancelled')
                        }
                    })
            },
        }
    }
</script>
