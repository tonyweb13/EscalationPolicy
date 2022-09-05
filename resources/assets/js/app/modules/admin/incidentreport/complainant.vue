<template>
    <table class="table borderless">
        <tbody>
        <tr>
            <td>
                <strong>IR Number: </strong> {{ complainant.ir_number }}
            </td>
            <td>
                <strong>Date Reported : </strong> {{ complainant.reported | formatDate }}
            </td>
        </tr>
        <tr>
            <td>
                <div v-if="complainant.respondent_user_id != user.id">
                    <strong>Complainant : </strong> {{ complainant.complainant }}
                </div>
            </td>
            <td>
                <strong>Date Incident : </strong>
                {{ complainant.date_incident_start }}
                <span v-if="complainant.date_incident_end">
                    - {{ complainant.date_incident_end }}
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Respondent : </strong> {{ complainant.respondent }}
            </td>
            <td>
                <div v-if="complainant.witness">
                    <strong>Witness : </strong>
                    <span v-for="witnesses in complainant.witness">
                        {{ witnesses.witness_fullname.first_name }}
                        {{ witnesses.witness_fullname.last_name }}<br>
                    </span>
                    </div>
            </td>
        </tr>

        <tr v-if="complainant.first_offense || complainant.second_offense">
            <td>
                <div v-if="complainant.first_offense">
                    <strong>First Offense IR No. : </strong> {{ complainant.first_offense }}
                </div>
            </td>
            <td>
                <div v-if="complainant.second_offense">
                    <strong>Second Offense IR No. : </strong> {{ complainant.second_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="complainant.third_offense || complainant.fourth_offense">
            <td>
                <div v-if="complainant.third_offense">
                    <strong>Third Offense IR No. : </strong> {{ complainant.third_offense }}
                </div>
            </td>
            <td>
                <div v-if="complainant.fourth_offense">
                    <strong>Fourth Offense IR No. : </strong> {{ complainant.fourth_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="complainant.fifth_offense || complainant.sixth_offense">
            <td>
                <div v-if="complainant.fifth_offense">
                    <strong>Fifth Offense IR No. : </strong> {{ complainant.fifth_offense }}
                </div>
            </td>
            <td>
                <div v-if="complainant.sixth_offense">
                    <strong>Sixth Offense IR No: </strong> {{ complainant.sixth_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="complainant.seventh_offense">
            <td colspan="2">
                <div v-if="complainant.seventh_offense">
                    <strong>Seventh Offense IR No: </strong> {{ complainant.seventh_offense }}
                </div>
            </td>
        </tr>

        <tr v-for="(off_multi, index) in complainant.offense_multiple" :key="index">
            <td colspan="2">
                <fieldset class="fieldsetCustom">
                    <legend class="legendCustom">Offense ( {{ index+1 }} )</legend>
                    <table class="table borderless">
                        <tr>
                            <td colspan="2">
                                <strong>Provision : </strong> {{ off_multi.category.name }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Gravity : </strong> {{ off_multi.gravity.gravity }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Prescriptive Period : </strong>
                                {{ off_multi.gravity.prescriptive_period }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Offense : </strong>
                                {{ off_multi.offense }}
                                <strong style="color:red;" v-if="off_multi.offense_archived">
                                    {{ off_multi.offense_archived }}
                                </strong>
                            </td>
                        </tr>
                        <tr v-if="role != 'Regular User Access'
                            && designation.toLowerCase().indexOf('supervisor') < 0
                            && designation.toLowerCase().indexOf('team') < 0">
                            <td colspan="2">
                                <strong v-if="complainant.multiple_irr_array[index]">
                                    Initial Corrective Action:
                                    {{ complainant.multiple_irr_array[index] }}
                                </strong><br />
                                <strong v-if="complainant.multiple_instance[index]">
                                    Initial Instance: {{ complainant.multiple_instance[index] }}
                                </strong>
                            </td>
                        </tr>
                        <tr v-if="complainant.multiple_progression_occurence">
                            <td colspan="2">
                                <div v-for="off_multi_first in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_first.offense_id
                                    && off_multi_first.first_respondent_id">
                                        <strong>First Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_first.first_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                                <div v-for="off_multi_second in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_second.offense_id
                                    && off_multi_second.second_respondent_id">
                                        <strong>Second Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_second.second_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                                <div v-for="off_multi_third in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_third.offense_id
                                    && off_multi_third.third_respondent_id">
                                        <strong>Third Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_third.third_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                                <div v-for="off_multi_fourth in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_fourth.offense_id
                                    && off_multi_fourth.fourth_respondent_id">
                                        <strong>Fourth Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_fourth.fourth_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                                <div v-for="off_multi_fifth in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_fifth.offense_id
                                    && off_multi_fifth.fifth_respondent_id">
                                        <strong>Fifth Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_fifth.fifth_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                                <div v-for="off_multi_sixth in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_sixth.offense_id
                                    && off_multi_sixth.sixth_respondent_id">
                                        <strong>Sixth Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_sixth.sixth_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                                <div v-for="off_multi_seventh in complainant.multiple_progression_occurence">
                                    <div v-if="off_multi.id == off_multi_seventh.offense_id
                                    && off_multi_seventh.seventh_respondent_id">
                                        <strong>Seventh Offense IR No. : </strong>
                                        {{ String("000000000" + off_multi_seventh.seventh_respondent_id).slice(-9) }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr v-if="complainant.category">
            <td colspan="2">
                <strong>Provision : </strong> {{ complainant.category }}
            </td>
        </tr>
        <tr v-if="complainant.gravity">
            <td>
                <strong>Gravity : </strong> {{ complainant.gravity }}
            </td>
            <td>
                <strong>Prescriptive Period : </strong> {{ complainant.prescriptive_period }}
            </td>
        </tr>
        <tr v-if="complainant.offense">
            <td colspan="2">
                <strong>Offense : </strong> {{ complainant.offense }}
                <strong style="color:red;" v-if="complainant.archived">
                    {{ complainant.archived }} </strong>
            </td>
        </tr>
        <tr v-if="complainant.offense_description">
            <td colspan="2">
                <strong>IR Description : </strong><br>
                <div v-html="complainant.offense_description"/>
            </td>
        </tr>
        <tr v-if="complainant.remarks">
            <td colspan="2">
                <strong>Remarks : </strong><br>
                {{ complainant.remarks }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Complainant Description : </strong><br>
                <div v-html="complainant.description"/>
            </td>
        </tr>
        <tr v-if="complainant.respondent_response && complainant.date_response">
            <td colspan="2">
                    <strong v-if="complainant.date_response">Date Response : </strong><br>
                    <div v-html="complainant.date_response" v-if="complainant.date_response"/>
                    <strong>Respondent Reply : </strong><br>
                    <div v-html="complainant.respondent_response"/>
                <div v-if="complainant.second_date_response">
                    <strong v-if="complainant.second_date_response">Second Date Response : </strong>
                    <br>
                    <div v-html="complainant.second_date_response"
                    v-if="complainant.second_date_response"/>
                    <strong>Second Respondent Reply : </strong><br>
                    <div v-html="complainant.second_respondent_response"/>
                </div>

                <!-- attachment-->
                <strong v-if="downloadRespondentAttach">
                    {{ downloadRespondentAttach }}
                    <br><br>
                </strong>
                <div v-for="attach1 in complainant.respondent_attachments">
                    <ul v-for="attach2 in attach1.attachments.split(',')"
                    v-if='attach1.attachments'>
                        <li v-if="attach2.match(/respondent/g)">
                            <a :href="'/api/respondent/download/attachment/'
                                + attach2 | urlReplace">
                                {{ attach2 | urlReplace }}
                            </a>
                            <span v-if="showLabelDownloadAttachRespondent(attach2)"/>
                        </li>
                    </ul>
                </div>

                <button class='btn btn-success btn-xs'
                    @click="acknowledge(complainant.ir_number)"
                    v-if="!complainant.hrbp_acknowledge_response && complainant.respondent_response
                    && (role == 'Super Admin Access' || role == 'HR Admin Access'
                    || role == 'HR Access')"
                >
                   <i class="fa fa-eye"></i>
                   Acknowledge
                </button>

                <button class='btn btn-success btn-xs'
                    @click="acknowledge(complainant.ir_number)"
                    v-if="complainant.hrbp_acknowledge_response && complainant.respondent_response"
                    :disabled="true"
                >
                   <i class="fa fa-eye"></i>
                   Acknowledged
                </button>
            </td>
        </tr>
        <tr v-else>
            <td colspan="2">
                <button class='btn btn-primary btn-xs'
                v-if="complainant.reply_reactivate == null && complainant.ageing > 5 &&
                (role == 'Super Admin Access' || role == 'HR Admin Access' || role == 'HR Access')"
                @click="replyReactivate(complainant.ir_number)" >
                   <i class="fa fa-send"></i>
                   Reply Reactivate in 2 Days
                </button>
            </td>
        </tr>
        <tr>
            <td colspan="2" v-if="complainant.attachments">
                <div v-for="attach1 in complainant.attachments">
                    <strong v-if="downloadComplainantAttachment && attach1.attachments != 'null'">
                        Download Complainant Attachment/s :
                        <br><br>
                    </strong>
                    <ul v-for="attach2 in attach1.attachments.split(',')">
                        <li v-if="attach2.match(/complainants/g)">
                            <a :href="'/api/admin/incidentreport/download/attachment/'
                            + attach2 | urlReplace">
                                {{ attach2 | urlReplace }}
                            </a>
                            <span v-if="showLabelDownloadAttach(attach2)"/>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr v-if="((complainant.initial_irr || complainant.initial_instance) ||
            (!complainant.initial_irr.length > 0 || !complainant.initial_instance.length > 0))
                && role != 'Regular User Access'
                && designation.toLowerCase().indexOf('supervisor') < 0
                && designation.toLowerCase().indexOf('team') < 0">
            <td v-if="complainant.initial_instance">
                <strong>HRBP Initial Instance : </strong>{{ complainant.initial_instance }}
            </td>
            <td v-if="complainant.initial_irr">
                <strong>HRBP Initial Case Closure : </strong>{{ complainant.initial_irr }}
                <strong style="color:red;" v-if="complainant.irr_exist">
                    {{ complainant.irr_exist }}
                </strong>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button class='btn btn-success btn-xs'
                    @click="eventGenerateComplainant(complainant.complainant_id,
                    complainant.respondent_user_id)">
                    <i class="fa fa-download"></i>
                    Download Complainant
                </button>
            </td>
        </tr>
        </tbody>
    </table>

</template>
<script>
export default {
    props: {
        complainantViewProps: Object,
    },
    data: function () {
        return {
            role: this.$ep.role,
            user: this.$ep.user,
            designation: this.$ep.user.designation.name,
            complainant: {
                archived: '',
                irr_exist: '',
                date_response: '',
                complainant_id: '',
                reported: '',
                ir_number: '',
                complainant: '',
                respondent: '',
                respondent_user_id: '',
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
                remarks: '',
                respondent_response: '',
                hrbp_acknowledge_response: '',
                res_id: '',
                first_offense: '',
                second_offense: '',
                third_offense: '',
                fourth_offense: '',
                fifth_offense: '',
                sixth_offense: '',
                seventh_offense: '',
                offense_id: '',
                offense_multiple: [],
                initial_irr: '',
                initial_instance: '',
                multiple_irr: [],
                multiple_irr_array: [],
                multiple_instance: [],
                second_date_response: '',
                second_respondent_response: '',
                multiple_progression_occurence: [],
                multiple_first_offense: [],
                respondent_attachments: [],
                ageing: '',
                reply_reactivate: ''
            },
            selectedIRR: [],
            downloadComplainantAttachment: '',
            downloadRespondentAttach: '',
        };
    },
    created(){
        if(this.complainantViewProps.complainant_id){
            this.complainant.respondent_user_id = this.complainantViewProps.respondent_user_id;
            this.complainant.date_response = this.complainantViewProps.date_response;
            this.complainant.complainant_id = this.complainantViewProps.complainant_id;
            this.complainant.reported = this.complainantViewProps.reported;
            this.complainant.ir_number = this.complainantViewProps.ir_number;
            this.complainant.complainant = this.complainantViewProps.complainant;
            this.complainant.respondent = this.complainantViewProps.respondent;
            this.complainant.description = this.complainantViewProps.description;
            this.complainant.prescriptive_period = this.complainantViewProps.prescriptive_period;
            this.complainant.date_incident_start = this.complainantViewProps.date_incident_start;
            this.complainant.date_incident_end = this.complainantViewProps.date_incident_end;
            this.complainant.offense_description = this.complainantViewProps.offense_description;
            this.complainant.remarks = this.complainantViewProps.remarks;
            this.complainant.respondent_response = this.complainantViewProps.respondent_response
            this.complainant.hrbp_acknowledge_response = this.complainantViewProps.hrbp_acknowledge_response
            this.complainant.witness = this.complainantViewProps.witness;
            this.complainant.is_generate_nte_invalid_ir = this.complainantViewProps.is_generate_nte_invalid_ir;

            this.complainant.first_offense = this.complainantViewProps.first_offense;
            this.complainant.second_offense = this.complainantViewProps.second_offense;
            this.complainant.third_offense = this.complainantViewProps.third_offense;
            this.complainant.fourth_offense = this.complainantViewProps.fourth_offense;
            this.complainant.fifth_offense = this.complainantViewProps.fifth_offense;
            this.complainant.sixth_offense = this.complainantViewProps.sixth_offense;
            this.complainant.seventh_offense = this.complainantViewProps.seventh_offense;
            this.complainant.attachments = this.complainantViewProps.complainant_attachments;
            this.complainant.offense_id = this.complainantViewProps.offense_id;
            this.complainant.offense = this.complainantViewProps.offense;
            this.complainant.category = this.complainantViewProps.category;
            this.complainant.gravity = this.complainantViewProps.gravity;
            this.complainant.archived = this.complainantViewProps.archived;
            this.complainant.irr_exist = this.complainantViewProps.irr_exist;
            this.complainant.second_date_response = this.complainantViewProps.second_date_response;
            this.complainant.ageing = this.complainantViewProps.ageing;
            this.complainant.reply_reactivate = this.complainantViewProps.reply_reactivate;
            this.complainant.second_respondent_response =
            this.complainantViewProps.second_respondent_response
            this.complainant.respondent_attachments =
            this.complainantViewProps.respondent_attachments


            if (this.complainantViewProps.initial_irr_id
            && JSON.parse(this.complainantViewProps.initial_irr_id).length > 0) {
                let jsonIRRChecker =
                this.complainantViewProps.initial_irr_id.replace(/'/g,'').includes('[');

                if (!jsonIRRChecker) {
                    this.$constants.Coc_API.get("/dropdown/irr")
                        .then(response => {
                        this.selectedIRR = response.data;
                        for (let s = 0; s < this.selectedIRR.length; s++) {
                            if (this.selectedIRR[s].value == this.complainantViewProps.initial_irr_id) {
                                this.complainant.initial_irr = response.data[s].text;
                            }
                        }
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else {

                    if (JSON.parse(this.complainantViewProps.initial_irr_id).length > 0) {
                        let multiple_irr_first =
                        this.complainantViewProps.initial_irr_id.replace(/\\|"/gi,'');
                        let multiple_irr_second = multiple_irr_first.replace(']','');
                        let multiple_irr_array = multiple_irr_second.replace('[','');
                        this.complainant.multiple_irr = multiple_irr_array.split(',');
                    }
                    for(let c=0; c < this.complainant.multiple_irr.length; c++){
                        this.findMultipleIrr(this.complainant.multiple_irr[c]);
                    }
                }
            } else {
                this.$constants.Coc_API.get("/dropdown/irr")
                        .then(response => {
                        this.selectedIRR = response.data;
                        for (let s = 0; s < this.selectedIRR.length; s++) {
                            if (this.selectedIRR[s].value ==
                            this.complainantViewProps.initial_irr_id)
                            {
                                this.complainant.initial_irr = response.data[s].text;
                            }
                        }
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

            }

            let jsonInstanceChecker =
            this.complainantViewProps.initial_instance.replace(/'/g,'').includes('[');
            if (!jsonInstanceChecker) {
                this.complainant.initial_instance = this.complainantViewProps.initial_instance;
            } else {

                if (JSON.parse(this.complainantViewProps.initial_instance).length > 0) {
                    this.complainant.multiple_instance =
                    JSON.parse(this.complainantViewProps.initial_instance.replace(/'/g,''));
                }
            }

            /* get complainant multiple offense */
            if (this.complainant.offense_id) {
                // for(let c=0; c < this.complainant.offense_id.length; c++){
                    for (let c in this.complainant.offense_id){
                    this.findMultipleOffense(this.complainant.offense_id[c]);
                    this.complainant.multiple_progression_occurence =
                    this.complainantViewProps.progression_occurence_multi
                }
            }
        }
    },
    methods:{
        acknowledge: function (respondent) {
            this.$constants.Default_API.get("/respondent/update/hrbp_acknowledge_response/"
            +respondent)
            .then(response => {
                this.$swal({
                    title: 'Successfully Acknowledge',
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                })
                this.complainant.hrbp_acknowledge_response = response
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
                        this.$success('Complainant downloaded successfully!');
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$failure('Download Complaint has been cancelled');
                    }
                })
        },
        // findMultipleOffense: function (offense_id) {
        //     this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
        //     .then(response => {
        //         this.complainant.offense_multiple.push(response.data);
        //     })
        //     .catch(error => {
        //         this.globalErrorHandling(error);
        //     });
        // },
        findMultipleOffense: function (offense_id) {

                this.$constants.Coc_API.get("/offense/progression/"+offense_id+"/"
                +this.complainantViewProps.respondent_user_id)
                .then(response => {

                    let instance_multiple = [];
                    let case_closure_multiple = [];
                    if (response.data == 'No occurrence') {

                        this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                        .then(respo => {
                            this.complainant.offense_multiple.push(respo.data);
                            this.complainant.multiple_instance.push("1st Instance")
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });

                    } else if (response.data.seventh_respondent_id) {
                        instance_multiple = "7th Instance";
                    } else if (response.data.sixth_respondent_id) {
                        instance_multiple = "7th Instance";
                    } else if (response.data.fifth_respondent_id) {
                        instance_multiple = "6th Instance";
                    } else if (response.data.fourth_respondent_id) {
                        instance_multiple = "5th Instance";
                    } else if (response.data.third_respondent_id) {
                        instance_multiple = "4th Instance";
                    } else if (response.data.second_respondent_id) {
                        instance_multiple = "3rd Instance";
                    } else if (response.data.first_respondent_id) {
                        instance_multiple = "2nd Instance";
                    }

                    if (response.data.offense.offense) {
                        case_closure_multiple = response.data.offense;
                    }

                    this.complainant.multiple_instance.push(instance_multiple)
                    this.complainant.offense_multiple.push(case_closure_multiple);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

        },
        findMultipleIrr: function (irr_id) {
            this.$constants.Coc_API.get("/dropdown/irr")
                .then(response => {
                this.selectedIRR = response.data;
                for (let s = 0; s < this.selectedIRR.length; s++) {
                    if (this.selectedIRR[s].value == irr_id) {
                        this.complainant.multiple_irr_array.push(response.data[s].text);
                    }
                }
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        showLabelDownloadAttach(attach){
            this.downloadComplainantAttachment = 'Download Complainant Attachment :';
        },
        showLabelDownloadAttachRespondent(attach){
            this.downloadRespondentAttach = 'Download Respondent Attachments :';
        },
        replyReactivate: function (ir_number) {
            this.$swal({
                title: 'Do You Want Extend Reply in 2 Days?',
                type: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
            })
            .then((result) => {
                if (result.value) {
                    this.$constants.Default_API.get("/respondent/reply/reactivate/" + ir_number)
                    .then(respo => {
                        if (respo) {
                            this.$success('Reply Reactivate Successfully!');
                            window.location.href = '/incidentreport'
                        }
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                    this.$failure('Reply Reactivate has been cancelled');
                }
            })
        },
    },
}
</script>
