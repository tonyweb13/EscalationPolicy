<template>
	<div class="row">
		<the-label label="Month" :asterisk="true"  class="col-lg-2 m-t-sm m-b-sm">
			<validation-provider rules="required" v-slot="{ errors }">
				<vSelect
					v-model="user.month"
					:options="month_list"
					@input="monthToUser(user.month)"
					input-id="month_week_form"
					label="text"
				/>
				<span class="style-red" id="month_error">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
			</validation-provider>
		</the-label>
		<the-label label="Employee Name" :asterisk="true"  class="col-lg-4 m-t-sm m-b-sm">
			<validation-provider rules="required" v-slot="{ errors }">
				<vSelect
	                v-model="user.emloyee_name"
	                :options="selectedRespondent"
					@input="userToWeek(user.emloyee_name, user.month)"
					input-id="employee_name_week_form"
	                label="text"
	            />
				<span class="style-red" id="employee_error">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
			</validation-provider>
        </the-label>
        <the-label label="Week" :asterisk="true"  class="col-lg-2 m-t-sm m-b-sm">
			<validation-provider rules="required" v-slot="{ errors }">
				<vSelect
					v-model="user.week"
					:options="week_list"
					input-id="week_number_week_form"
					@input="weeklyChecker(user.week)"
					label="text"
				/>
				<span class="style-red" id="weekly">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
			</validation-provider>
		</the-label>
		<button
			type="button"
			@click.prevent="createWeeklyForm(user.emloyee_name, user.month, user.week)"
			class="btn btn-info col-lg-2 m-t-lg"
			style="margin-right:10px"
	 	>
			<i class="fa fa-print"/> Create Weekly Performance Form
		</button>

        <the-ibox title="">
            <v-client-table :data="rows" :columns="columns" :options="options" >
				<template slot="actions" slot-scope="props">
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
	import addForm from '@/modules/coaching_log/addCoachingLogForm.vue'

    export default {
        data: function () {
            return {
				user: {
			        emloyee_name: null,
					month: null,
					week: null,
                },
				selectedRespondent: [],
				month_list: [],
				week_list: [],
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
			this.week_list = [];
			this.employee_name = [];
			if (this.$route.hash == '#create-weekly-form') {
				this.getList();
			}
			this.$bus.$on('weeklyPerformance', this.getList);
        },

		methods: {
			monthToUser: function (data) {
				this.$constants.Coaching_API.get("/dropdown/users/"+ data.value)
				.then(response => {
					this.selectedRespondent = response.data;
					this.isLoading = false
					document.getElementById("month_error").textContent = ""
				})
				.catch(error => {
					this.globalErrorHandling(error);
				});
			},

			userToWeek: function (employee_name, month) {
				if(employee_name !== '') {
					let employee_no = this.splitUserName(employee_name);
					this.$constants.Coaching_API.get("/userToWeek/"+ employee_no + "/" + month.value)
					.then(response => {
						this.week_list = response.data;
						this.isLoading = false
						document.getElementById("employee_error").textContent = ""
					})
					.catch(error => {
						this.globalErrorHandling(error);
					});
				}
			},

			weeklyChecker: function(week) {
				if (week) {
					document.getElementById("weekly").textContent = ""
				}
			},

			splitUserName: function (data) {
				let employee_no = data.text.split(" ");

				return employee_no[employee_no.length - 1]
			},

			createWeeklyForm: function (employee, month, week) {
				if (employee && month && week) {

					let employee_no = this.splitUserName(employee)

					window.location.href = '/api/admin/coaching/create_weekly_form/' + employee_no +"/"+month.value+"/"+week
				} else {
					if (!month) {
						document.getElementById("month_error").textContent = "Field Required"
					}
					if (!employee) {
						document.getElementById("employee_error").textContent = "Field Required"
					}
					if (!week) {
						document.getElementById("weekly").textContent = "Field Required"
					}
			    }
            },

			download: function (id){
				window.location.href = '/api/admin/coaching/download_form/' + id
			},

			getList: function() {
                this.isLoading = true
                this.$constants.Coaching_API.get("/weekly")
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
