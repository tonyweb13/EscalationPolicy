<template>
    <div>
        <the-form :asterisk="true" @submitForm="eventUpdateRespondent">
            <div slot="form-body">
                <table class="table borderless">
                    <tbody>
                    <tr>
                        <td>
                            <strong>IR Number : </strong> {{ respondent.ir_number }}
                        </td>
                        <td>
                            <strong>Date Reported : </strong> {{ respondent.reported | formatDate }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Instance : </strong>
                            <div v-if="respondent.single_instance">
                                {{ respondent.single_instance }}
                            </div>
                            <div v-else>
                                <span v-for="(off_multi, index) in respondent.multiple_instance" :key="index">
                                    <strong>Offense ( {{ index+1 }} ):</strong> {{ off_multi }}<br />
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Provision : </strong>
                            <div v-if="respondent.offense_multiple === ''">
                                {{ respondent.category }}
                            </div>
                            <div v-else>
                                <span v-for="(off_multi, index) in respondent.offense_multiple" :key="index">
                                    <strong>Offense ( {{ index+1 }} ):</strong> {{ off_multi.category.name }}<br />
                                </span>
                            </div>
                        </td>
                        <td>
                            <div v-if="respondent.witness">
                                <strong v-if="respondent.witness">Witness : </strong>
                                <span v-for="witnesses in respondent.witness">
                                    {{ witnesses.witness_fullname.first_name }} {{ witnesses.witness_fullname.last_name }}<br>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Gravity : </strong>
                            <div v-if="respondent.offense_multiple === ''">
                                {{ respondent.gravity }}
                            </div>
                            <div v-else>
                                <span v-for="(off_multi, index) in respondent.offense_multiple" :key="index">
                                    <strong>Offense ( {{ index+1 }} ):</strong> {{ off_multi.gravity.gravity }}<br />
                                </span>
                            </div>
                        </td>
                        <td>
                            <strong>Prescriptive Period : </strong>
                            <div v-if="respondent.offense_multiple === ''">
                                {{ respondent.prescriptive_period }}
                            </div>
                            <div v-else>
                                <span v-for="(off_multi, index) in respondent.offense_multiple" :key="index">
                                    <strong>Offense ( {{ index+1 }} ):</strong> {{ off_multi.gravity.prescriptive_period }}<br />
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- <td>
                            <strong>Complainant : </strong>
                            <span v-if="respondent.complainant">
                                {{ respondent.complainant }}
                            </span>
                            <span v-else-if="user.first_name != ''  && user.last_name != '' ">
                                {{ user.first_name }}
                                {{ user.last_name }}
                            </span>
                        </td> -->
                        <td colspan="2">
                            <strong>Date Incident : </strong>
                            {{ respondent.date_incident_start }}
                            <span v-if="respondent.date_incident_end">
                                - {{ respondent.date_incident_end }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Offense : </strong>
                            <div v-if="respondent.offense_multiple === ''">
                                {{ respondent.offense }}
                            </div>
                            <div v-else>
                                <span v-for="(off_multi, index) in respondent.offense_multiple" :key="index">
                                    <strong>Offense ( {{ index+1 }} ):</strong> {{ off_multi.offense }}<br />
                                </span>
                            </div>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td colspan="2">
                            <strong>Date Incident : </strong>
                            {{ respondent.date_incident_start }}
                            <span v-if="respondent.date_incident_end">
                                - {{ respondent.date_incident_end }}
                            </span>
                        </td>
                    </tr> -->
                    <tr>
                        <td colspan="2">
                            <strong>IR Description : </strong><br>
                            <div v-html="respondent.offense_description"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Complainant Description : </strong><br>
                            <div v-html="respondent.description"></div>
                        </td>
                    </tr>
                    <tr v-if="respondent.notes">
                        <td colspan="2">
                            <strong>Notes : </strong><br>
                            {{ respondent.notes }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <!-- TODO: added radio button, will disabled textarea if select #1  -->
                            <!-- 1. Will not further explain -->
                            <!-- 2. Respond with explanation -->
                        <the-label :asterisk="true">
                            <label-radio>
                                <input type="radio"
                                    name="respondent_reply"
                                    id="respondent_reply1"
                                    @input="eventRespondentReply('Will not further explain')"
                                    v-model="respondent.is_respondent_reply"
                                    value='Will not further explain'
                                >
                                <label for="respondent_reply1"><b> Will not further explain </b></label>
                            </label-radio>
                            <label-radio>
                                <input type="radio"
                                    label="Respond with explanation"
                                    name="respondent_reply"
                                    id="respondent_reply2"
                                    @input="eventRespondentReply('Respond with explanation')"
                                    v-model="respondent.is_respondent_reply"
                                    value='Respond with explanation'
                                >
                                <label for="respondent_reply2"><b> Respond with explanation </b></label>
                            </label-radio>

                            <!-- Replace textarea to remove extra blank pages in DA -->
                            <!-- <vue-editor
                                v-model="respondent.current_response"
                                :editorToolbar="customToolbar"
                                v-show="showExplanation"
                            /> -->

                            <input-textarea v-model="respondent.current_response" v-show="showExplanation"/>

                            <input-file-uploader label="Attachments" @change="attachFile"
                            v-show="showExplanation" />

                        </the-label>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button-save :disabled='!isComplete' />
            </div>
        </the-form>
        <hr style="border: 1px solid;">
        <table>
            <tr>
                <td colspan="2">
                    <button class='btn btn-success btn-xs'
                        @click="eventGenerateComplainant(respondent.complainant_id, respondent.respondent_user_id)">
                        <i class="fa fa-download"></i>
                        Download Complainant
                    </button>
                </td>
            </tr>
        </table>
    </div>
</template>
<script>

    export default {
        props: {
            respondentProps: Object,
        },
        data: function () {
            return {
                countRespondent: 0,
                respondent: {
                    offense_multiple: [],
                    complainant_id: '',
                    reported: '',
                    ir_number: '',
                    complainant: '',
                    offense: '',
                    category: '',
                    gravity: '',
                    description: '',
                    prescriptive_period: '',
                    date_incident_start: '',
                    date_incident_end: '',
                    offense_description: '',
                    attachments: '',
                    witness: '',
                    notes: '',
                    response: '',
                    hrbp_acknowledge_response: '',
                    first_offense: '',
                    second_offense: '',
                    third_offense: '',
                    fourth_offense: '',
                    fifth_offense: '',
                    sixth_offense: '',
                    seventh_offense: '',
                    is_respondent_reply: '',
                    progression_occurence: '',
                    respondent_user_id: '',
                    respondent_attachments: []
                },
                showExplanation: false,
                form: new FormData,
            };
        },
        created(){
            if(this.respondentProps.res_id){
                if (this.respondentProps.comp_complainant) {
                    this.respondent.complainant = this.respondentProps.comp_complainant;
                }
                this.respondent.respondent_id = this.respondentProps.res_id;
                this.respondent.reported = this.respondentProps.comp_reported;
                this.respondent.ir_number = this.respondentProps.comp_ir_number;
                this.respondent.offense = this.respondentProps.comp_offense;
                this.respondent.category = this.respondentProps.comp_category;
                this.respondent.gravity = this.respondentProps.comp_gravity;
                this.respondent.description = this.respondentProps.comp_description;
                this.respondent.prescriptive_period = this.respondentProps.comp_prescriptive_period;
                this.respondent.date_incident_start = this.respondentProps.comp_date_incident_start;
                this.respondent.date_incident_end = this.respondentProps.comp_date_incident_end;
                this.respondent.offense_description = this.respondentProps.comp_offense_description;
                this.respondent.attachments = this.respondentProps.comp_attachments;
                this.respondent.hrbp_acknowledge_response = this.respondentProps.comp_respondent_hrbp_acknowledge_response;
                this.respondent.response = this.respondentProps.comp_respondent_response;
                this.respondent.second_response = this.respondentProps.comp_respondent_second_response;
                this.respondent.witness = this.respondentProps.comp_witness;
                this.respondent.single_instance = this.respondentProps.comp_single_instance;
                this.respondent.progression_occurence = this.respondentProps.comp_progress_occurence;
                this.respondent.complainant_id = this.respondentProps.comp_id;
                this.respondent.respondent_user_id = this.respondentProps.respondent_user_id;

                if (this.respondentProps.comp_multiple_offense.length > 0) {
                    for(let c=0; c < this.respondentProps.comp_multiple_offense.length; c++){
                        this.findMultipleOffense(this.respondentProps.comp_multiple_offense[c]);
                    }
                    if (this.respondentProps.comp_multiple_instance) {
                        this.respondent.multiple_instance = JSON.parse(this.respondentProps.comp_multiple_instance.replace(/'/g,''));
                    }
                } else {
                    this.respondent.offense_multiple = '';
                }
                if(this.respondentProps.comp_notes){
                    this.respondent.notes = this.respondentProps.comp_notes;
                }
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if(this.respondent.is_respondent_reply == 'Will not further explain'){
                    return this.respondent.is_respondent_reply
                } else {
                    return this.respondent.current_response;
                }
            }
        },
        methods: {
            eventUpdateRespondent() {
                this.laddabtn.start();

                let respondent = this.respondent;
                let getAxios = '';

                if(this.respondent.response == 'Will not further explain' || this.respondent.response == '<p>Will not further explain</p>'){
                    if (this.respondent.is_respondent_reply == 'Will not further explain') {
                        this.respondent.second_response = this.respondent.is_respondent_reply
                    } else {
                        this.respondent.second_response = this.respondent.current_response;
                    }
                } else {
                    if (this.respondent.is_respondent_reply == 'Will not further explain') {
                        this.respondent.response = this.respondent.is_respondent_reply
                    } else {
                        this.respondent.response = this.respondent.current_response;
                    }
                }

                for(let r=0; r<this.respondent.respondent_attachments.length;r++) {
                    this.form.append('pics[]', this.respondent.respondent_attachments[r]);
                }
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }

                this.$constants.Default_API.post('/respondent/attach/file', this.form, config)
                    .then(response => {

                    respondent.respondent_attachments = response.data.uploaded

                    if(this.respondentProps.comp_id){
                        getAxios = this.$constants.Default_API.get("/respondent/update/response/"+this.respondentProps.res_id, {
                            params: {
                            complainant_id: respondent.complainant_id,
                            response : respondent.response,
                            second_response : respondent.second_response,
                            respondent_attachments: respondent.respondent_attachments,
                            }
                        });
                    } else {
                        // TODO: check this if functional.
                        getAxios = this.$constants.Default_API.get("/respondent/create", {
                            params: {
                                complainant_id: respondent.complainant_id,
                                response: respondent.response,
                                respondent_attachments: respondent.respondent_attachments,
                            }
                        });
                    }

                    getAxios.then(response => {
                        this.laddabtn.stop();
                        this.$bus.$emit('init_modal', false);
                        this.respondent = response.data;
                        this.$bus.$emit('changeRespondentCount', this.countRespondent);
                        this.$success('Successfully Saved!')
                        this.$bus.$emit('updateList');
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                })
                .catch(error => { //end attachments
                    this.globalErrorHandling(error);
                });
            },
            eventRespondentReply(isExplain) {
                if(isExplain == 'Will not further explain'){
                    this.showExplanation = false;
                } else {
                    this.showExplanation = true;
                }
            },
            findMultipleOffense: function (offense_id) {
                this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                .then(response => {
                    this.respondent.offense_multiple.push(response.data);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventGenerateComplainant: function (complainant_id, respondent_user) {
                this.$swal({
                    title: 'Do You Want To Download this Complainant?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            window.location.href = "/api/admin/incidentreport/generate/complainant/"
                            + complainant_id + "/" + respondent_user;
                            this.$success('Download Complaint downloaded successfully!');
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Download Complaint has been cancelled');
                        }
                    })
            },
            attachFile(e){
                this.respondent.respondent_attachments = [];
                let selectedFiles = e.target.files;
                console.log("selectedFiles==", selectedFiles);

                if(!selectedFiles.length){
                    return false;
                }

                for(let i=0; i<selectedFiles.length;i++){
                    if (selectedFiles[i].size < 25000000) {
                        this.respondent.respondent_attachments.push(selectedFiles[i]);
                    } else {
                        this.$swal({
                            title: 'Invalid File Size',
                            text: 'Please upload less than 25 MB',
                            type: "error",
                            showConfirmButton: true
                        }).then((result) => {
                            document.getElementById("file-upload").value = "";
                            this.respondent.respondent_attachments = [];
                        });
                    }
                }
            },
        },
    }
</script>
