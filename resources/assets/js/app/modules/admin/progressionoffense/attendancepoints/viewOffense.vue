<template>
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#tab-complainant" aria-expanded="true">Complainant</a>
            </li>
            <li><a data-toggle="tab" href="#tab-hrview" aria-expanded="false">HR Review</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-complainant" class="tab-pane active">
                <complainant-closed :complainantViewProps="complainant_view_data"/>
            </div>
            <div class="clearfix"></div>

            <div id="tab-hrview" class="tab-pane">
                <incident-closed :incidentProps="incident_data"/>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>
<script>
import complainantClosed from '@/modules/admin/progressionoffense/attendancepoints/complainantClosed.vue'
import incidentClosed from '@/modules/admin/progressionoffense/attendancepoints/incidentClosed.vue'

export default {
    components: { incidentClosed, complainantClosed },
        props: {
            complainantProps: Object,
    },
    data: function () {
        return {
            complainant_view_data: {
                respondent_id: '',
                complainant_id: '',
                reported: '',
                respondent_response: '',
                ir_number: '',
                complainant: '',
                respondent: '',
                offense: '',
                category: '',
                gravity: '',
                description: '',
                prescriptive_period: '',
                date_incident_start: '',
                offense_description: '',
                attachments: '',
                witness: '',
                remarks: '',
                status: '',
                action: '',
            },
            incident_data: {
                complainant_id: '',
                incident_report: '',
                status_id: '',
                respondent_id: '',
                offense_id: '',
                ir_number: '',
            },
        };
    },
    created(){
        if(this.complainantProps.complainant_id){
            //for complainantClosed.vue
            this.complainant_view_data.respondent_id = this.complainantProps.respondent_id;
            this.complainant_view_data.complainant_id = this.complainantProps.complainant_id;
            this.complainant_view_data.ir_number = this.complainantProps.ir_number;
            this.complainant_view_data.reported = this.complainantProps.reported;
            this.complainant_view_data.respondent_response = this.complainantProps.respondent_response
            this.complainant_view_data.date_incident_start = this.complainantProps.date_incident_start;
            this.complainant_view_data.respondent = this.complainantProps.respondent;
            this.complainant_view_data.complainant = this.complainantProps.complainant;
            this.complainant_view_data.offense = this.complainantProps.offense;
            this.complainant_view_data.category = this.complainantProps.category;
            this.complainant_view_data.gravity = this.complainantProps.gravity;
            this.complainant_view_data.description = this.complainantProps.description;
            this.complainant_view_data.prescriptive_period = this.complainantProps.prescriptive_period;
            this.complainant_view_data.offense_description = this.complainantProps.offense_description;
            this.complainant_view_data.attachments = this.complainantProps.attachments;
            this.complainant_view_data.remarks = this.complainantProps.remarks;
            this.complainant_view_data.witness = this.complainantProps.witness;
            //for incidentClosed.vue
            this.incident_data.respondent_id = this.complainantProps.respondent_id;
            this.incident_data.complainant_id =   this.complainantProps.complainant_id;
            this.incident_data.incident_report =   this.complainantProps.incident_report;
            this.incident_data.status_id =   this.complainantProps.status;
            this.incident_data.respondent_user_id = this.complainantProps.respondent_user_id;
            this.incident_data.prescriptive_period = this.complainantProps.prescriptive_period;
            this.incident_data.complainant_user_id = this.complainantProps.complainant_user_id;
            this.incident_data.offense_id = this.complainantProps.offense_id;
            this.incident_data.ir_number = this.complainantProps.ir_number;
        }
    },
}
</script>
