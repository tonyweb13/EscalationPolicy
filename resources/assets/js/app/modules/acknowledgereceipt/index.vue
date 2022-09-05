<template>
    <div>
        <loading-modal v-if="isLoading" />
		<the-ibox title="">
            <div class="pull-right" style="margin-top: -55px; margin-right: 240px;">
                <json-excel
                    class   = "btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    name    = "Acknowledgement Receipt Report.xls"
                    v-if="rule.acknowledge_receipt.child_rules.export.is_enable"
                >
                <i class="fa fa-download"></i>
                Export Acknowledgement Receipt
                </json-excel>
            </div>
            <button-add @add="addEditRow()" thingToAdd="Acknowledgement Receipt"
            v-if="rule.acknowledge_receipt.child_rules.add.is_enable"/>

			<v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                    <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)"
                    v-if="rule.acknowledge_receipt.child_rules.edit.is_enable">
                    <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                    <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)"
                    v-if="rule.acknowledge_receipt.child_rules.archive.is_enable">
                    <i class="fa fa-remove"></i> {{ labels.deleteBtn }}</button>
                </template>
                <template slot="acknowledgement_confirmation" slot-scope="props">
                    <div v-if="props.row.acknowledgement_confirmation == 1">
				        <div class="label label-success">YES</div>
                    </div>
                    <div v-else><div class="label label-default">NO</div></div>
				</template>
                <template slot="user_id" slot-scope="props">
				    {{ props.row.employee_user.first_name }} {{ props.row.employee_user.last_name }}
				</template>
                <template slot="supervisor_id" slot-scope="props">
				    <div v-if="props.row.supervisor_user">
                        {{ props.row.supervisor_user.first_name }} {{ props.row.supervisor_user.last_name }}
                    </div>
				</template>
                <template slot="manager_id" slot-scope="props">
                    <div v-if="props.row.manager_user">
				        {{ props.row.manager_user.first_name }} {{ props.row.manager_user.last_name }}
                    </div>
				</template>
                <template slot="item" slot-scope="props">
				    <div v-html="props.row.item"/>
				</template>
			</v-client-table>

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit :acknowledgementProps="acknowledge_data" slot="body" />
            </modal>
		</the-ibox>
    </div>
</template>
<script>
    import addEdit from '@/modules/acknowledgereceipt/addEdit.vue'

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
                            add: 'Add Acknowledgement Receipt',
                            edit: 'Edit Acknowledgement Receipt',
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
					headings: {
                        'user_id': 'Employee Name',
                        'supervisor_id': 'Supervisor',
                        'manager_id': 'Manager',
                        'acknowledgement_confirmation': 'Acknowledge',
                        'incentive': 'Name of the Incentive',
                        'item': 'Item/s',
                    },
                    sortable: [
                        'acknowledgement_confirmation',
                        'ticket_number',
                        'user_id',
                        'incentive',
                        'date_received'
                    ],
                    filterable: [
                        'ticket_number',
                        'user_id',
                        'supervisor_id',
                        'manager_id',
                        'incentive',
                        'prize',
                        'item',
                        'date_received'
                    ]
                },
                headerName: '',
                openModal: false,
                acknowledge_data: {
                    acknowledge_id: '',
                    user_id: '',
                    employee_name: '',
                    supervisor_id: '',
                    manager_id: '',
                    incentive: '',
                    prize: '',
                    item: '',
                    date_received: ''
                },
                json_fields: {
                    'Ticket Number' : {
                        field: 'ticket_number',
                        callback: (value) => {
                            return value ? `#${value}` : 'No Ticket Number';
                        }
                    },
                    'Acknowledge' : {
                        field: 'acknowledgement_confirmation',
                        callback: (value) => {
                            return value ? 'Yes' : 'No';
                        }
                    },
                    'Employee Name' : {
                        field: 'employee_user',
                        callback: (value) => {
                            return value.first_name + ' ' + value.last_name;
                        }
                    },
                    'Supervisor' : {
                        field: 'supervisor_user',
                        callback: (value) => {
                            return value ? value.first_name + ' ' + value.last_name: '';
                        }
                    },
                    'Manager' : {
                        field: 'manager_user',
                        callback: (value) => {
                            return value ? value.first_name + ' ' + value.last_name: '';
                        }
                    },
                    'Name of the Incentive': 'incentive',
                    'Prize': 'prize',
                    'Date Received': 'date_received',
                    'Item/s': 'item'
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
            }
        },
        created() {
            this.columns = (this.rule.acknowledge_receipt.child_rules.edit.is_enable
            || this.rule.acknowledge_receipt.child_rules.archive.is_enable) ?
                [
                    'actions',
                    'ticket_number',
                    'acknowledgement_confirmation',
                    'user_id',
                    'supervisor_id',
                    'manager_id',
                    'incentive',
					'prize',
                    'date_received',
					'item'
				] :
                [
                    'ticket_number',
                    'acknowledgement_confirmation',
                    'user_id',
                    'supervisor_id',
                    'manager_id',
                    'incentive',
					'prize',
                    'date_received',
					'item'
				];

            this.getList();
            this.$bus.$on('updateList', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Default_API.get("/acknowledgementreceipt")
                .then(response => {
                    this.rows = response.data;
                    this.json_data = this.rows;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventModalClose: function () {
                this.acknowledge_data = {
                    employee: '',
                    acknowledge_id: '',
                    user_id: '',
                    employee_name: '',
                    supervisor_id: '',
                    manager_id: '',
                    incentive: '',
                    prize: '',
                    item: '',
                    date_received: ''
                };
            },
            addEditRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = this.labels.edit;
                    this.acknowledge_data.acknowledge_id = props_row.id;
                    this.acknowledge_data.user_id = props_row.user_id;
                    this.acknowledge_data.employee_name = props_row.employee_user.first_name + " " + props_row.employee_user.last_name;
                    this.acknowledge_data.supervisor_id = props_row.supervisor_id;
                    this.acknowledge_data.manager_id = props_row.manager_id;
                    this.acknowledge_data.incentive = props_row.incentive;
                    this.acknowledge_data.prize = props_row.prize;
                    this.acknowledge_data.item = props_row.item;
                    this.acknowledge_data.date_received = props_row.date_received;

                } else {
                    this.headerName = this.labels.add;
                    this.eventModalClose();
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete acknowledgement receipt of '
                    + props_row.employee_user.first_name + " " + props_row.employee_user.last_name +'?',
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
                            this.$constants.Default_API.delete("/acknowledgementreceipt/" + props_row.id)
                                .then(response => {
                                    response.data
                                    this.getList();
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                            this.$success(props_row.employee_user.first_name + " " + props_row.employee_user.last_name + ' has been deleted!')
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure(props_row.employee_user.first_name + " " + props_row.employee_user.last_name + ' has been cancelled!')
                        }
                    })
            },
        }
    }
</script>
