<template>
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
            <td>
                <strong>Respondent : </strong>
                <span v-if="respondent.complainant">
                    {{ respondent.complainant }}
                </span>
                <span v-else>
                    {{ user.first_name }}
                    {{ user.last_name }}
                </span>
            </td>
            <td>
                <strong>Date Incident : </strong>
                {{ respondent.date_incident_start }}
                <span v-if="respondent.date_incident_end">
                    - {{ respondent.date_incident_end }}
                </span>
            </td>
        </tr>

        <!-- <tr v-if="respondent.first_offense || respondent.second_offense">
            <td>
                <div v-if="respondent.first_offense">
                    <strong>First Offense : </strong> {{ respondent.first_offense }}
                </div>
            </td>
            <td>
                <div v-if="respondent.second_offense">
                    <strong>Second Offense : </strong> {{ respondent.second_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="respondent.third_offense || respondent.fourth_offense">
            <td>
                <div v-if="respondent.third_offense">
                    <strong>Third Offense : </strong> {{ respondent.third_offense }}
                </div>
            </td>
            <td>
                <div v-if="respondent.fourth_offense">
                    <strong>Fourth Offense : </strong> {{ respondent.fourth_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="respondent.fifth_offense || respondent.sixth_offense">
            <td>
                <div v-if="respondent.fifth_offense">
                    <strong>Fifth Offense : </strong> {{ respondent.fifth_offense }}
                </div>
            </td>
            <td>
                <div v-if="respondent.sixth_offense">
                    <strong>Sixth Offense : </strong> {{ respondent.sixth_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="respondent.seventh_offense">
            <td colspan="2">
                <div v-if="respondent.seventh_offense">
                    <strong>Seventh Offense : </strong> {{ respondent.seventh_offense }}
                </div>
            </td>
        </tr> -->

        <tr>
            <td>
                <strong>Provision : </strong> {{ respondent.category }}
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
        <tr v-if="respondent.prescriptive_period">
            <td>
                <strong>Gravity : </strong> {{ respondent.gravity }}
            </td>
            <td>
                <strong>Prescriptive Period : </strong> {{ respondent.prescriptive_period }} Days
            </td>
        </tr>
        <tr v-else>
            <td colspan="2">
                <strong>Gravity : </strong> {{ respondent.gravity }}
            </td>
        </tr>

        <tr v-if="respondent.offense">
            <td colspan="2">
                <strong>Offense : </strong> {{ respondent.offense }}
            </td>
        </tr>

        <tr v-if="respondent.offense_description">
            <td colspan="2">
                <strong>IR Description : </strong><br>
                <div v-html="respondent.offense_description"/>
            </td>
        </tr>
        <!-- <tr v-else-if="respondent.attendancepointsssytem">
            <td>
                <strong>Type of Infraction : </strong><br>
                <div v-html="respondent.infraction"/> -
                <div v-html="respondent.attendance_points"/>
            </td>
            <td>
                <strong>Definition : </strong><br>
                <div v-html="respondent.definition"/>
            </td>
        </tr> -->

        <tr v-if="respondent.remarks">
            <td colspan="2">
                <strong>Complainant Remarks : </strong><br>
                {{ respondent.remarks }}
            </td>
        </tr>
        <tr v-if="respondent.description">
            <td colspan="2">
                <strong>Complainant Description : </strong><br>
                <div v-html="respondent.description"/>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div v-for="attach1 in respondent.attachments">
                    <strong v-if="attach1.attachments != 'null'">
                        Download Complainant Attachments :
                        <br><br>
                    </strong>
                    <ul v-for="attach2 in attach1.attachments.split(',')">
                        <li v-if="attach2.match(/complainants/g)">
                            <a :href="'/api/admin/incidentreport/download/attachment/'+ attach2 | urlReplace">
                                {{ attach2 | urlReplace }}
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong v-if="respondent.response">
                Respondent Reply : </strong><br>
                <div v-html="respondent.response"/>
                <label
                    v-if="respondent.response !== null && respondent.hrbp_acknowledge_response"
                >
                    <b class="text-success"><i class="fa fa-check text-success"></i> Acknowledge By HRBP </b>
                </label>
            </td>
        </tr>
        <tr v-if="respondent.incident_report">
            <td colspan="2">
                <strong>HR Details : </strong><br>
                <ul>
                    <li v-if="respondent.incident_report.hrbp_user">
                        <i>HRBP</i>:
                        <span v-if="respondent.incident_report">
                            {{ respondent.incident_report.hrbp_user.first_name }}
                            {{ respondent.incident_report.hrbp_user.last_name }}
                        </span>
                        <span v-else>
                            {{ respondent.hrbp_user.first_name }}
                            {{ respondent.hrbp_user.last_name }}
                        </span>
                    </li>
                    <li>
                        <i>Status</i>:
                        <span v-for="stats in respondent.status_constant" >
                            <span v-if="respondent.status == stats.value">
                                {{ stats.text }}
                            </span>
                        </span>
                    </li>
                    <li><i>Date Hearing</i>: {{ respondent.incident_report.date_admin_hearing }}</li>
                    <li><i>Remarks</i>: <br>
                        <div v-html="respondent.incident_report.remarks" />
                    </li>
                    <li><i>HR Witness</i>:
                        <span v-for="witness in respondent.incident_report.witness_user">
                            {{ witness.witness_fullname.first_name }} {{ witness.witness_fullname.last_name }},
                        </span>
                    </li>
                    <li><i>HR Invite</i>:
                        <span v-for="invite in respondent.incident_report.invite_user">
                            {{ invite.invite_fullname.first_name }} {{ invite.invite_fullname.last_name }},
                        </span>
                    </li>
                </ul>
            </td>
        </tr>
        <!-- <tr v-if="respondent.ir_id">
            <td colspan="2">
                <button class="ladda-button btn btn-primary" data-style="slide-right" data-color="mint"
                type="submit" @click="approveEvent(respondent, 1)" :disabled="showApproval">
                    <i v-if="respondent.supervisor_approval = 1" class="fa fa-check"/>
                    <i v-else class="fa fa-thumbs-up"/>
                    Approve
                </button>
                <button class="ladda-button btn btn-danger" data-style="slide-right" data-color="mint" type="submit"
                @click="approveEvent(respondent, 0)" :disabled="showApproval">
                    <i v-if="respondent.supervisor_approval = 0" class="fa fa-check"/>
                    <i v-else class="fa fa-thumbs-down"/> Disapprove
                </button>
            </td>
        </tr> -->
        </tbody>
    </table>
</template>
<script>
    export default {
        props: {
            respondentViewProps: Object,
        },
        data: function () {
            return {
                // laddabtn : '',
                respondent: {
                    ir_id: '',
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
                    remarks: '',
                    response: '',
                    hrbp_acknowledge_response: '',
                    incident_report: '',
                    status: '',
                    status_constant: this.$constants.Status,
                    first_offense: '',
                    second_offense: '',
                    third_offense: '',
                    fourth_offense: '',
                    fifth_offense: '',
                    sixth_offense: '',
                    seventh_offense: '',
                    // supervisor_approval: '',
                    attendancepointssystem: '',
                    infraction: '',
                    attendance_points: '',
                    definition: '',
                },
                user: this.$ep.user,
                showApproval: false,
            };
        },
        // mounted() {
        //     this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        // },
        created(){
            if(this.respondentViewProps.res_id){
                this.respondent.ir_id = this.respondentViewProps.res_ir_id;
                this.respondent.respondent_user_id = this.respondentViewProps.respondent_user_id;
                this.respondent.complainant_id = this.respondentViewProps.res_id;
                this.respondent.reported = this.respondentViewProps.res_reported;
                this.respondent.ir_number = this.respondentViewProps.res_ir_number;
                this.respondent.complainant = this.respondentViewProps.res_complainant;
                this.respondent.offense = this.respondentViewProps.res_offense;
                this.respondent.category = this.respondentViewProps.res_category;
                this.respondent.gravity = this.respondentViewProps.res_gravity;
                this.respondent.description = this.respondentViewProps.res_description;
                this.respondent.prescriptive_period = this.respondentViewProps.res_prescriptive_period;
                this.respondent.date_incident_start = this.respondentViewProps.res_date_incident_start;
                this.respondent.date_incident_end = this.respondentViewProps.res_date_incident_end;
                this.respondent.offense_description = this.respondentViewProps.res_offense_description;
                this.respondent.attachments = this.respondentViewProps.res_attachments;
                this.respondent.remarks = this.respondentViewProps.res_remarks;
                this.respondent.witness = this.respondentViewProps.res_witness;
                this.respondent.incident_report = this.respondentViewProps.res_incident_report;
                this.respondent.response = this.respondentViewProps.res_respondent_response;
                this.respondent.hrbp_acknowledge_response = this.respondentViewProps.res_respondent_hrbp_acknowledge_response;
                this.respondent.status = this.respondentViewProps.res_status;
                this.respondent.first_offense = this.respondentViewProps.first_offense;
                this.respondent.second_offense = this.respondentViewProps.second_offense;
                this.respondent.third_offense = this.respondentViewProps.third_offense;
                this.respondent.fourth_offense = this.respondentViewProps.fourth_offense;
                this.respondent.fifth_offense = this.respondentViewProps.fifth_offense;
                this.respondent.sixth_offense = this.respondentViewProps.sixth_offense;
                this.respondent.seventh_offense = this.respondentViewProps.seventh_offense;

                // if (this.respondentViewProps.res_supervisor_approval) {
                //     this.respondent.supervisor_approval = this.respondentViewProps.res_supervisor_approval;
                //     this.showApproval = true;
                // }

                if(this.respondentViewProps.res_notes){
                    this.respondent.notes = this.respondentViewProps.res_notes;
                }
            }
        },
        computed: {
            isComplete () {
                return this.respondent.response;
            }
        },
        // methods: {
        //     approveEvent: function (getRespondent, approverDisapprove) {
        //         this.laddabtn.start();
        //         this.$swal({
        //             title: 'Are you sure you want to Approve IR Number: '+ getRespondent.ir_number +'?',
        //             text: "Apply my details and employee number to NTE letter",
        //             type: "warning",
        //             showCancelButton: true,
        //             cancelButtonText: 'No, cancel!',
        //             confirmButtonClass: 'btn btn-success',
        //             cancelButtonClass: 'btn btn-danger',
        //         })
        //             .then((result) => {
        //                 if (result.value) {
        //                     this.$constants.Admin_API.get("/approval/signatory/create", {
        //                             params: {
        //                                 supervisor_user_id: this.user.id,
        //                                 respondent_user_id: getRespondent.respondent_user_id,
        //                                 complainant_id: getRespondent.complainant_id,
        //                                 ir_id: getRespondent.ir_id,
        //                                 approver_status: approverDisapprove,
        //                             },
        //                         })
        //                         .then(response => {
        //                             this.laddabtn.stop();
        //                             this.$bus.$emit('init_modal', false);
        //                             return response.data;
        //                         })
        //                         .catch(error => {
        //                             this.globalErrorHandling(error);
        //                         });
        //                 } else if (result.dismiss === this.$swal.DismissReason.cancel) {
        //                     this.laddabtn.stop();
        //                     this.$failure('IR Number:' + getRespondent.ir_number + ' Approval has been cancelled!')
        //                 }
        //             })
        //     },
        // },
    }
</script>
