<template>
    <the-form :asterisk="true" @submitForm="eventUpload">
        <div slot="form-body">
            <div class="form-group" v-if="uploadProps.nte_upload">
                <label>Uploaded NTE</label>
                <div>{{ signed.nte_upload }}</div>

                <div v-if="uploadProps.irr_id == null">
                    <br>
                        <small><i>
                            (Required selection in Generate Case Closure before uploading IRR,
                            please Go to <b>"EDIT"</b> Incident Form and make sure not <b>"Invalid IR"</b>)
                        </i></small>
                    <br>
                </div>
            </div>

            <div class="form-group" v-else>
                    <div class="asterisk_label">*</div>
                    <label class="m-l-xs">Upload NTE</label>
                    <div>
                        <label for="file-upload1" class="btn btn-success custom-single-file-button">
                            <i class="fa fa-upload m-r-xs" />
                            Upload
                        </label>
                        <input id="file-upload1"
                            type="file"
                            class="form-control"
                            @change="uploadNTE"
                        />
                    </div>
            </div>

            <div class="form-group" v-show="irrDisplay">

                    <label class="m-l-xs">Upload IRR</label>
                    <div>
                        <label for="file-upload2" class="btn btn-success custom-single-file-button">
                            <i class="fa fa-upload m-r-xs" />
                            Upload
                        </label>
                        <input id="file-upload2"
                            type="file"
                            class="form-control"
                            @change="uploadIRR"
                        />
                    </div>
            </div>
            <button-save v-if="!uploadProps.nte_upload || irrDisplay == true" :disabled='!isComplete'/>
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            uploadProps: Object,
        },
        data: function () {
            return {
                isLoading: false,
                form: new FormData,
                irrDisplay: false,
                signed: {
                    complainant_id: '',
                    ir_id: '',
                    ir_number: '',
                    nte_upload: [],
                    irr_upload: [],
                    respondent_id: '',
                    category_id: '',
                    irr_id: '',
                    respondent_user_id: '',
                    complainant_user_id: '',
                    hr_user_id: ''
                },
            };
        },
        created() {
            this.signed.complainant_id = this.uploadProps.complainant_id;
            this.signed.ir_id = this.uploadProps.ir_id;
            this.signed.ir_number = this.uploadProps.ir_number;
            this.signed.irr_id = this.uploadProps.irr_id;

            /* callback use for progressionoccurence */
            this.signed.respondent_id = this.uploadProps.respondent_id;
            this.signed.category_id = this.uploadProps.category_id;
            this.signed.respondent_user_id = this.uploadProps.respondent_user_id;
            this.signed.complainant_user_id = this.uploadProps.complainant_user_id;
            this.signed.hr_user_id = this.uploadProps.hr_user_id;

            if (this.uploadProps.nte_upload) {
                this.signed.nte_upload = this.uploadProps.nte_upload;
            }

            if(this.uploadProps.irr_id) {
                this.irrDisplay = true;
            }

            console.log("this.signed==");
            console.log(this.signed);
        },
        computed: {
            isComplete () {
                return Object.keys(this.signed.nte_upload).length > 0
            }
        },
        methods: {
            eventUpload(){
                let signed = this.signed;

                if (this.uploadProps.nte_upload) {
                    this.form.append('nte_uploaded', this.uploadProps.nte_upload);
                } else if (Object.keys(signed.nte_upload).length > 0) {
                    for(let i=0; i<signed.nte_upload.length;i++){
                        this.form.append('pics1[]', signed.nte_upload[i]);
                        console.log("eventUpload nte_upload this.form==");
                        console.log(this.form);
                    }
                }

                if (Object.keys(signed.irr_upload).length > 0) {
                    for(let i=0; i<signed.irr_upload.length;i++){
                        this.form.append('pics2[]', signed.irr_upload[i]);
                        console.log("eventUpload irr_upload this.form==");
                        console.log(this.form);
                    }

                    /*TODO: not insert/update on database table */

                    /*  check if not absolved
                        status irr_id = 8 (Absolved)
                        Generate NTE (is_generate_nte_invalid_ir = 1)
                        Invalid IR   (is_generate_nte_invalid_ir = 2)
                    */

                        /* Moved to update upload nte/irr */
                        /* Insert Employee Progression Offense here */
                        this.$constants.Admin_API.get('/progressionoffense/record/'
                        + signed.respondent_user_id
                        + '/' + signed.category_id
                        + '/' + signed.respondent_id
                        )
                        .then(response => {
                            console.log("uploadsigned response.data==");
                            console.log(response.data);

                            return response.data;
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });

                } else {
                    this.form.append('pics2[]', '');
                }

                this.form.append('ir_number', signed.ir_number);
                this.form.append('complainant_id', signed.complainant_id);
                this.form.append('ir_id', signed.ir_id);
                this.form.append('respondent_user_id', signed.respondent_user_id);
                this.form.append('complainant_user_id', signed.complainant_user_id);
                this.form.append('hr_user_id', signed.hr_user_id);

                const config = { headers: { 'Content-Type': 'multipart/form-data' }}

                this.$constants.Admin_API.post('/irhistory/attach/signed', this.form, config)
                .then(response => {
                    this.$bus.$emit('init_modal', false);
                    this.signed = response.data;
                    this.$success('Successfully Saved!')
                    // this.$bus.$emit('updateList');
                    this.$bus.$emit('updateDashboard');
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            uploadNTE(e){
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    return false;
                }

                if (this.signed.irr_id) {
                    this.irrDisplay = true;
                }

                for(let i=0; i<selectedFiles.length;i++){
                    this.signed.nte_upload.push(selectedFiles[i]);
                }
            },
            uploadIRR(e){
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    return false;
                }

                for(let i=0; i<selectedFiles.length;i++){
                    this.signed.irr_upload.push(selectedFiles[i]);
                }
            },
        },
    }
</script>
