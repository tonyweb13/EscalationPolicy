<template>
    <the-form :asterisk="true" @submitForm="eventUpdate" >
        <div slot="form-body">
            <the-label label="Respondent" :asterisk="false">
                <vSelect
                    v-model="complainant.respondent"
                    :options="selectedRespondent"
                    :disabled="true"
                    label="text"
                />
            </the-label>

            <the-label label="Provision" :asterisk="true">
                <vSelect
                    v-model="complainant.category"
                    :options="selectedCategory"
                    label="text"
                    @input="eventOffense"
                />
            </the-label>

            <!-- start category 15 - attendance -->
            <the-label label="Type of Infraction:" :asterisk="true"
                v-show="this.complainant.comp_offense_check == 'attendance'">
                <vSelect
                    v-model="complainant.offense"
                    :options="selectedInfraction"
                    label="text"
                    :disabled="infractionDisabled"
                    @input="eventAttendance"
                />
            </the-label>

            <the-label v-if="complainant.attendance_points" label="Attendance Points:"
                v-show="this.complainant.comp_offense_check == 'attendance'">
                <div style="background: #eee; padding: 10px;" v-html="complainant.attendance_points"/>
            </the-label>

            <the-label v-if="complainant.definition" label="Definition:"
                v-show="this.complainant.comp_offense_check == 'attendance'">
                <div style="background: #eee; padding: 10px;" v-html="complainant.definition"/>
            </the-label>
            <!-- end category 1 - attendance -->

            <!-- start category 2 to 13 -->
            <the-label label="Offense" :asterisk="true" v-show="this.complainant.comp_offense_check == 'offense'">
                <vSelect
                    v-model="complainant.offense"
                    :options="selectedOffense"
                    label="text"
                    :disabled="offenseDisabled"
                    @input="eventCoc"
                />
            </the-label>

            <the-label v-if="complainant.offense_description" label="Description:" v-show="this.complainant.comp_offense_check">
                <div style="background: #eee; padding: 10px;" v-html="complainant.offense_description"/>
            </the-label>

            <the-label v-if="complainant.gravity" label="Gravity:" v-show="this.complainant.comp_offense_check">
                <div style="background: #eee; padding: 10px;" v-html="complainant.gravity"/>
            </the-label>
            <!-- end category 2 to 13 -->

            <the-label label="Date of Incident" :asterisk="true" class="m-t-lg">
                <br>
                <div class="col-lg-6">
                    <small>Start</small>
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="true"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        placeholder="YYYY-MM-DD"
                        v-model="complainant.date_incident_start"
                    />
                </div>

                <div class="col-lg-6">
                    <small>End</small>
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="true"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        placeholder="YYYY-MM-DD"
                        v-model="complainant.date_incident_end"
                        @input="dateRangeValidation(complainant.date_incident_start, complainant.date_incident_end)"
                    />
                </div>

            </the-label>
            <br>
            <br>
            <br>

            <the-label label="Description" :asterisk="true">
                <vue-editor v-model="complainant.description" :editorToolbar="customToolbar"/>
            </the-label>

            <the-label label="Witness">
                <small v-if="complainant.category.value == 1">
                    <i> ( Category {{ complainant.category.text }} not require to include Witness )</i>
                </small>
                <vSelect
                    multiple
                    v-model="complainant.witness"
                    :options="selectedWitness"
                    label="text"
                    :disabled="true"
                />
            </the-label>

            <!-- <input-textarea v-model="complainant.remarks" label="Remarks"/> -->
            <div class="form-group">
                <div class="asterisk_label">*</div>
                <input-checkbox
                    v-model="complainant.is_attachment_tk_image"
                    label="TK Image Attachment (height: 120 width: 690)"
                    v-if='!complainant.is_attachment_tk_image'
                    @change="eventTKImageAttachment(complainant.is_attachment_tk_image)"
                    :checked="complainant.is_attachment_tk_image"
                />
                <input-checkbox
                    v-model="complainant.is_attachment_tk_image"
                    label="TK Image Attachment (height: 120 width: 690)"
                    v-if='complainant.is_attachment_tk_image'
                    :disabled="true"
                    @change="eventTKImageAttachment(complainant.is_attachment_tk_image)"
                    :checked="complainant.is_attachment_tk_image"
                />
            </div>

            <input-file-uploader label="Attachments" @change="attachFile" />


            <div v-for="attach1 in complainant.attachments" >
                <strong v-if="attach1.attachments != 'null'">
                    Download Complainant Attachments :
                    <br><br>
                </strong>
                <ul v-for="attach2 in attach1.attachments.split(',')" >
                    <li v-if="attach2.match(/complainants/g)" :class=" attach2 ">
                        <a :href="'/api/admin/incidentreport/download/attachment/'+ attach2 | urlReplace">
                            {{ attach2 | urlReplace }}
                        </a>
                        <button type="button" class="btn btn-default btn-xs" style="color:red"
                            v-if='!complainant.is_attachment_tk_image'
                            @click="removeAttachment(attach1, attach2, complainant)">
                            <i class="fa fa-close"/>
                        </button>
                    </li>
                </ul>
            </div>

            <button-submit :disabled='!isComplete'/>
            <!-- <button-save/> -->
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            complainantProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                complainant: {
                    respondent: '',
                    complainant_request_id: '',
                    complainant_id: '',
                    category: '',
                    description: '',
                    offense: '',
                    gravity: '',
                    date_incident_start: '',
                    date_incident_end: '',
                    offense_description: '',
                    is_attachment_tk_image: false,
                    attachments: [],
                    witness: [],
                    remarks: '',
                    respondent_multiple: [],
                    attachments_new: [],
                    incident_report: [],
                    witness_multiple: [],
                    hrbp_cluster_id: '',
                    ir_number: '',
                    infraction: '',
                    attendance_points: '',
                    definition: '',
                    approver_id: '',
                    approver_name: '',
                },
                selectedRespondent: [],
                selectedWitness: [],
                selectedCategory: [],
                selectedOffense: [],
                offenseDisabled: true,
                selectedCoc: [],
                selectedHr: [],
                selectedInfraction: [],
                selectedAttendance: [],
                showChecked: false,
                form: new FormData,
                attached: [],
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                disabledWitness: false,
                showOffense: false,
                showGravity: false,
                showDescription: false,
                showTypeInfraction: false,
                showAttendancePoints: false,
                showDefinition: false,
                infractionDisabled: false,
            };
        },
        created(){
            this.eventRespondent();
            this.eventWitness();
            this.eventCategory();
            this.eventOffense();
            this.eventFilingData();
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.complainant.infraction) {
                    return this.complainant.respondent && this.complainant.category
                    && this.complainant.date_incident_start && this.complainant.description
                    && this.complainant.infraction;
                } else {
                    return this.complainant.respondent && this.complainant.category
                    && this.complainant.offense && this.complainant.date_incident_start
                    && this.complainant.description;
                }
            }
        },
        methods: {
            eventFilingData() {
                if (this.complainantProps) {
                    this.complainant.respondent = this.complainantProps.comp_respondent;
                    this.complainant.complainant_request_id = this.complainantProps.comp_request;
                    this.complainant.id = this.complainantProps.comp_id;
                    this.complainant.category = this.complainantProps.comp_category;
                    this.complainant.description = this.complainantProps.comp_description;
                    this.complainant.offense = this.complainantProps.comp_offense;
                    this.complainant.gravity = this.complainantProps.comp_gravity;
                    this.complainant.date_incident_start = this.complainantProps.comp_date_incident_start;
                    this.complainant.date_incident_end = this.complainantProps.comp_date_incident_end;
                    this.complainant.offense_description = this.complainantProps.comp_offense_description;
                    this.complainant.is_attachment_tk_image = false;
                    if (this.complainantProps.comp_is_attachment_tk_image == 1) {
                        this.complainant.is_attachment_tk_image = true;
                    }
                    if (this.complainantProps.comp_witness.length > 0) {
                        this.witness_count = this.complainantProps.comp_witness.length;

                        for(let n=0; n<this.complainantProps.comp_witness.length; n++){
                            this.disabledWitness = false;

                            if (this.complainantProps.comp_witness[n].witness_fullname) {
                                this.complainant.witness[n] = {
                                    text: this.complainantProps.comp_witness[n].witness_fullname.first_name
                                        + " " + this.complainantProps.comp_witness[n].witness_fullname.last_name,
                                    value: this.complainantProps.comp_witness[n].witness_fullname.id,
                                    selected: "selected"
                                };
                            }
                        }
                    }
                    this.complainant.ir_id = this.complainantProps.comp_ir_id;
                    this.complainant.remarks = '';
                    this.complainant.attachments = this.complainantProps.comp_attachments;
                    this.complainant.comp_offense_check = this.complainantProps.comp_offense_check;

                    this.complainant.approver_id = this.complainantProps.comp_approver_id;
                    this.complainant.approver_name = this.complainantProps.comp_approver_name;
                } else {
                    this.complainant.respondent = '';
                    this.complainant.category = '';
                    this.complainant.description = '';
                    this.complainant.offense = '';
                    this.complainant.gravity = '';
                    this.complainant.date_incident_start = '';
                    this.complainant.date_incident_end = '';
                    this.complainant.offense_description = '';
                    this.complainant.is_attachment_tk_image = false;
                    this.complainant.witness = [];
                    this.complainant.remarks = '';
                    this.complainant.attachments = [];
                    this.complainant.comp_offense_check = '';
                    this.complainant_data.approver_id = '';
                    this.complainant_data.approver_name = '';
                }

            },
            eventUpdate() {
                for(let r=0; r<this.complainant.respondent.length; r++){
                    this.complainant.respondent_multiple.push(this.complainant.respondent[r].text);
                }

                for(let w=0; w<this.complainant.witness.length; w++){
                    this.complainant.witness_multiple.push(this.complainant.witness[w].text);
                }

                for(let i=0; i<this.complainant.attachments_new.length;i++){
                    this.form.append('pics[]', this.complainant.attachments_new[i]);
                }
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                this.laddabtn.start();
                let complainant = this.complainant;

                let getAxios = '';
                    // remove request in new untitled file
                    this.$constants.Incident_API.post('/request_edit/attach/file', this.form, config)
                    .then(response => {
                        this.complainant.attachments_new = response.data.uploaded
                        getAxios = this.$constants.Incident_API.get("/request_edit/edit/"+
                            complainant.complainant_request_id, {
                                params: {
                                    complainant_id: complainant.id,
                                    category: complainant.category.text ? complainant.category.text : '',
                                    witness_user_id: complainant.witness ? complainant.witness : [],
                                    ir_id: complainant.ir_id ? complainant.ir_id : [],
                                    description: complainant.description ? complainant.description : '',
                                    offense: complainant.offense['text'] ? complainant.offense['text'] : complainant.offense ? complainant.offense : '',
                                    gravity: complainant.gravity ? complainant.gravity : '',
                                    date_incident_start: complainant.date_incident_start ? complainant.date_incident_start : '',
                                    date_incident_end: complainant.date_incident_end ? complainant.date_incident_end : '',
                                    offense_description: complainant.offense_description ? complainant.offense_description : '',
                                    is_attachment_tk_image: complainant.is_attachment_tk_image ? complainant.is_attachment_tk_image : '',
                                    remarks: complainant.remarks ? complainant.remarks : '',
                                    attachments: complainant.attachments_new ? complainant.attachments_new : [],
                                    comp_offense_check: complainant.comp_offense_check ? complainant.comp_offense_check : '',
                                    approver_id: complainant.approver_id ? complainant.approver_id : '',
                                    approver_name: complainant.approver_name ? complainant.approver_name : '',
                                }
                            });

                    getAxios.then(response => {
                        this.laddabtn.stop();
                        this.$bus.$emit('init_modal', false);
                        this.complainant = response.data;
                        this.$bus.$emit('updateList');
                        this.$bus.$emit('updateDashboard');
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventTKImageAttachment(is_attachment_tk_image) {
                if (event.target.checked == false) {
                    this.complainant.is_attachment_tk_image = false;
                } else {
                    this.complainant.is_attachment_tk_image = true;
                }
            },
            removeAttachment(attachments, file, complainant) {
                this.$swal({
                    title: 'Remove Attachments',
                    text: 'Are you sure you want to Remove this file ?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                .then((result) => {
                    console.log(complainant);
                    if (result.value) {
                        let getAxios = '';
                        getAxios = this.$constants.Incident_API.get("/remove/attachments/"+attachments.id, {
                            params: {
                                attachment: file,
                                approver_id: complainant.approver_id
                            },
                        });
                        getAxios.then(response => {
                            let x = document.getElementsByClassName(file);
                            x[0].style.display = 'none';
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    this.$success('Remove successfully!');

                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$failure('Remove has been cancelled');
                    }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
                console.log(attachments.id);
            },
            eventRespondent(){
                this.$constants.Default_API.get("/dropdown/employee/hrbp")
                .then(response => {
                    this.selectedRespondent = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventWitness(){
                this.$constants.Default_API.get("/dropdown/users")
                    .then(response => {
                    this.selectedWitness = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventCategory(){
                this.$constants.Coc_API.get("/dropdown/category")
                    .then(response => {
                        this.selectedCategory = response.data;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            eventOffense(){
                if(this.complainant.category.value != undefined){

                    if(this.complainant.category.value == 15){
                        this.infractionDisabled = false;
                        this.offenseDisabled = true;
                        this.disabledWitness = true;
                        this.complainant.witness = '';

                        this.complainant.offense = '';
                        this.complainant.offense_description = '';
                        this.complainant.gravity = '';

                        this.showOffense = false;
                        this.showGravity = false;
                        this.showDescription = false;
                        this.showTypeInfraction = true;
                        this.showAttendancePoints = true;
                        this.showDefinition = true;

                        console.log("if category attendance=="+this.complainant.category.value
                        +" this.disabledWitness="+this.disabledWitness);

                        this.$constants.Coc_API.get("/dropdown/attendance/infraction")
                        .then(response => {
                            this.selectedInfraction = response.data;
                            console.log("selectedInfraction==");
                            console.log(this.selectedInfraction);
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });

                    } else {
                        this.infractionDisabled = true;
                        this.offenseDisabled = false;
                        this.complainant.offense = '';
                        this.complainant.offense_description = '';
                        this.complainant.gravity = '';
                        this.disabledWitness = false;

                        this.showOffense = true;
                        this.showGravity = true;
                        this.showDescription = true;
                        this.showTypeInfraction = false;
                        this.showAttendancePoints = false;
                        this.showDefinition = false;

                        console.log("else category=="+this.complainant.category.value
                        +" this.disabledWitness="+this.disabledWitness);

                        this.$constants.Coc_API.get("/dropdown/offense/category/"+this.complainant.category.value)
                        .then(response => {
                            this.selectedOffense = response.data;
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    }
                }
            },
            eventCoc(){
                if(this.complainant.offense.value != undefined){
                    this.$constants.Coc_API.get("/offense/codeofconduct/"+this.complainant.offense.value)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.complainant.offense_description = this.selectedCoc.offense_description;
                        this.complainant.gravity = this.selectedCoc.gravity.gravity;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            eventAttendance(){
                if(this.complainant.infraction.value != undefined){
                    this.$constants.Coc_API.get("/attendance/points/systems/"+this.complainant.infraction.value)
                    .then(response => {
                        this.selectedAttendance = response.data;
                        this.complainant.attendance_points = this.selectedAttendance.attendancepoint.attendance_points;
                        this.complainant.definition = this.selectedAttendance.definition;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            attachFile(e){
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    return false;
                }
                if (this.complainant.is_attachment_tk_image) {
                    for (let value of selectedFiles) {

                       // check file format csv
                       if (value.type == "image/*") {
                           for(let i=0; i<selectedFiles.length;i++){
                               this.complainant.attachments_new.push(selectedFiles[i]);
                           }
                       } else {
                           this.complainant.attachments_new = [];
                           this.fileData =  new FormData();
                           selectedFiles = '';

                           this.$swal({
                               title: 'Invalid File Format',
                               text: 'Please use file extension (.csv)',
                               type: "error",
                               showConfirmButton: true
                           });
                       }
                   }
               } else {
                   for(let i=0; i<selectedFiles.length;i++){
                       this.complainant.attachments_new.push(selectedFiles[i]);
                   }
               }
            },
            dateRangeValidation(start, end){
                if (end < start) {
                    this.$swal({
                        text: "Date of Incident is not valid range",
                        type: "error",
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        this.complainant.date_incident_start = '';
                        this.complainant.date_incident_end = '';
                    });
                }
            }
        },
    }
</script>
