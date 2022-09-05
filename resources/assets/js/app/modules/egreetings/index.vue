<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">
            <button-add @add="addEditRow()" thingToAdd="E-Greetings"
            v-if="designation.indexOf('Engagement') > 0"/>
            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                    <button class='btn btn-warning btn-xs' @click="viewRow(props.row)">
                    <i class="fa fa-eye"></i> {{ labels.viewBtn }}</button>
                    <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)"
                    v-if="designation.indexOf('Engagement') > 0">
                    <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                    <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)"
                    v-if="designation.indexOf('Engagement') > 0">
                    <i class="fa fa-remove"></i> {{ labels.deleteBtn }}</button>
                </template>
                <template slot="is_sent" slot-scope="props">
                    <div v-if="props.row.is_sent == 1">
                       <div class="label label-success">YES</div>
                    </div>
                    <div v-else><div class="label label-default">NO</div></div>
                </template>
            </v-client-table>
            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <view-greetings v-if="openAction == 'view'" :eGreetingsProps="egreetings_data" slot="body" />
                <add-edit v-else :eGreetingsProps="egreetings_data" slot="body" />
            </modal>
        </the-ibox>
    </div>
</template>
<script>
    import addEdit from '@/modules/egreetings/addEdit.vue'
    import viewGreetings from '@/modules/egreetings/view.vue'

    export default {
        components: {
            addEdit,
            viewGreetings
        },
        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Add E-Greetings',
                            edit: 'Edit E-Greetings',
                            view: 'View E-Greetings',
                            viewBtn: 'View',
                            editBtn: 'Edit',
                            deleteBtn: 'Archive'
                        }
                    }
                },

            },
        data: function () {
            return {
                designation: this.$ep.user.designation.name,
                isLoading: false,
                columns: [
                    'actions',
                    'employee_number',
                    'employee_name',
                    'email_address',
                    'birthday',
                    'is_sent',
                ],
                rows:  [],
                options: {
                    sortable: [
                        'employee_number',
                        'employee_name',
                        'email_address',
                        'birthday',
                        'is_sent'
                    ],
                    filterable: [
                        'employee_number',
                        'employee_name',
                        'email_address',
                        'birthday',
                        'is_sent'
                    ]
                },
                headerName: '',
                openAction: '',
                openModal: false,
                egreetings_data: {
                    greeting_id: '',
                    template1: '',
                    template2: '',
                    // profile_pic: '',
                    employee_number: '',
                    employee_name: '',
                    email_address: '',
                    birthday: '',
                    created_at: ''
                },
            }
        },
        created() {
            this.getList();
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Default_API.get("/egreetings")
                .then(response => {
                    this.rows = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventModalClose: function () {
                window.location.href = '/egreetings'
            },
            viewRow: function (props_row) {
                this.openModal = true;
                this.openAction = 'view';
                this.egreetings_data.greeting_id = props_row.id;
                this.egreetings_data.template1 = props_row.template1;
                this.egreetings_data.template2 = props_row.template2;
                // this.egreetings_data.profile_pic = props_row.profile_pic;
                this.egreetings_data.employee_number = props_row.employee_number;
                this.egreetings_data.employee_name = props_row.employee_name;
                this.egreetings_data.email_address = props_row.email_address;
                this.egreetings_data.birthday = props_row.birthday;
                this.egreetings_data.created_at = props_row.created_at;
            },
            addEditRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = this.labels.edit + " - " + props_row.employee_name;
                    this.egreetings_data.greeting_id = props_row.id;
                    this.egreetings_data.employee_number = props_row.employee_number;
                    this.egreetings_data.employee_name = props_row.employee_name;
                    this.egreetings_data.email_address = props_row.email_address;
                    this.egreetings_data.birthday = props_row.birthday;
                } else {
                    this.headerName = this.labels.add;
                    this.egreetings_data.template1 = '';
                    this.egreetings_data.template2 = '';
                    // this.egreetings_data.profile_pic = '';
                    this.egreetings_data.employee_number = '';
                    this.egreetings_data.employee_name = '';
                    this.egreetings_data.email_address = '';
                    this.egreetings_data.birthday = '';
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            this.$constants.Default_API.delete("/egreetings/" + props_row.id)
                                .then(response => {
                                    response.data
                                    this.getList()
                                    this.$success(props_row.employee_name + ' has been deleted!')
                                });

                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure(props_row.employee_name + ' has been cancelled!')
                        }
                    })
            },
        }
    }
</script>
