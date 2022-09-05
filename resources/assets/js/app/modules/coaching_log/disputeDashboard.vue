<template>
	<div class="row">
		<div class="form-group">
	        <label class="m-l-xs" style="margin-left: 15px;">File upload CSV</label>
	        <div class="col-lg-12">
				<button
					type="button"
					@click.prevent="upload"
					class="btn btn-success ladda-button-2 custom-file-button"
					style="padding: 6px 16px !important;"
				>
					<i class="fa fa-upload m-r-xs" /> Upload
				</button>
	            <input
	                type="file"
	                class="form-control"
					name="dispute_dashboard"
					id="dispute_dashboard"
					@change="fieldChange"
					ref="fileInput"
	            />
	        </div>
	    </div>
		<br><br>
		<the-ibox title="">
	        <v-client-table
	            :data="rows"
	            :columns="columns"
	            :options="options"
	        />
		</the-ibox>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
				laddabtn : '',
				attachments:[],
				fileData: new FormData(),
				columns: [
					'created_by',
					'changes',
					'date_updated',
				],
				rows:  [],
				options: {
					headings: {
						'created_by': 'Uploaded By',
						'changes': 'Changes',
						'date_updated': 'Date',
                    },
                    sortable: [
						'created_by',
						'changes',
						'date_updated',
                    ],
                    filterable: true,
                    pagination: { chunk:10 }
                },
            }
        },

		created(){
			if (this.$route.hash == '#dispute-dashboard-raw') {
				this.getList();
			}
			this.$bus.$on('disputeDashboard', this.getList);
		},

		mounted() {
			this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button-2'));
		},

		methods: {
			fieldChange(e) {
	            let selectedFiles = e.target.files
				this.attachments = [];

	            if (!selectedFiles.length) {
	                return false
	            }

	            for (let value of selectedFiles) {
	                this.attachments.push(value)
	            }
	        },

			upload: function () {
				this.laddabtn.start()
				if (this.attachments.length === 0) {
                	this.$swal({
	                    title: 'Please Choose A File',
	                    type: "error",
	                    showCancelButton: false,
	                    showConfirmButton: true
	                });
					this.laddabtn.stop()
	            } else {
	                for (let value of this.attachments) {
	                    this.fileData.append('attachments[]', value);
	                }
					this.$constants.Coaching_API.post('/importExcelDispute', this.fileData)
					.then(response => {
						if (response.data.isvalid) {
							const input = this.$refs.fileInput
						    input.type = 'text'
						    input.type = 'file'
							this.attachments = [];
							this.laddabtn.stop()
							this.$swal({
			                    title: 'Upload successfully',
			                    type: "success",
			                    showCancelButton: false,
			                    showConfirmButton: true
			                })
				                .then((result) => {
				                    if (result.value) {
				                        location.reload();
				                    }
				                })
                        } else {
                            this.attachments = [];
    						const input = this.$refs.fileInput
    					    input.type = 'text'
    					    input.type = 'file'
    						this.laddabtn.stop();
							let message_array = response.data.errors.toString();
							let message = message_array.replace(",", "<br />");
							this.$swal({
    		                    title: "Upload Error !!",
    		                    type: "error",
								html: message,
    		                    showCancelButton: false,
    		                    showConfirmButton: true
    		                }).then((result) => {
				                    if (result.value) {
				                        location.reload();
				                    }
				                })
                        }
	                })
	                .catch(error => {
						this.attachments = [];
						this.laddabtn.stop()
						if (error.status == 503) {
 						   this.$swal({
 							   title: 'Please Wait Momentarily',
 							   type: "success",
 							   showCancelButton: false,
 							   showConfirmButton: true
 						   });
 					   } else {
 						   this.globalErrorHandling(error);
 					   }
	                });
				}
            },

			getList: function() {
                this.isLoading = true
                this.$constants.Coaching_API.get("/dispute_dashboard")
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
