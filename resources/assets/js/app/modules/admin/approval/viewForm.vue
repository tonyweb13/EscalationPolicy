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
                <strong>Complainant : </strong>
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
        <tr v-if="respondent.witness">
            <td colspan="2">
                <strong>Witness : </strong> {{ respondent.witness }}
            </td>
        </tr>

        <tr v-if="respondent.first_offense || respondent.second_offense">
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
        </tr>

        <tr>
            <td>
                <strong>Provision : </strong> {{ respondent.category }}
            </td>
            <td>
                <strong>Offense : </strong> {{ respondent.offense }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Gravity : </strong> {{ respondent.gravity }}
            </td>
            <td>
                <strong>Prescriptive Period : </strong> {{ respondent.prescriptive_period }} Days
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>IR Description : </strong><br>
                <div v-html="respondent.offense_description"/>
            </td>
        </tr>
        <tr v-if="respondent.description">
            <td colspan="2">
                <strong>Complainant Description : </strong><br>
                <div v-html="respondent.description"/>
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
                <strong v-if="respondent.response">
                Respondent Reply : </strong><br>
                <div v-html="respondent.response"/>
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
        <tr>
            <td colspan="2">
                <button class="ladda-button btn btn-primary" data-style="slide-right" data-color="mint" type="submit"
                @click="approveEvent(respondent, 1)">
                    <i class="fa fa-thumbs-up"/> Approve
                </button>
                <button class="ladda-button btn btn-danger" data-style="slide-right" data-color="mint" type="submit"
                @click="approveEvent(respondent, 0)">
                    <i class="fa fa-thumbs-down"/> Disapprove
                </button>
            </td>
        </tr>
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
                laddabtn : '',
                user: this.$ep.user,
                respondent: {
                    ir_id: '',
                    respondent_user_id: '',
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
                },
                // approval: {
                //     supervisor_user_id: '',
                //     respondent_user_id: '',
                //     complainant_id:  '',
                //     ir_id: '',
                //     approver_status: '',
                // }
            };
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        created(){
            if(this.respondentViewProps.res_id){
                this.respondent.ir_id = this.respondentViewProps.ir_id;
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
                this.respondent.res_offense_description = this.respondentViewProps.res_description;
                this.respondent.attachments = this.respondentViewProps.res_attachments;
                this.respondent.witness = this.respondentViewProps.res_witness;
                this.respondent.incident_report = this.respondentViewProps.res_incident_report;
                this.respondent.response = this.respondentViewProps.res_respondent_response;
                this.respondent.status = this.respondentViewProps.res_status;
                this.respondent.first_offense = this.respondentViewProps.first_offense;
                this.respondent.second_offense = this.respondentViewProps.second_offense;
                this.respondent.third_offense = this.respondentViewProps.third_offense;
                this.respondent.fourth_offense = this.respondentViewProps.fourth_offense;
                this.respondent.fifth_offense = this.respondentViewProps.fifth_offense;
                this.respondent.sixth_offense = this.respondentViewProps.sixth_offense;
                this.respondent.seventh_offense = this.respondentViewProps.seventh_offense;

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
        methods: {
            approveEvent: function (getRespondent, approverDisapprove) {
                this.laddabtn.start();
                this.$swal({
                    title: 'Are you sure you want to Approve IR Number: '+ getRespondent.ir_number +'?',
                    text: "Apply my details and employee number to NTE letter",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            console.log("getRespondent==");
                            console.log(getRespondent);

                            this.$constants.Admin_API.get("/headOfficer/approval/create", {
                                    params: {
                                        supervisor_user_id: this.user.id,
                                        respondent_user_id: getRespondent.respondent_user_id,
                                        complainant_id: getRespondent.complainant_id,
                                        ir_id: getRespondent.ir_id,
                                        approver_status: approverDisapprove,
                                    },
                                })
                                .then(response => {
                                    this.laddabtn.stop();
                                    this.$bus.$emit('init_modal', false);
                                    // console.log("this.approval==");
                                    // console.log(response.data);
                                    return response.data;
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.laddabtn.stop();
                            this.$failure('IR Number:' + getRespondent.ir_number + ' Approval has been cancelled!')
                        }
                    })
            },
        },
    }
</script>
