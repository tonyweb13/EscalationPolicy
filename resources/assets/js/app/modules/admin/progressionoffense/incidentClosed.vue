<template>
    <div v-if="incident.incident_report != null">
        <table class="table borderless">
            <tbody>
                <tr>
                    <td width="50%">
                        <strong>HRBP Name : </strong>
                        <span v-if="incident.incident_report
                        && incident.incident_report.hrbp_user">
                            {{ incident.incident_report.hrbp_user.first_name }}
                            {{ incident.incident_report.hrbp_user.last_name }}
                        </span>
                        <span v-else-if="incident.hrbp_user">
                            {{ incident.hrbp_user }}
                        </span>
                    </td>
                    <td width="50%">
                        <strong>Status : </strong>
                        <span v-for="stat in selectedStatus">
                            <span v-if="incident.status_id == stat.value">
                                {{ stat.text }}
                            </span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <span v-if="incident.incident_report.created_at">
                            <strong>Date NTE Generated :</strong>
                            {{ incident.incident_report.created_at | formatDateCompleteMonth }}
                        </span>
                    </td>
                    <td width="50%" >
                        <span v-if="incident.incident_report.date_da">
                            <strong>Date IR Closed : </strong>
                            {{ incident.incident_report.date_da | formatDateCompleteMonth }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <strong>Generate NTE / Invalid IR : </strong>
                        <span v-for="generateNte in selectedIsGenerateNte">
                            <span v-if="incident.incident_report.is_generate_nte_invalid_ir ==
                            generateNte.value">
                                {{ generateNte.text }}
                            </span>
                        </span>
                    </td>
                    <td width="50%" >
                        <div v-if="incident.incident_report.type_invite">
                            <strong>Type of Invite : </strong>
                            <span >
                                {{incident.incident_report.type_invite}}
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="100%" v-if="incident.incident_report.remarks">
                        <strong>Remarks :</strong>
                        <div v-html="incident.incident_report.remarks"/>
                    </td>
                </tr>
                <!-- date of admin hearing -->
                <tr v-if="incident.incident_report.date_admin_hearing
                    || incident.incident_report.time_admin_hearing">
                    <td width="50%" >
                        <strong>Date of Admin Hearing :</strong>
                        <span v-if="incident.incident_report.date_admin_hearing">
                            {{ incident.incident_report.date_admin_hearing }}
                        </span>
                    </td>
                    <td width="50%" >
                        <strong>Time of Admin Hearing :</strong>
                        <span v-if="incident.incident_report.time_admin_hearing">
                            {{ incident.incident_report.time_admin_hearing }}
                        </span>
                    </td>
                </tr>
                <tr v-if="incident.incident_report.meeting_place">
                    <td width="50%">
                    </td>
                    <td width="50%" >
                        <strong>Place of Admin Hearing :</strong>
                        <span v-if="incident.incident_report.meeting_place">
                            {{ incident.incident_report.meeting_place }}
                        </span>
                    </td>
                </tr>
                <!-- Rtw -->
                <tr v-if="incident.incident_report.rtw">
                    <td width="50%" >
                        <strong>Return to work order date : </strong>
                        <span v-if="incident.incident_report.rtw_date">
                            {{ incident.incident_report.rtw_date }}
                        </span>
                    </td>
                    <td width="50%" >
                        <strong>Advised to return to work later than : </strong>
                        <span v-if="incident.incident_report.rtw_advice_date">
                            {{ incident.incident_report.rtw_advice_date }}
                        </span>
                    </td>
                </tr>
                <tr v-if="incident.incident_report.rtw">
                    <td width="50%">
                    </td>
                    <td width="50%" v-if="incident.incident_report.sn_termination_date">
                        <strong>Termination Date :</strong>
                        {{ incident.incident_report.sn_termination_date }}
                    </td>
                </tr>
                <tr v-if="incident.incident_report.rtw">
                    <td width="50%" >
                        <strong>Date of Absences start :</strong>
                        <span v-if="incident.incident_report.sn_date_absence_start">
                            {{ incident.incident_report.sn_date_absence_start }}
                        </span>
                    </td>
                    <td width="50%" >
                        <strong>Date of Absences end :</strong>
                        <span v-if="incident.incident_report.sn_date_absence_end">
                            {{ incident.incident_report.sn_date_absence_end }}
                        </span>
                    </td>
                </tr>
                <!-- hr invite -->
                <tr>
                    <td width="50%" v-if="incident.incident_report.invite_user.length > 1">
                        <strong>HR Invite : </strong>
                        <span v-for="invite in incident.incident_report.invite_user">
                            <span v-if="invite.invite_fullname">
                                {{ invite.invite_fullname.first_name }}
                                {{ invite.invite_fullname.last_name }}
                            </span><br />
                        </span>
                    </td>
                    <td width="50%" v-if="incident.incident_report.witness_user.length > 1">
                        <strong>HR Witness : </strong>
                        <span v-for="witness in incident.incident_report.witness_user">
                            <span v-if="witness.witness_fullname">
                                {{ witness.witness_fullname.first_name }}
                                {{ witness.witness_fullname.last_name }}
                            </span><br />
                        </span>
                    </td>
                </tr>
                <tr v-if="incident.incident_report.irr_id && role != 'Regular User Access'
                && designation.toLowerCase().indexOf('supervisor') < 0
                && designation.toLowerCase().indexOf('team') < 0">
                    <td colspan="2">
                        <strong>Case Closure :</strong><br>
                        <div v-html="incident.incident_report.irr.name"/>
                        <strong style="color:red;" v-if="incident.archived">
                            {{ incident.archived }} </strong>
                    </td>
                </tr>
                <tr v-if="incident.incident_report.date_admin_hearing">
                    <td colspan="2">
                        <strong>Date of Admin Hearing :</strong>
                        {{ incident.incident_report.date_admin_hearing }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>

    export default {
        props: {
            incidentProps: Object,
        },

        data: function () {
            return {
                role: this.$ep.role,
                designation: this.$ep.user.designation.name,
                incident: {
                    respondent_id: '',
                    complainant_id: '',
                    status_id: {
                        text: "In Progress",
                        value: 2,
                        selected: "selected",
                    },
                    remarks: '',
                    is_generate_nte_invalid_ir: '',
                    is_admin_hearing: 0,
                    date_admin_hearing: '',
                    is_under_investigation: 0,
                    irr_id: '',
                    type_invite: '',
                    witness_user_id: '',
                    invite_user_id: '',
                    witness_multiple: [],
                    invite_multiple: [],
                    incident_report: null,
                    respondent_user_id: null,
                    prescriptive_period: null,
                    complainant_user_id: '',
                    offense_id: '',
                    hr_user_id: '',
                    time_admin_hearing:'',
                    meeting_place:'',
                    ir_number: '',
                    archived: '',
                },
                selectedUser: [],
                selectedIRR: [],
                selectedHrbp: [],
                selectedStatus: this.$constants.Status,
                selectedIsGenerateNte: this.$constants.Is_generate_nte,
                selectedTime: this.$constants.Time,
                selectedMeetingPlace: this.$constants.Meeting_Place,
                selectedTypeInvite: [
                    'For Investigation',
                    'For Admin Hearing'
                ],
            };
        },
        created(){
            this.incident.respondent_id = this.incidentProps.respondent_id;
            this.incident.complainant_id = this.incidentProps.complainant_id;
            this.incident.respondent_user_id = this.incidentProps.respondent_user_id;
            this.incident.prescriptive_period = this.incidentProps.prescriptive_period;
            this.incident.complainant_user_id = this.incidentProps.complainant_user_id;
            this.incident.offense_id = this.incidentProps.offense_id;
            this.incident.ir_number = this.incidentProps.ir_number;
            this.incident.incident_report = this.incidentProps.incident_report;
            this.incident.archived = this.incidentProps.archived;

            if(this.incidentProps.status_id > 1) {
                this.incident.status_id = this.incidentProps.status_id;
            }
        },
    }
</script>
