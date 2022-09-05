<template>
	<div class="row">
		<the-label  label="Month" :asterisk="true"  class="col-lg-2 m-t-sm m-b-sm">
			<validation-provider rules="required" v-slot="{ errors }">
				<vSelect
					v-model="user.month"
					:options="month_list"
					input-id="month"
					@input="monthToUser(user.month)"
					label="text"
				/>
				<span class="style-red" id="monthly">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
			</validation-provider>
		</the-label>
		<the-label label="Employee Name" :asterisk="true"  class="col-lg-4 m-t-sm m-b-sm">
			<validation-provider rules="required" v-slot="{ errors }">
				<vSelect
	                v-model="user.employee_name"
	                :options="selectedRespondent"
					input-id="employee_name"
					@input="userChecker(user.employee_name)"
	                label="text"
	            />
				<span class="style-red" id="employee">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
			</validation-provider>
        </the-label>
		<button
			type="button"
			@click.prevent="createMonthlyForm(user.employee_name, user.month)"
			class="btn btn-info col-lg-2 m-t-lg"
			style="margin-right:10px"
	 	>
			<i class="fa fa-print"/> Create Monthly Performance Form
		</button>
        <the-ibox title="">
            <v-client-table :data="rows" :columns="columns" :options="options" >
				<template slot="actions" slot-scope="props">
                    <!-- <button class='btn btn-default btn-xs'>
                        <i class="fa fa-paperclip"></i> Edit
                    </button> -->
                    <button class='btn btn-success btn-xs' @click="download(props.row.id)" >
                        <i class="fa fa-download"></i>
                        Download
                    </button>
                </template>
			</v-client-table>
        </the-ibox>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
				user: {
			        employee_name: null,
					month: null,
                },
				selectedRespondent: [],
				month_list: [],
				columns: [
					'actions',
					'name',
					'created_by',
					'date_created',
				],
				rows:  [],
				options: {
					headings: {
						'name': 'Employee Name',
						'created_by': 'Created By',
						'date_created': 'Date Created',
                    },
                    sortable: [
						'name',
						'created_by',
						'date_created',
                    ],
                    filterable: true,
                    pagination: { chunk:10 }
                },
            }
        },

		created(){
			this.month_list = this.$constants.Month_List;
			this.$bus.$on('createMonthlyForm', this.getList);
			if (this.$route.hash == '#create-monthly-form') {
				this.getList();
			}
		},


		methods: {
			monthToUser: function (data) {
				this.$constants.Coaching_API.get("/dropdown/users/"+ data.value)
                    .then(response => {
                    this.selectedRespondent = response.data;
					document.getElementById("monthly").textContent = ""
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
			},

			userChecker: function (user) {
				if (user) {
					document.getElementById("employee").textContent = ""
				}
			},

			splitUserName: function (data) {
				let employee_no = data.text.split(" ");

				return employee_no[employee_no.length - 1]
			},

			createMonthlyForm: function (employee, month) {
				if (employee && month) {
					let employee_no = this.splitUserName(employee)

					window.location.href = '/api/admin/coaching/create_monthly_form/' + employee_no +"/"+month.value
				} else {
					if (!month) {
						document.getElementById("monthly").textContent = "Field Required"
					}
					if (!employee) {
						document.getElementById("employee").textContent = "Field Required"
					}
				}
			},

			download: function (id){
				window.location.href = '/api/admin/coaching/download_form/' + id
			},

			getList: function() {
                this.isLoading = true
                this.$constants.Coaching_API.get("/monthly")
                .then(response => {
                    this.rows = response.data;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
		}
    }

</script>
