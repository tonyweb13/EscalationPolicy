<template>
	<div class="row">
		<div class="form-group">
	        <label class="m-l-xs" style="margin-left: 15px;">File upload CSV</label>
	        <div class="col-lg-12">
				<button
					type="button"
					@click.prevent="upload"
					class="btn btn-success ladda-button custom-file-button"
					style="padding: 6px 16px !important;"
				>
					<i class="fa fa-upload m-r-xs" /> Upload
				</button>
	            <input
	                type="file"
	                class="form-control"
					name="dashboard"
					id="dashboard"
					@change="fieldChange"
					ref="fileInput"
	            />
	        </div>
	    </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
				laddabtn : '',
			 	attachments:[],
				fileData: new FormData(),
            }
        },

		mounted() {
			this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
		},

		methods: {
			fieldChange(e) {
				this.attachments = [];
	            let selectedFiles = e.target.files

	            if (!selectedFiles.length) {
	                return false
	            }

	            for (let value of selectedFiles) {
	                this.attachments.push(value)
	            }
	        },

			upload: function () {
				this.laddabtn.start();
				if (this.attachments.length === 0) {
                	this.$swal({
	                    title: 'Please Choose A File',
	                    type: "error",
	                    showCancelButton: false,
	                    showConfirmButton: true
	                });
					this.laddabtn.stop();
	            } else {
	                for (let value of this.attachments) {
	                    this.fileData.append('attachments[]', value);
	                }
					this.$constants.Coaching_API.post('/importExcel', this.fileData)
					.then(response => {
						if (response.data.isvalid) {
                            this.attachments = [];
    						const input = this.$refs.fileInput
    					    input.type = 'text'
    					    input.type = 'file'
    						this.laddabtn.stop();
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
						this.laddabtn.stop();
						console.log(error.status);
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
		}
    }

</script>
