<template>
    <the-form :asterisk="true" @submitForm="eventUpdate" >
        <div slot="form-body">
            <the-label label="Respondent" :asterisk="true">
                <vSelect
                    multiple
                    v-model="complainant.respondent"
                    :options="selectedRespondent"
                    @input="eventRespondentInput"
                    label="text"
                />
            </the-label>

            <the-label label="Select Complainant" v-show="showSelectComplainant" :asterisk="true">
                <vSelect
                    v-model="complainant.complainant_user"
                    :options="selectedRespondent"
                    label="text"
                />
            </the-label>

            <hr v-if="showAddOffense">
            <the-label label="Provision" :asterisk="true">
                <vSelect
                    v-model="complainant.category"
                    :options="selectedCategory"
                    label="text"
                    @input="eventOffense"
                    :disabled="categoryDisabled"
                />
            </the-label>

            <!-- start category 15 - attendance -->
            <!-- <the-label label="Type of Infraction:" :asterisk="true" v-show="showTypeInfraction">
                <vSelect
                    v-model="complainant.infraction"
                    :options="selectedInfraction"
                    label="text"
                    :disabled="infractionDisabled"
                    @input="eventAttendance"
                />
            </the-label> -->

            <!-- <the-label v-if="complainant.attendance_points" label="Attendance Points:" v-show="showAttendancePoints">
                <div style="background: #eee; padding: 10px;" v-html="complainant.attendance_points"/>
            </the-label> -->

            <!-- <the-label v-if="complainant.definition" label="Definition:" v-show="showDefinition">
                <div style="background: #eee; padding: 10px;" v-html="complainant.definition"/>
            </the-label> -->
            <!-- end category 15 - attendance -->

            <!-- start category 2 to 13 -->
            <the-label label="Offense" :asterisk="true" v-show="showOffense">
                <vSelect
                    v-model="complainant.offense"
                    :options="selectedOffense"
                    label="text"
                    :disabled="offenseDisabled"
                    ref="vSelect"
                    @input="eventCoc"
                />
            </the-label>

            <the-label v-if="complainant.offense_description" label="Description:" v-show="showDescription">
                <div style="background: #eee; padding: 10px;" v-html="complainant.offense_description"/>
            </the-label>

            <the-label v-if="complainant.gravity" label="Gravity:" v-show="showGravity">
                <div style="background: #eee; padding: 10px;" v-html="complainant.gravity"/>
            </the-label>
            <div v-for="multiple_instance in complainant.instance">
                <the-label v-if="complainant.instance" label="Instance:" v-show="complainant.offense && complainant.respondent">
                    <div style="background: #eee; padding: 10px;" v-html="multiple_instance"/>
                </the-label>
            </div>
            <!-- end category 2 to 13 -->

            <!-- start multiple -->
            <div v-for="(multiple_off, index) in provision_multiple">
                <fieldset class="fieldsetCustom">
                <legend class="legendCustom" >
                Offense ( {{ offenseCnt == index ? 2 : indexCnt+index }} )
                </legend>
                <!-- multiple_off.category_multi == {{ multiple_off.category_multi.value }} -->
                <the-label label="Provision" v-show="showProvisionMultiple" :asterisk="true">
                    <vSelect
                        v-model="multiple_off.category_multi"
                        :options="selectedCategory"
                        label="text"
                        @input="eventOffenseMultiple(index)"
                    />
                </the-label>

                <!-- start category 15 - attendance -->
                <!-- <the-label label="Type of Infraction:" :asterisk="true" v-show="showTypeInfractionMultiple">
                    <vSelect
                        v-model="multiple_off.infraction_multi"
                        :options="selectedInfraction"
                        label="text"
                        :disabled="infractionMultipleDisabled"
                        @input="eventAttendanceMultiple(index)"
                    />
                </the-label> -->

                <!-- <the-label v-if="multiple_off.attendance_points_multi" label="Attendance Points:"
                v-show="showAttendancePointsMultiple">
                    <div style="background: #eee; padding: 10px;" v-html="multiple_off.attendance_points_multi"/>
                </the-label> -->

                <!-- <the-label v-if="multiple_off.definition_multi" label="Definition:" v-show="showDefinitionMultiple">
                    <div style="background: #eee; padding: 10px;" v-html="multiple_off.definition_multi"/>
                </the-label> -->
                <!-- end category 15 - attendance -->

                <the-label label="Offense" v-show="showOffenseMultiple" :asterisk="true">
                    <vSelect
                        v-model="multiple_off.offense_multi"
                        :options="selectedOffense"
                        label="text"
                        :disabled="offenseMultipleDisabled"
                        ref='vSelectMulti'
                        @input="eventCocMultiple(index)"
                    />
                </the-label>

                <the-label v-if="multiple_off.offense_description_multi" label="Description:" v-show="showDescriptionMultiple">
                    <div style="background: #eee; padding: 10px;" v-html="multiple_off.offense_description_multi"/>
                </the-label>

                <the-label v-if="multiple_off.gravity_multi" label="Gravity:" v-show="showGravityMultiple">
                    <div style="background: #eee; padding: 10px;" v-html="multiple_off.gravity_multi"/>
                </the-label>
                <div v-for="multiple_instance in multiple_off.instance" v-show="multiple_off.offense_multi && complainant.respondent">
                    <the-label v-if="multiple_off.instance" label="Instance:" >
                        <div style="background: #eee; padding: 10px;" v-html="multiple_instance"/>
                    </the-label>
                </div>

                <div class="pull-left" v-if="showDeleteOffense" >
                    <button type="button" @click="deleteOffense(index, multiple_off.offense_multi)" class="btn btn-danger">
                        <i class="fa fa-remove"/> Delete Offense
                        ( {{ offenseCnt == index ? 2 : indexCnt+index }} )
                    </button>
                </div>
                </fieldset>
            </div>

            <div class="addOffenseCustom" v-if="showAddOffense">
                <div class="pull-right">
                    <button type="button" @click="addOffense" class="btn btn-info">
                        <i class="fa fa-plus"/> Add Another Offense
                    </button>
                </div>
            </div>
            <hr v-if="showAddOffense">
            <!-- end multiple -->

            <the-label label="Date of Incident" :asterisk="true" class="m-t-lg" style="margin-bottom: 80px;">
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

            <!-- Replace textarea to disable paste image -->
            <the-label label="Description" :asterisk="true">
                <vue-editor v-model="complainant.description" :editorToolbar="customToolbar" @input="eventImageSize" />
                <!-- <vue-editor v-model="complainant.description" :editorToolbar="customToolbar" /> -->
            </the-label>

            <!-- <input-textarea label="Description" :asterisk="true" v-model="complainant.description" /> -->

            <the-label label="Witness">
                <small v-if="complainant.category.value == 1">
                    <i> ( Category {{ complainant.category.text }} not require to include Witness )</i>
                </small>
                <vSelect
                    multiple
                    v-model="complainant.witness"
                    :options="selectedWitness"
                    label="text"
                    :disabled="disabledWitness"
                />
            </the-label>

            <!-- <input-textarea v-model="complainant.remarks" label="Remarks"/> -->
            <the-label label="Attachment Type">
                <div class="form-group">
                    <vSelect
                        v-model="complainant.attachment_type"
                        :options="selectedAttachmentType"
                        @change="attachmentSelected"
                        label="text"
                        :disabled="disabledAttachmentType"
                    />
                </div>
            </the-label>

            <div v-if="complainant.attachment_type.value <= 1">
                <input-file-uploader label="Attachments" @change="attachFile" />
            </div>

            <div v-if="complainant.attachment_type.value >= 2">
                <input-single-file-uploader label="Attachments" @change="attachFile" />
            </div>
            <button-submit :disabled='!isComplete'/>
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
                role: this.$ep.role,
                user: this.$ep.user,
                laddabtn : '',
                complainant: {
                    respondent: '',
                    category: '',
                    description: '',
                    offense: '',
                    instance: [],
                    gravity: '',
                    date_incident_start: '',
                    date_incident_end: '',
                    offense_description: '',
                    attachment_type: { "value": 0, "text": "Others" },
                    attachments: [],
                    witness: '',
                    remarks: '',
                    respondent_multiple: [],
                    witness_multiple: [],
                    hrbp_cluster_id: '',
                    ir_number: '',
                    offense_multiple: [],
                    infraction: '',
                    attendance_points: '',
                    definition: '',
                    complainant_user: '',
                },
                selectedAttachmentType: this.$constants.Attachment_Type,
                selectedRespondent: [],
                selectedWitness: [],
                selectedCategory: [],
                selectedOffense: [],
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
                    [{ 'indent': '-1'}, { 'indent': '+1' }]
                ],
                offenseDisabled: true,
                disabledWitness: false,
                disabledAttachmentType: false,
                showOffense: false,
                showGravity: false,
                showDescription: false,
                showTypeInfraction: false,
                showAttendancePoints: false,
                showDefinition: false,
                infractionDisabled: false,
                provision_multiple: [],
                offenseMultipleDisabled: true,
                categoryDisabled: false,
                showProvisionMultiple: false,
                showOffenseMultiple: false,
                showSelectComplainant: false,
                showGravityMultiple: false,
                showDescriptionMultiple: false,
                showAddOffense: false,
                showDeleteOffense: false,
                indexCnt: 2,
                offenseCnt: 0,
                offense_remove: [],
                offense_index: [],
            };
        },
        created(){
            this.eventRespondent();
            this.eventWitness();
            this.eventCategory();
            this.eventOffense();

            this.complainant.respondent = '';
            this.complainant.category = '';
            this.complainant.description = '';
            this.complainant.offense = '';
            this.complainant.instance = [];
            this.complainant.gravity = '';
            this.complainant.date_incident_start = '';
            this.complainant.date_incident_end = '';
            this.complainant.offense_description = '';
            this.complainant.attachment_type = { "value": 0, "text": "Others" };
            this.complainant.witness = [];
            this.complainant.remarks = '';
            this.complainant.attachments = [];

            if (this.role == 'Super Admin Access' || this.role == 'HR Admin Access' || this.role == 'HR Access') {
                this.showSelectComplainant = true;
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.complainant.infraction) {
                    if (this.role == 'Super Admin Access' || this.role == 'HR Admin Access' || this.role == 'HR Access') {
                        return this.complainant.respondent && this.complainant.category
                        && this.complainant.date_incident_start && this.complainant.date_incident_end
                        && this.complainant.description && this.complainant.infraction && this.complainant.complainant_user;
                    } else {
                        return this.complainant.respondent && this.complainant.category
                        && this.complainant.date_incident_start && this.complainant.date_incident_end
                        && this.complainant.description && this.complainant.infraction;
                    }
                } else if (this.provision_multiple.length > 0) {
                    for(let o=0; o<this.provision_multiple.length; o++){
                        return this.provision_multiple[o].offense_multi.value && this.complainant.respondent
                        && this.complainant.date_incident_start && this.complainant.date_incident_end
                        && this.complainant.description && this.complainant.offense
                        && this.complainant.date_incident_start;
                    }
                } else {
                    if (this.role == 'Super Admin Access' || this.role == 'HR Admin Access' || this.role == 'HR Access') {
                        return this.complainant.respondent && this.complainant.category
                        && this.complainant.offense && this.complainant.date_incident_start
                        && this.complainant.date_incident_end && this.complainant.description
                        && this.complainant.complainant_user;
                    } else {
                        return this.complainant.respondent && this.complainant.category
                        && this.complainant.offense && this.complainant.date_incident_start
                        && this.complainant.date_incident_end && this.complainant.description;
                    }
                }
            }
        },
        methods: {
            dropdownOffenseCategory(category_value, offense_remove, index) {
                this.offense_index.push(index);

                this.$constants.Coc_API.get("/dropdown/offense/category/multiple/"
                +category_value+"/"
                +offense_remove)
                .then(response => {
                        this.selectedOffense = response.data;
                        // this.deleteDropdownSelectedOffense(index);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            dropdownInfraction() {
                this.$constants.Coc_API.get("/dropdown/attendance/infraction")
                .then(response => {
                    this.selectedInfraction = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventUpdate() {
                for(let r=0; r<this.complainant.respondent.length; r++){
                    this.complainant.respondent_multiple.push(this.complainant.respondent[r].value);
                }

                for(let w=0; w<this.complainant.witness.length; w++){
                    this.complainant.witness_multiple.push(this.complainant.witness[w].value);
                }

                for(let i=0; i<this.complainant.attachments.length;i++){
                    this.form.append('pics[]', this.complainant.attachments[i]);
                }
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                this.laddabtn.start();

                this.complainant.offense_multiple.push(this.complainant.offense.value);

                if (this.provision_multiple.length > 0) {
                    for(let o=0; o<this.provision_multiple.length; o++){
                        if (this.provision_multiple[o].offense_multi.value) {
                            this.complainant.offense_multiple.push(this.provision_multiple[o].offense_multi.value);
                        }
                    }
                }

                let complainant = this.complainant;
                let getAxios = '';
                let creator_user_id = complainant.complainant_user.value ? complainant.complainant_user.value : this.user.id;

                this.$constants.Default_API.post('/complainant/attach/file', this.form, config)
                    .then(response => {
                        complainant.attachments = response.data.uploaded

                        /* start saving here */
                        if (complainant.offense_multiple.length > 1) {
                            this.form.append('offense_multiple', complainant.offense_multiple);
                        } else {
                            this.form.append('offense_id', complainant.offense.value);
                        }

                        this.form.append('respondent_user_id', complainant.respondent_multiple);
                        this.form.append('witness_user_id', complainant.witness_multiple);
                        this.form.append('remarks', complainant.remarks);
                        this.form.append('attachments', complainant.attachments);
                        this.form.append('date_incident_start', complainant.date_incident_start);
                        this.form.append('date_incident_end', complainant.date_incident_end);
                        this.form.append('description', complainant.description);
                        this.form.append('attachment_type', complainant.attachment_type.value);
                        this.form.append('complainant_user', creator_user_id);

                        getAxios = this.$constants.Default_API.post('/complainant/create/description', this.form, config)

                        // if (complainant.infraction.value) {
                        //     getAxios = this.$constants.Default_API.get("/complainant/create", {
                        //         params: {
                        //             respondent_user_id: complainant.respondent_multiple,
                        //             witness_user_id: complainant.witness_multiple,
                        //             attendancepointssystem_id: complainant.infraction.value,
                        //             remarks: complainant.remarks,
                        //             attachments: complainant.attachments,
                        //             date_incident_start: complainant.date_incident_start,
                        //             date_incident_end: complainant.date_incident_end,
                        //             description: complainant.description,
                        //             attachment_type: complainant.attachment_type.value,
                        //             complainant_user: creator_user_id
                        //         },
                        //     })
                        // } else if (complainant.offense_multiple.length > 1) {
                        //     getAxios = this.$constants.Default_API.get("/complainant/create", {
                        //         params: {
                        //             respondent_user_id: complainant.respondent_multiple,
                        //             witness_user_id: complainant.witness_multiple,
                        //             offense_multiple: complainant.offense_multiple,
                        //             remarks: complainant.remarks,
                        //             attachments: complainant.attachments,
                        //             date_incident_start: complainant.date_incident_start,
                        //             date_incident_end: complainant.date_incident_end,
                        //             description: complainant.description,
                        //             attachment_type: complainant.attachment_type.value,
                        //             complainant_user: creator_user_id
                        //         },
                        //     })
                        // } else {
                        //     getAxios = this.$constants.Default_API.get("/complainant/create", {
                        //         params: {
                        //             respondent_user_id: complainant.respondent_multiple,
                        //             witness_user_id: complainant.witness_multiple,
                        //             offense_id: complainant.offense.value,
                        //             remarks: complainant.remarks,
                        //             attachments: complainant.attachments,
                        //             date_incident_start: complainant.date_incident_start,
                        //             date_incident_end: complainant.date_incident_end,
                        //             description: complainant.description,
                        //             attachment_type: complainant.attachment_type.value,
                        //             complainant_user: creator_user_id
                        //         },
                        //     })
                        // }

                            getAxios.then(response => {
                                this.laddabtn.stop();
                                this.$bus.$emit('init_modal', false);
                                this.complainant = response.data;

                                this.$bus.$emit('updateList');
                                this.$bus.$emit('updateDashboard');
                                this.$swal({
                                    title: 'Please Read!',
                                    html: '<p style="text-align:justify;">Your <b>Incident Report</b> has been successfully filed and reported to your '
                                          + 'respective Human Resource Business Partner. The turnaround time for reviewing new '
                                          + 'incident reports is 24 to 48 hours. For follow ups and additional notes, '
                                          + 'please leave a note to your HR Business Partner using the <b>Note</b> option.</p>',
                                    type: "success",
                                    confirmButtonText: 'OK',
                                });

                                /* TODO: make notification when not send to HR */
                                // this.$constants.Default_API.get("/mail/newIr/" + this.complainant.ir_number
                                // + "/" + this.$ep.user.employee_no)
                                // .then(response => {
                                //     return response.data
                                // })
                                // .catch(error => {
                                //     this.globalErrorHandling(error);
                                // });
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
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

                        this.dropdownInfraction();

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
            eventRespondentInput(user){
                if (user.length == 0) {
                    this.complainant.respondent = '';
                }

                let respondent_object_to_array = Object.keys(user).map((key) => [Number(key), user[key]['value']]);
                let respondent_user_array = []
                for (var i = 0; i < respondent_object_to_array.length; i++) {
                    respondent_user_array.push(respondent_object_to_array[i][1])
                }
                if((respondent_user_array.length < 1 || respondent_user_array == undefined)
                    && this.complainant.offense
                ){
                    this.complainant.instance = null
                    if (this.provision_multiple) {
                        for (var i = 0; i < this.provision_multiple.length; i++) {
                            this.provision_multiple[i].instance = null
                        }
                    }
                } else {
                    this.eventCoc();
                    if (this.provision_multiple) {
                        for (var i = 0; i < this.provision_multiple.length; i++) {
                            this.eventCocMultiple(i);
                        }
                    }
                }
            },
            eventCoc(e){
                if(this.complainant.offense != null && this.complainant.offense.value != undefined && this.complainant.respondent){
                    this.showAddOffense = true
                    this.offense_remove[0] = this.complainant.offense.value;
                    let respondent_object_to_array = Object.keys(this.complainant.respondent).map((key) => [Number(key), this.complainant.respondent[key]['value']]);
                    let respondent_user_array = []
                    for (var i = 0; i < respondent_object_to_array.length; i++) {
                        respondent_user_array.push(respondent_object_to_array[i][1])
                    }

                    this.$constants.Coc_API.get("/offense/codeofconduct/"+this.complainant.offense.value+"/"+respondent_user_array)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.complainant.offense_description = this.selectedCoc.offense_description;
                        this.complainant.gravity = this.selectedCoc.gravity.gravity;
                        this.complainant.instance = this.selectedCoc.instance;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else if (this.complainant.offense && this.complainant.respondent.length == 0) {
                    this.$refs.vSelect.clearSelection();
                    this.showAddOffense = false;
                    this.complainant.offense = '';
                    this.complainant.gravity = '';
                    this.complainant.offense_description = '';
                    this.$swal({
                        title: 'No Respondent Selected',
                        text: 'Please select respondent',
                        type: "error",
                        showConfirmButton: true
                    });

                } else if (this.complainant.offense == null && this.complainant.respondent.length == 0) {
                    this.$refs.vSelect.clearSelection();
                    this.showAddOffense = false;
                    this.complainant.offense = '';
                    this.complainant.gravity = '';
                    this.complainant.offense_description = '';
                    this.$swal({
                        title: 'No Respondent Selected',
                        text: 'Please select respondent',
                        type: "error",
                        showConfirmButton: true
                    });
                } else if (this.complainant.offense == null && this.complainant.respondent.length > 0) {
                    this.$refs.vSelect.clearSelection();
                    this.showAddOffense = false;
                    this.complainant.offense = '';
                    this.complainant.gravity = '';
                    this.complainant.offense_description = '';
                    this.complainant.instance = '';
                }
            },
            attachmentSelected(attachment_type) {
                this.complainant.attachments = [];
            },
            attachFile(e){
                this.complainant.attachments = [];
                if (e.target.files.length > 0 ) {
                    this.disabledAttachmentType = true;
                } else {
                    this.disabledAttachmentType = false;
                }
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    return false;
                }

                if (this.complainant.attachment_type.value > 0) {
                    for (let value of selectedFiles) {
                        // check file format csv
                        if (value.type.includes("image/")) {
                        this.complainant.attachments.push(value);
                        } else {
                            this.complainant.attachments = [];
                            this.fileData =  new FormData();
                            selectedFiles = '';

                            this.$swal({
                                title: 'Invalid File Format',
                                text: 'Please use (.jpeg .png .jpg)',
                                type: "error",
                                showConfirmButton: true
                            });
                        }
                    }
                } else {
                    if (e.target.files[0].type == "image/webp" || e.target.files[0].type == "image/gif" || e.target.files[0].type == "image/tiff") {
                        this.$swal({
                            title: 'Invalid File Format',
                            text: 'Please use (.jpeg .png .jpg)',
                            type: "error",
                            showConfirmButton: true
                        }).then((result) => {
                            document.getElementById("file-upload").value = "";
                            this.complainant.attachments = [];
                        });
                    } else {
                        for(let i=0; i<selectedFiles.length;i++){
                            this.complainant.attachments.push(selectedFiles[i]);
                        }
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
            },
            addOffense: function () {
                this.showProvisionMultiple = true;
                this.provision_multiple.push({
                    category_multi: '',
                    offense_multi: '',
                    offense_description_multi: '',
                    gravity_multi: '',
                 });
                if (this.provision_multiple[0].category_multi.value != "") {
                    this.categoryDisabled = true;
                    this.offenseDisabled = true;
                }
                this.showAddOffense = false;
            },
            deleteOffense: function (index, offense) {
                this.offense_remove.splice(this.offense_remove.indexOf(offense.value), 1);
                this.provision_multiple.splice(index, 1);
                if (index == 0) {
                    this.categoryDisabled = false;
                    this.offenseDisabled = false;
                    this.showAddOffense = true
                }

                if(index < 0)
                this.addOffense()

            },
            deleteDropdownSelectedOffense: function (index) {
                this.selectedOffense.splice(index, 1);
                const valuesArr = this.selectedOffense;
                const removeValFromIndex = this.offense_index;
                const indexSet = new Set(removeValFromIndex);
                const arrayWithValuesRemoved = valuesArr.filter((value, i) => !indexSet.has(i));
                this.selectedOffense = arrayWithValuesRemoved;
            },
            eventOffenseMultiple(index){
                if(this.provision_multiple[index].category_multi.value != undefined){
                    if(this.provision_multiple[index].category_multi.value == 15){

                        this.provision_multiple[index].offense_multi = '';
                        this.provision_multiple[index].offense_description_multi = '';
                        this.provision_multiple[index].gravity_multi = '';

                        this.offenseMultipleDisabled = true;
                        this.showOffenseMultiple = false;
                        this.showGravityMultiple = false;
                        this.showDescriptionMultiple = false;

                        /* TODO: multiple attendance points error*/
                        // this.dropdownInfraction();

                    } else {
                        this.offenseMultipleDisabled = false;

                        this.provision_multiple[index].offense_multi = '';
                        this.provision_multiple[index].offense_description_multi = '';
                        this.provision_multiple[index].gravity_multi = '';

                        this.showOffenseMultiple = true;
                        this.showGravityMultiple = true;
                        this.showDescriptionMultiple = true;

                        this.dropdownOffenseCategory(
                            this.provision_multiple[index].category_multi.value,
                            this.offense_remove,
                            index
                        );
                    }

                }
            },
            eventCocMultiple(index){
                this.showAddOffense = true;
                this.showDeleteOffense = true
                let respondent_object_to_array_multi = Object.keys(this.complainant.respondent).map((key) => [Number(key), this.complainant.respondent[key]['value']]);
                let respondent_user_array_multi = []
                for (var i = 0; i < respondent_object_to_array_multi.length; i++) {
                    respondent_user_array_multi.push(respondent_object_to_array_multi[i][1])
                }
                if(this.provision_multiple[index].offense_multi && this.provision_multiple[index].offense_multi.value != undefined  && this.complainant.respondent.length != 0){
                    this.offense_remove[index+1] = this.provision_multiple[index].offense_multi.value;
                    this.$constants.Coc_API.get("/offense/codeofconduct/multiple/"
                    +this.provision_multiple[index].offense_multi.value+"/"
                    +this.offense_remove+'/'
                    +respondent_user_array_multi)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.dropdownOffenseCategory(this.provision_multiple[index].category_multi.value, this.offense_remove);
                        this.provision_multiple[index].offense_description_multi = response.data.description
                        this.provision_multiple[index].gravity_multi = response.data.gravity.gravity
                        this.showAddOffense = true;
                        this.provision_multiple[index].instance = response.data.instance
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else if (this.provision_multiple[index].offense_multi && this.complainant.respondent.length == 0) {
                    this.$refs.vSelectMulti[index].clearSelection();
                    this.provision_multiple[index].offense_multi = '';
                    this.provision_multiple[index].offense_description_multi = '';
                    this.provision_multiple[index].gravity_multi = '';
                    this.showAddOffense = false;
                    this.$swal({
                        title: 'No Respondent Selected',
                        text: 'Please select respondent',
                        type: "error",
                        showConfirmButton: true
                    });
                } else if (this.provision_multiple[index].offense_multi == null && this.complainant.respondent.length == 0) {
                    this.provision_multiple[index].offense_multi = '';
                    this.provision_multiple[index].offense_description_multi = '';
                    this.provision_multiple[index].gravity_multi = '';
                    this.$refs.vSelectMulti[index].clearSelection();
                    this.showAddOffense = false;
                    this.$swal({
                        title: 'No Respondent Selected',
                        text: 'Please select respondent',
                        type: "error",
                        showConfirmButton: true
                    });
                } else if (this.provision_multiple[index].offense_multi == null && this.complainant.respondent.length > 0) {
                    this.$refs.vSelectMulti[index].clearSelection();
                    this.provision_multiple[index].offense_multi = '';
                    this.provision_multiple[index].offense_description_multi = '';
                    this.provision_multiple[index].gravity_multi = '';
                    this.showAddOffense = false;
                    this.complainant.instance = '';
                }
            },
            eventImageSize(descriptionElement) {
                if (descriptionElement.indexOf(".webp") > 0 || descriptionElement.indexOf(".gif") > 0 || descriptionElement.indexOf(".tiff") > 0
                || descriptionElement.indexOf(".mp4") > 0 || descriptionElement.indexOf(".mp3") > 0){
                    this.$swal({
                        title: 'Invalid File Format',
                        text: 'Please use (.jpeg .png .jpg)',
                        type: "error",
                        showConfirmButton: true
                    }).then((result) => {
                        this.complainant.description = "";
                    });
                }
            }
        },
    }
</script>
