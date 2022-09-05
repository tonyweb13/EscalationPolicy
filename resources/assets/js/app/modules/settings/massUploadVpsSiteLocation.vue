<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div class="form-group" style="padding-bottom: 50px;">
            <label class="m-l-xs" style="margin-left: 15px;">Mass Upload VPS Site Location (CSV)</label><br>
            <small style="margin-left: 15px; background-color: yellow;"><i>
                NOTE: Only "Two Neo, Alabang, Bacolod and Market Market" 
                are accepted in vps_office_location field
            </i></small>
            <div class="col-lg-12">
                <button
                    type="button"
                    @click.prevent="upload"
                    class="btn btn-success ladda-button custom-file-button"
                    style="padding: 6px 18px !important;"
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
                errors:[],
                laddabtn : '',
                attachments:[],
                fileData: new FormData(),
                isLoading: false,
            };
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
                    let type = value.name.split('.');
                    console.log('VALUE', value.type)

                    // check file format csv
                    if (type[type.length - 1] == "csv") {
                        this.attachments.push(value);
                    } else {
                        this.attachments = [];
                        this.$swal({
                            title: 'Invalid File Format',
                            text: 'Please use file extension (.csv)',
                            type: "error",
                            showConfirmButton: true
                        });
                    }
                }
            },

            upload: function () {
                this.laddabtn.start();
                this.isLoading = true
                if (this.attachments.length === 0) {
                	this.$swal({
                        title: 'Please Choose A File',
                        type: "error",
                        showConfirmButton: true
                    });
                    this.laddabtn.stop();
                    this.isLoading = false
                } else {
                    for (let value of this.attachments) {
                        this.fileData.append('attachments[]', value);
                    }
                    this.$constants.Settings_ACL_API.post('/import/csv/sitelocation', this.fileData)
                    .then(response => {
                        if (response.data.isvalid) {
                            this.attachments = [];
                            const input = this.$refs.fileInput
                            input.type = 'text'
                            input.type = 'file'
                            this.laddabtn.stop();
                            this.isLoading = false
                            this.$bus.$emit('updateList');
                            this.$swal({
                                title: 'CSV Uploaded successfully',
                                type: "success",
                                showCancelButton: false,
                                showConfirmButton: true
                            });
                            location.reload();
                        } else {
                            this.attachments = [];
                            const input = this.$refs.fileInput
                            input.type = 'text'
                            input.type = 'file'
                            this.laddabtn.stop();
                            this.isLoading = false
                            this.$swal({
                                title: response.data.errors[0],
                                type: "error",
                                showCancelButton: false,
                                showConfirmButton: true
                            });
                        }
                    })
                    .catch(error => {
                        this.attachments = [];
                        this.laddabtn.stop();
                        this.isLoading = false
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
