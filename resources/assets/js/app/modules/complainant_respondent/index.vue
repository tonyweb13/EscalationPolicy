<template>
	<div class="row">
		<div class="ibox-content col-lg-12 p-md m-b-sm">
			<button type="button" @click.prevent="addComplainant()" class="btn btn-info" style="margin-bottom:20px;"
			v-for="accs in access" v-if="user.designation.name == accs.designation.name">
                <i class="fa fa-plus"/> Create Incident Report
            </button>
			<div class="tabs-container">
				<ul class="nav nav-tabs">
					<li class="active">
						<a data-toggle="tab" href="#tab-inprogress" aria-expanded="false">In Progress</a>
					</li>
					<li><a data-toggle="tab" href="#tab-onhold" aria-expanded="false">On Hold</a></li>
					<!-- <li><a data-toggle="tab" href="#tab-closed" aria-expanded="false">Closed</a></li> -->
					<li><a data-toggle="tab" href="#tab-invite" aria-expanded="false">Invite</a></li>
				</ul>
				<div class="tab-content">

					<div id="tab-inprogress" class="tab-pane active"><inprogress/></div>
					<div class="clearfix"></div>

					<div id="tab-onhold" class="tab-pane"><onhold/></div>
					<div class="clearfix"></div>

					<!-- <div id="tab-closed" class="tab-pane"><closed/></div>
					<div class="clearfix"></div> -->

					<div id="tab-invite" class="tab-pane"><invite/></div>
					<div class="clearfix"></div>

				</div>
			</div>

			<modal v-if="openModal" @close="openModal = false">
				<h3 slot="header">Create Incident Report</h3>
                <add-form v-if="openAction == 'add'" slot="body" />
			</modal>

		</div>
    </div>
</template>
<script>
	import inprogress from '@/modules/complainant_respondent/inprogress.vue'
	import onhold from '@/modules/complainant_respondent/onhold.vue'
	import addForm from '@/modules/complainant_respondent/addForm.vue'
	// import closed from '@/modules/irhistory/closed.vue'
	import invite from '@/modules/complainant_respondent/invite.vue'

    export default {
		data: function () {
			return {
				user: this.$ep.user,
				openModal: false,
				openAction: "",
				access: ""
			}
		},
		components: {
			inprogress,
			onhold,
			addForm,
			// closed,
			invite,
		},
        created(){
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
			});

			this.$constants.Default_API.get("/user/supervisor/manager/access")
			.then(response => {
				this.access = response.data;
				console.log(this.access);
			})
			.catch(error => {
				this.globalErrorHandling(error);
			});
        },
		methods: {
			addComplainant: function () {
                this.openModal = true;
                this.openAction = "add";
            },
		}
    }

</script>
