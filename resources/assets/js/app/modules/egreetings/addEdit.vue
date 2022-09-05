<template>
    <the-form :asterisk="true" @submitForm="eventUpdate">
        <div slot="form-body">
            <div class="form-group" v-if="!egreetings.greeting_id">
                <div class="asterisk_label">*</div>
                <label class="m-l-xs">Template 1</label>
                <div>
                    <label for="file-upload1" class="btn btn-success"
                    style="cursor: pointer; position: absolute;">
                        <i class="fa fa-upload m-r-xs" />
                        Upload 1
                    </label>
                    <input id="file-upload1"
                        type="file"
                        class="form-control"
                        @change="attachTemplate1"
                    />
                </div>
            </div>
            <div class="form-group" v-if="!egreetings.greeting_id">
                <div class="asterisk_label">*</div>
                <label class="m-l-xs">Template 2</label>
                <div>
                    <label for="file-upload2" class="btn btn-success" 
                    style="cursor: pointer; position: absolute;">
                        <i class="fa fa-upload m-r-xs" />
                        Upload 2
                    </label>
                    <input id="file-upload2"
                        type="file"
                        class="form-control"
                        @change="attachTemplate2"
                    />
                </div>
            </div>
            <!-- <div class="form-group" v-if="!egreetings.greeting_id">
                <div class="asterisk_label">*</div>
                <label class="m-l-xs">Profile Picture</label>
                <div>
                    <label for="file-upload3" class="btn btn-success" 
                    style="cursor: pointer; position: absolute;">
                        <i class="fa fa-upload m-r-xs" />
                        Upload 3
                    </label>
                    <input id="file-upload3"
                        type="file"
                        class="form-control"
                        @change="attachProfilePic"
                    />
                </div>
            </div> -->
            <the-label label="Employee Number" :asterisk="true">
                <input
                    type="number"
                    class="form-control"
                    v-model="egreetings.employee_number"
                />
            </the-label>
            <the-label label="Employee Name" :asterisk="true">
                <input
                    type="text"
                    class="form-control"
                    v-model="egreetings.employee_name"
                />
            </the-label>
            <the-label label="Email Address" :asterisk="true">
                <input
                    type="email"
                    class="form-control"
                    v-model="egreetings.email_address"
                />
            </the-label>
            <the-label label="Birthday" :asterisk="true">
                <datepicker
                    :input-class="'custom-datepicker'"
                    :format="'yyyy-MM-dd'"
                    :calendar-button="true"
                    :calendar-button-icon="'fa fa-calendar'"
                    :use-utc="false"
                    placeholder="YYYY-MM-DD"
                    v-model="egreetings.birthday"
                    :typeable="true"
                />
            </the-label>
            <button-submit :disabled='!isComplete'/>
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            eGreetingsProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                formTemplate1: new FormData,
                formTemplate2: new FormData,
                formProfilePic: new FormData,
                egreetings: {
                    template1: [],
                    template2: [],
                    // profile_pic: [],
                    employee_number: '',
                    employee_name: '',
                    email_address: '',
                    birthday: '',
                    greeting_id: '',
                },
            };
        },
        created(){
            if (this.eGreetingsProps.greeting_id) {
                this.egreetings.employee_number = this.eGreetingsProps.employee_number
                this.egreetings.employee_name = this.eGreetingsProps.employee_name;
                this.egreetings.email_address = this.eGreetingsProps.email_address;
                this.egreetings.birthday = this.eGreetingsProps.birthday;
                this.egreetings.greeting_id = this.eGreetingsProps.greeting_id;
            } else {
                this.egreetings.template1 = '';
                this.egreetings.template2 = '';
                // this.egreetings.profile_pic = '';
                this.egreetings.employee_number = '';
                this.egreetings.employee_name = '';
                this.egreetings.email_address = '';
                this.egreetings.birthday = '';
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.eGreetingsProps.greeting_id) {
                    return this.egreetings.employee_number && this.egreetings.employee_name 
                    && this.egreetings.email_address && this.egreetings.birthday;
                } else {
                    return this.egreetings.template1 && this.egreetings.template2 && 
                    this.egreetings.employee_number && this.egreetings.employee_name 
                    && this.egreetings.email_address && this.egreetings.birthday;
                }  
            }
        },
        methods: {
            eventUpdate() {
                this.laddabtn.start();
                let egreetings = this.egreetings;
                let getAxios = '';
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                for(let r=0; r<this.egreetings.template1.length;r++){
                    this.formTemplate1.append('template1[]', egreetings.template1[r]);
                    this.formTemplate1.append('employee_number', egreetings.employee_number);
                }
                for(let r=0; r<this.egreetings.template2.length;r++){
                    this.formTemplate2.append('template2[]', egreetings.template2[r]);
                    this.formTemplate2.append('employee_number', egreetings.employee_number);
                }
                // for(let r=0; r<this.egreetings.profile_pic.length;r++){
                //     this.formProfilePic.append('profile_pic[]', egreetings.profile_pic[r]);
                //     this.formProfilePic.append('employee_number', egreetings.employee_number);
                // }
                this.$constants.Default_API.post('/egreetings/attach/template1', 
                this.formTemplate1, config)
                .then(response => {
                    return response.data.uploaded
                }).catch(error => {
                    this.globalErrorHandling(error);
                });
                this.$constants.Default_API.post('/egreetings/attach/template2', 
                this.formTemplate2, config)
                .then(response => {
                    return response.data.uploaded
                }).catch(error => {
                    this.globalErrorHandling(error);
                });
                // this.$constants.Default_API.post('/egreetings/attach/profilepic', 
                // this.formProfilePic, config)
                // .then(response => {
                //     return response.data.uploaded
                // }).catch(error => {
                //     this.globalErrorHandling(error);
                // });
                if (this.eGreetingsProps.greeting_id) {
                    getAxios = this.$constants.Default_API.get("/egreetings/" + 
                    this.eGreetingsProps.greeting_id, {
                        params: {
                            employee_number: egreetings.employee_number,
                            employee_name: egreetings.employee_name,
                            email_address: egreetings.email_address,
                            birthday: egreetings.birthday,
                        },
                    })
                } else {
                    getAxios = this.$constants.Default_API.get("/egreetings/create", {
                        params: {
                            template1: egreetings.template1[0].name,
                            template2: egreetings.template2[0].name,
                            // profile_pic: egreetings.profile_pic[0].name,
                            employee_number: egreetings.employee_number,
                            employee_name: egreetings.employee_name,
                            email_address: egreetings.email_address,
                            birthday: egreetings.birthday,
                        },
                    })
                }
                getAxios.then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.egreetings = response.data;
                    this.$success('Successfully Saved!')
                    window.location.href = '/egreetings'
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            attachTemplate1(e){
                this.egreetings.template1 = [];
                let selectedFiles = e.target.files;
                if(!selectedFiles.length){
                    return false;
                }
                if (e.target.files[0].size > 100000) {
                    this.$swal({
                        title: 'File Size Too Large',
                        text: 'Please use image lower than 100KB size',
                        type: "error",
                        showConfirmButton: true
                    }).then((result) => {
                        document.getElementById("file-upload1").value = "";
                        this.egreetings.template1 = [];
                    });
                } else if (e.target.files[0].type != "image/jpeg" && 
                e.target.files[0].type != "image/png" && e.target.files[0].type != "image/jpg") {
                    this.$swal({
                        title: 'Invalid File Format',
                        text: 'Please use (.jpeg .png .jpg)',
                        type: "error",
                        showConfirmButton: true
                    }).then((result) => {
                        document.getElementById("file-upload1").value = "";
                        this.egreetings.template1 = [];
                    });
                } else {
                    for(let i=0; i<selectedFiles.length;i++){
                        this.egreetings.template1.push(selectedFiles[i]);
                    }
                } 
            },
            attachTemplate2(e){
                this.egreetings.template2 = [];
                let selectedFiles = e.target.files;
                if(!selectedFiles.length){
                    return false;
                }
                if (e.target.files[0].size > 100000) {
                    this.$swal({
                        title: 'File Size Too Large',
                        text: 'Please use image lower than 100KB size',
                        type: "error",
                        showConfirmButton: true
                    }).then((result) => {
                        document.getElementById("file-upload2").value = "";
                        this.egreetings.template2 = [];
                    });
                } else if (e.target.files[0].type != "image/jpeg" && 
                e.target.files[0].type != "image/png" && e.target.files[0].type != "image/jpg") {
                    this.$swal({
                        title: 'Invalid File Format',
                        text: 'Please use (.jpeg .png .jpg)',
                        type: "error",
                        showConfirmButton: true
                    }).then((result) => {
                        document.getElementById("file-upload2").value = "";
                        this.egreetings.template2 = [];
                    });
                } else {
                    for(let i=0; i<selectedFiles.length;i++){
                        this.egreetings.template2.push(selectedFiles[i]);
                    }
                } 
            },
            // attachProfilePic(e){
            //     this.egreetings.profile_pic = [];
            //     let selectedFiles = e.target.files;
            //     if(!selectedFiles.length){
            //         return false;
            //     }
            //     if (e.target.files[0].size > 100000) {
            //         this.$swal({
            //             title: 'File Size Too Large',
            //             text: 'Please use image lower than 100KB size',
            //             type: "error",
            //             showConfirmButton: true
            //         }).then((result) => {
            //             document.getElementById("file-upload3").value = "";
            //             this.egreetings.profile_pic = [];
            //         });
            //     } else if (e.target.files[0].type != "image/jpeg" && 
            //     e.target.files[0].type != "image/png" && e.target.files[0].type != "image/jpg") {
            //         this.$swal({
            //             title: 'Invalid File Format',
            //             text: 'Please use (.jpeg .png .jpg)',
            //             type: "error",
            //             showConfirmButton: true
            //         }).then((result) => {
            //             document.getElementById("file-upload3").value = "";
            //             this.egreetings.profile_pic = [];
            //         });
            //     } else {
            //         for(let i=0; i<selectedFiles.length;i++){
            //             this.egreetings.profile_pic.push(selectedFiles[i]);
            //         }
            //     } 
            // },
        },
    }
</script>
