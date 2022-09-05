<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div >
            <div class="pull-left">
                <h4>Levels of Gravity List</h4>
            </div>
            <div class="pull-right" >
                <json-excel
                    class   = "btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    name    = "gravity offense.xls"
                    v-if="rule.levels_of_gravity.child_rules.export.is_enable"
                >
                <i class="fa fa-download"></i>
                Export Gravity Offense
                </json-excel>
            </div>
            <button-add @add="addEditRow()" thingToAdd="Gravity"
                v-if="rule.levels_of_gravity.child_rules.add.is_enable"
                style="text-align:right;margin-top: 0px; margin-right: 2px;"/>
                <br />
            <hr style="width:100%"></hr/>
            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="gravity" slot-scope="props">
                    <div style="width: 90px;">{{ props.row.gravity.gravity }}</div>
                </template>
                <template slot="actions" slot-scope="props">
                    <div style="width: 120px;">
                        <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)"
                        v-if="rule.levels_of_gravity.child_rules.edit.is_enable">
                            <i class="fa fa-pencil"/> {{ labels.editBtn }}
                        </button>
                        <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)"
                        v-if="rule.levels_of_gravity.child_rules.archive.is_enable">
                            <i class="fa fa-remove"/> {{ labels.deleteBtn }}
                        </button>
                    </div>
                </template>
            </v-client-table>

            <modal v-if="openModal" @close="openModal = false">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit :gravityProps="gravity_data" slot="body" />
            </modal>
        </div>
    </div>
</template>
<script>
    import addEdit from '@/modules/admin/settings/coc/gravity/addEdit.vue'

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
                            add: 'Add Gravity Offense',
                            edit: 'Edit Gravity Offense',
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
                    sortable: ['fields','gravity'],
                    filterable: ['fields','gravity', 'description']
                },
                headerName: '',
                openModal: false,
                gravity_data: {
                    grav_fields: '',
                    grav_desc: '',
                    grav_gravity: '',
                    grav_id: '',
                },
                json_fields: {
                    'Fields': 'fields',
                    'Gravity': 'gravity.gravity',
                    'Prescriptive': 'gravity.prescriptive_period',
                    'IR Description': 'description',
                },
                json_data: [],
                json_meta: [
                    [
                        {
                            'key': 'charset',
                            'value': 'utf-8'
                        }
                    ]
                ],
                designation: this.$ep.user.designation,
            }
        },
        created(){
            this.$bus.$on('levelsOfGravity', this.getList);
            if (this.$route.hash == '#levels_of_gravity') {
                this.getList();
            }
            this.columns = (this.rule.levels_of_gravity.child_rules.edit.is_enable
            || this.rule.levels_of_gravity.child_rules.archive.is_enable) ?
                ['actions','fields','gravity', 'description'] :
                ['fields','gravity', 'description'];

            this.getList();
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Coc_API.get("/gravity")
                    .then(response => {
                        this.rows = response.data;
                        this.json_data = this.rows;
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
                    this.gravity_data.grav_fields = props_row.fields;
                    this.gravity_data.grav_desc = props_row.description;
                    this.gravity_data.grav_gravity = props_row.gravity.gravity;
                    this.gravity_data.grav_id = props_row.id;
                } else {
                    this.headerName = this.labels.add;
                    this.gravity_data.grav_fields = '';
                    this.gravity_data.grav_desc = '';
                    this.gravity_data.grav_gravity = '';
                    this.gravity_data.grav_id = '';
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete ' + props_row.fields + ' ?',
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
                            this.$constants.Coc_API.delete("/gravity/" + props_row.id)
                                .then(response => {
                                    response.data
                                    this.getList()
                                    this.$success(props_row.fields + ' has been deleted!')
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure(props_row.fields + ' has been cancelled!')
                        }
                    })
            },
        }
    }
</script>
