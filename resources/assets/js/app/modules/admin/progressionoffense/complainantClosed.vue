<template>
    <table class="table borderless">
        <tbody>
        <tr>
            <td>
                <strong>IR Number : </strong> {{ complainant.ir_number }}
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
            <td v-if="complainant.witness">
                <strong>Witness : </strong>
                <span v-for="witnesses in complainant.witness">
                    {{ witnesses.witness_fullname.first_name }} {{ witnesses.witness_fullname.last_name }}<br>
                </span>
            </td>
            <td v-else></td>
        </tr>

        <!-- <tr v-if="complainant.first_offense || complainant.second_offense">
            <td>
                <div v-if="complainant.first_offense">
                    <strong>First Offense : </strong> {{ complainant.first_offense }}
                </div>
            </td>
            <td>
                <div v-if="complainant.second_offense">
                    <strong>Second Offense : </strong> {{ complainant.second_offense }}
                </div>
            </td>
        </tr> -->

        <!-- <tr v-if="complainant.third_offense || complainant.fourth_offense">
            <td>
                <div v-if="complainant.third_offense">
                    <strong>Third Offense : </strong> {{ complainant.third_offense }}
                </div>
            </td>
            <td>
                <div v-if="complainant.fourth_offense">
                    <strong>Fourth Offense : </strong> {{ complainant.fourth_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="complainant.fifth_offense || complainant.sixth_offense">
            <td>
                <div v-if="complainant.fifth_offense">
                    <strong>Fifth Offense : </strong> {{ complainant.fifth_offense }}
                </div>
            </td>
            <td>
                <div v-if="complainant.sixth_offense">
                    <strong>Sixth Offense : </strong> {{ complainant.sixth_offense }}
                </div>
            </td>
        </tr>

        <tr v-if="complainant.seventh_offense">
            <td colspan="2">
                <div v-if="complainant.seventh_offense">
                    <strong>Seventh Offense : </strong> {{ complainant.seventh_offense }}
                </div>
            </td>
        </tr> -->

        <tr>
            <td>
                <strong>Provision : </strong> {{ complainant.category }}
            </td>
            <td width="60%">
                <strong>Offense : </strong> {{ complainant.offense }}
                <strong style="color:red;" v-if="complainant.archived"> {{ complainant.archived }} </strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Gravity : </strong> {{ complainant.gravity }}
            </td>
            <td>
                <strong>Prescriptive Period : </strong> {{ complainant.prescriptive_period }}
            </td>
        </tr>
        <tr>
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
        <tr v-if="complainant.respondent_response">
            <td colspan="2">
                <strong>Respondent Reply : </strong><br>
               <div v-html="complainant.respondent_response"/>
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
            user: this.$ep.user,
            complainant: {
                respondent_user_id: '',
                complainant_id: '',
                reported: '',
                ir_number: '',
                complainant: '',
                respondent: '',
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
                res_id: '',
                archived: '',
                // first_offense: '',
                // second_offense: '',
                // third_offense: '',
                // fourth_offense: '',
                // fifth_offense: '',
                // sixth_offense: '',
                // seventh_offense: '',
            },
        };
    },
    created(){
        if(this.complainantViewProps.complainant_id){
            this.complainant.respondent_user_id = this.complainantViewProps.respondent_user_id;
            this.complainant.complainant_id = this.complainantViewProps.complainant_id;
            this.complainant.reported = this.complainantViewProps.reported;
            this.complainant.respondent_response = this.complainantViewProps.respondent_response;
            this.complainant.date_incident_start = this.complainantViewProps.date_incident_start;
            this.complainant.date_incident_end = this.complainantViewProps.date_incident_end;
            this.complainant.ir_number = this.complainantViewProps.ir_number;
            this.complainant.respondent = this.complainantViewProps.respondent;
            this.complainant.archived = this.complainantViewProps.archived;

            this.complainant.complainant = this.complainantViewProps.complainant;
            this.complainant.offense = this.complainantViewProps.offense;
            this.complainant.category = this.complainantViewProps.category;
            this.complainant.gravity = this.complainantViewProps.gravity;
            this.complainant.description = this.complainantViewProps.description;
            this.complainant.prescriptive_period = this.complainantViewProps.prescriptive_period;

            this.complainant.offense_description = this.complainantViewProps.offense_description;
            this.complainant.attachments = this.complainantViewProps.attachments;
            this.complainant.remarks = this.complainantViewProps.remarks;
            this.complainant.respondent_response = this.complainantViewProps.respondent_response
            this.complainant.witness = this.complainantViewProps.witness;
            this.complainant.is_generate_nte_invalid_ir = this.complainantViewProps.is_generate_nte_invalid_ir;
        }
    },
}
</script>
