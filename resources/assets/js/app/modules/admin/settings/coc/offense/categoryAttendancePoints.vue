<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div v-if="categoriesProps">
            <div class="pull-left" style="">
                <h4>Attendance With Points</h4>
            </div>
            <button-add
                @add="addEditAttendanceRow()"
                thingToAdd="Attendance"
                style="margin-top:0px !important; margin-left:10px; margin-bottom: 5px"
                v-if="attendance_data.category_rule.add.is_enable"
            />
            <div class="pull-right" style="margin-top:0px !important">
                <a
                    href="/api/admin/settings/coc/download"
                    v-if="attendance_data.category_rule.export.is_enable"
                >
                    <button type="button" class="btn btn-success">
                        <i class="fa fa-download"></i> Export
                    </button>
                </a>
            </div>
            <hr style="width:100%"></hr/>
                <!-- <div class="pull-right" style="margin-top: -55px; margin-right: 130px;">
                    <json-excel
                        class   = "btn btn-success"
                        :data   = "json_data"
                        :fields = "json_fields"
                        name    = "coc.xls"
                    >
                    <i class="fa fa-download"></i>
                    Export Code of Conduct
                    </json-excel>
                </div> -->


                <v-client-table :data="rows" :columns="columns" :options="options">
                        <template slot="actions" slot-scope="props">
                            <div style="width: 120px;">
                                <button class='btn btn-primary btn-xs' @click="addEditAttendanceRow(props.row)"
                                v-if="attendance_data.category_rule.edit.is_enable">
                                    <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                                <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)"
                                v-if="attendance_data.category_rule.archive.is_enable">
                                    <i class="fa fa-remove"></i> {{ labels.deleteBtn }}
                                    </button>
                            </div>
                        </template>
                        <template slot="type_infraction" slot-scope="props">
                           <div v-html="props.row.type_infraction"/>
                        </template>
                        <template slot="attendancepoint" slot-scope="props">
                           <div style="width: 80px;">{{ props.row.attendancepoint.attendance_points }}</div>
                        </template>
                        <template slot="definition" slot-scope="props">
                           <div v-html="props.row.definition"/>
                        </template>
                </v-client-table>

            <modal v-if="openModal" @close="openModal = false">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit-attendance :attendanceProps="attendance_data" slot="body" />
            </modal>
        </div>
    </div>
</template>
<script>
    import addEditAttendance from '@/modules/admin/settings/coc/offense/addEditAttendance.vue'

    export default {
        components: {
            addEditAttendance
        },
        props: {
            categoriesProps: Object,
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Add Attendance Points System',
                            edit: 'Edit Attendance Points System',
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
                        'attendancepoint': 'Attendance Point'
                    },
                    sortable: ['type_infraction', 'attendancepoint', 'definition'],
                    filterable: ['type_infraction', 'attendancepoint', 'definition']
                },
                headerName: '',
                openModal: false,
                attendance_data: {
                    att_id: '',
                    att_infraction: '',
                    att_category: '',
                    att_attendancepoint: '',
                    att_definition: '',
                },
                categoryTitle: '',
            }
        },
        mounted() {
            this.$emit("update", this.updatePagination);
        },
        created(){
            this.attendance_data.category_rule = this.categoriesProps.category_rule;
            this.columns = (this.attendance_data.category_rule.edit.is_enable || this.attendance_data.category_rule.archive.is_enable) ?
                ['actions', 'type_infraction', 'attendancepoint', 'definition'] :
                ['type_infraction', 'attendancepoint', 'definition'];
            this.attendance_data.category_id = this.categoriesProps.category_id;
            if (this.$route.hash == '#attendance-with-points' || this.$route.hash === "") {
                this.getList();
            }
            this.$bus.$on('categoryAttendance', this.getList);
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            getList: function() {
                this.isLoading = true

                this.$constants.Coc_API.get("/attendance/points/system")
                    .then(response => {
                        this.rows = response.data;
                        // this.json_data = this.rows;
                        this.isLoading = false
                        this.categoryTitle = "List of Attendance Point System";
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            addEditAttendanceRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = this.labels.edit;
                    this.attendance_data.att_id = props_row.id;
                    this.attendance_data.att_infraction = props_row.type_infraction;
                    this.attendance_data.att_category = props_row.category.name;
                    this.attendance_data.att_attendancepoint = props_row.attendancepoint.attendance_points;
                    this.attendance_data.att_definition = props_row.definition;
                } else {
                    this.headerName = this.labels.add;
                    this.attendance_data.att_id = '';
                    this.attendance_data.att_infraction = '';
                    this.attendance_data.att_category = '';
                    this.attendance_data.att_attendancepoint = '';
                    this.attendance_data.att_definition = '';
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete this Type Infraction: ' + props_row.type_infraction + ' ?',
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
                            this.$constants.Coc_API.delete("/attendance/points/system/" + props_row.id)
                                .then(response => {
                                    response.data;
                                    this.getList();
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                            this.$success('Type Infraction: ' + props_row.type_infraction+ ' has been deleted!')
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Type Infraction: ' + props_row.type_infraction +' has been cancelled')
                        }
                    })
            },
        }
    }
</script>
