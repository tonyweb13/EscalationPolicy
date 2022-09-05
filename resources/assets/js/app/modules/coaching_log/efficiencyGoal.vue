<template>
    <div class="row">
        <the-ibox title="">
            <button type="button" @click.prevent="addComet()" class="btn btn-info" style="margin-bottom:20px;">
                <i class="fa fa-plus"/> Create Efficiency Goal
            </button>
            <v-client-table dusk="vue-table" :data="data" :columns="columns" :options="options" >
                <template slot="actions" slot-scope="props">
                    <button dusk="table-edit-button" class='btn btn-primary btn-xs' @click="addEditRow(props.row)">
                        <i class="fa fa-pencil"></i> Edit
                    </button>
                    <button dusk="table-delete-button" class='btn btn-danger btn-xs' @click="deleteRow(props.row)">
                        <i class="fa fa-remove"></i> Delete
                    </button>
				</template>
            </v-client-table>
        </the-ibox>
        <modal dusk="efficiency_modal" v-if="openModal" @close="openModal = false; eventModalClose()">
			<h3 slot="header">{{ headerName }}</h3>
			<add-form :efficiency_goal_props="efficiency_goal" v-if="openAction == 'add'" slot="body"  />
		</modal>
    </div>
</template>

<script>
	import addForm from '@/modules/coaching_log/addEfficiency.vue'

    export default {
        data: function () {
            return {
				columns: [
                    'actions',
					'portfolio_name',
                    'priority',
					'in_call',
					'ready',
					'wrap_up',
					'not_ready',
				],
				data:  [],
                efficiency_goal: {
                    id: '',
                    portfolio_name: '',
                    portfolio_id: '',
                    priority: '',
                    in_call: '',
                    ready: '',
                    not_ready: '',
                    wrap_up: '',
                },
				options: {
					headings: {
                        'portfolio_name': 'Portfolio',
						'priority': 'Priority',
						'in_call': 'In Call(%)',
						'ready': 'Ready(%)',
						'wrap_up': 'Wrap Up(%)',
						'not_ready': 'Not Ready(%)',
                    },
                    sortable: [
                        'portfolio_name',
                        'priority',
    					'in_call',
    					'ready',
    					'wrap_up',
    					'not_ready',
    				],
                    filterable: true,
                    pagination: { chunk:10 }
                },
                openModal: false,
				openAction: "",
            }
        },

		components: {
			addForm,
		},

        created(){
            if (this.$route.hash == '#efficiency-goals') {
				this.getList();
			}
            this.$bus.$on('efficiencyGoal', this.getList);
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
                this.getList();
            });
        },

		methods: {
			getList: function() {
                this.isLoading = true
                this.$constants.Coaching_API.get("/efficiency_goal")
                .then(response => {
                    this.data = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },

            eventModalClose: function() {
                this.efficiency_goal = {}
            },

            addEditRow: function (props_row) {
                this.openAction = "add";
                this.openModal = true;
                if(props_row){
                    this.headerName = "Edit Efficiency Goal";
                    this.efficiency_goal.id = props_row.id;
                    this.efficiency_goal.portfolio_name = props_row.portfolio_name;
                    this.efficiency_goal.portfolio_id = props_row.portfolio_id;
                    this.efficiency_goal.priority = props_row.priority;
                    this.efficiency_goal.in_call = props_row.in_call;
                    this.efficiency_goal.ready = props_row.ready;
                    this.efficiency_goal.wrap_up = props_row.wrap_up;
                    this.efficiency_goal.not_ready = props_row.not_ready;
                }
            },

            addComet: function () {
                this.openModal = true;
                this.openAction = "add";
                this.headerName = "Add Efficiency Goal"
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
                            this.$constants.Coaching_API.post("/delete_efficiency/" + props_row.id)
                                .then(response => {
                                    this.getList()
                                    this.$success(props_row.priority + ' has been deleted!')
                                });
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure(props_row.priority + ' has been cancelled!')
                        }
                    })
            },
		},
    }

</script>
