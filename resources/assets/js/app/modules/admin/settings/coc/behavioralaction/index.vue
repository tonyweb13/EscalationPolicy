<template>
    <div>
        <loading-modal v-if="isLoading" />
		<the-ibox title="">
            <div class="pull-right" style="margin-top: -55px; margin-right: 200px;">
                <json-excel
                    class   = "btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    name    = "gravity occurrence.xls"
                    v-if="rule.behavioral_action.child_rules.export.is_enable"
                >
                <i class="fa fa-download"></i>
                Export Gravity Occurrence
                </json-excel>
            </div>
            <button-add @add="addEditRow()" thingToAdd="Gravity Occurrence"
            v-if="rule.behavioral_action.child_rules.add.is_enable"/>
			<v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                        <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)"
                        v-if="rule.behavioral_action.child_rules.edit.is_enable">
                        <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                        <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)"
                        v-if="rule.behavioral_action.child_rules.archive.is_enable">
                        <i class="fa fa-remove"></i> {{ labels.deleteBtn }}</button>
                </template>
			</v-client-table>

            <behavioral-management-flowchart/>

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit :gravityOccurrenceProps="gravity_data" slot="body" />
            </modal>
		</the-ibox>
    </div>
</template>
<script>
    import addEdit from '@/modules/admin/settings/coc/behavioralaction/addEdit.vue'

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
                            add: 'Add Gravity Occurrence',
                            edit: 'Edit Gravity Occurrence',
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
						'gravity_offense.gravity': 'Gravity',
						'first_occur.name': '1st Occurrence',
						'second_occur.name': '2nd  Occurrence',
						'third_occur.name': '3rd  Occurrence',
						'fourth_occur.name': '4th  Occurrence',
						'fifth_occur.name': '5th  Occurrence',
						'sixth_occur.name': '6th  Occurrence',
						'seventh_occur.name': '7th Occurrence'
                    },
                    sortable: [],
                    filterable: []
                },
                headerName: '',
                openModal: false,
                gravity_data: {
                    occur_id: '',
                    occur_gravity: '',
                    occur_first_irr: '',
                    occur_second_irr: '',
                    occur_third_irr: '',
                    occur_fourth_irr: '',
                    occur_fifth_irr: '',
                    occur_sixth_irr: '',
                    occur_seventh_irr: '',
                },
                json_fields: {
                    'Gravity': 'gravity_offense.gravity',
                    '1st Occurrence': 'first_occur.name',
                    '2nd Occurrence': 'second_occur.name',
                    '3rd Occurrence': 'third_occur.name',
                    '4th Occurrence': 'fourth_occur.name',
                    '5th Occurrence': 'fifth_occur.name',
                    '6th Occurrence': 'sixth_occur.name',
                    '7th Occurrence': 'seventh_occur.name',
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
            this.columns = (this.rule.behavioral_action.child_rules.edit.is_enable
            || this.rule.behavioral_action.child_rules.archive.is_enable) ?
                [
                    'actions',
					'gravity_offense.gravity',
					'first_occur.name',
					'second_occur.name',
					'third_occur.name',
					'fourth_occur.name',
					'fifth_occur.name',
					'sixth_occur.name',
					'seventh_occur.name'
				] :
                [
                    'gravity_offense.gravity',
					'first_occur.name',
					'second_occur.name',
					'third_occur.name',
					'fourth_occur.name',
					'fifth_occur.name',
					'sixth_occur.name',
					'seventh_occur.name'
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
                this.$constants.Coc_API.get("/behavioralaction")
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
                this.gravity_data = {};
            },
            addEditRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = this.labels.edit;
                    this.gravity_data.occur_id = props_row.id;
                    this.gravity_data.occur_gravity = props_row.gravity_offense.gravity;
                    this.gravity_data.occur_first_irr = props_row.first_occur.name;
                    if (props_row.second_occur) this.gravity_data.occur_second_irr = props_row.second_occur.name;
                    if (props_row.third_occur) this.gravity_data.occur_third_irr = props_row.third_occur.name;
                    if (props_row.fourth_occur) this.gravity_data.occur_fourth_irr = props_row.fourth_occur.name;
                    if (props_row.fifth_occur) this.gravity_data.occur_fifth_irr = props_row.fifth_occur.name;
                    if (props_row.sixth_occur) this.gravity_data.occur_sixth_irr = props_row.sixth_occur.name;
                    if (props_row.seventh_occur) this.gravity_data.occur_seventh_irr = props_row.seventh_occur.name;
                } else {
                    this.headerName = this.labels.add;
                    this.gravity_data.occur_gravity_id = '';
                    this.gravity_data.occur_first_irr = '';
                    this.gravity_data.occur_second_irr = '';
                    this.gravity_data.occur_third_irr = '';
                    this.gravity_data.occur_fourth_irr = '';
                    this.gravity_data.occur_fifth_irr = '';
                    this.gravity_data.occur_sixth_irr = '';
                    this.gravity_data.occur_seventh_irr = '';
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    title: 'Are you sure you want to delete?',
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
                            this.$constants.Coc_API.delete("/behavioralaction/" + props_row.id)
                                .then(response => {
                                    response.data
                                    this.getList()
                                    this.$success(props_row.gravity_offense.gravity + ' has been deleted!')
                                });

                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure(props_row.gravity_offense.gravity + ' has been cancelled!')
                        }
                    })
            },
        }
    }
</script>
