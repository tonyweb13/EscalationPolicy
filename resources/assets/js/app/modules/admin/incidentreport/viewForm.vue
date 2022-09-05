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
                <complainant :complainantViewProps="complainant_view_data"/>
            </div>
            <div class="clearfix"></div>

            <div id="tab-hrview" class="tab-pane">
                <edit-form v-if="edit_data.action == 'edit'" :editProps="edit_data"/>
                <incident-form v-else :incidentProps="incident_data"/>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>
<script>
import complainant from '@/modules/admin/incidentreport/complainant.vue'
import incidentForm from '@/modules/admin/incidentreport/incidentForm.vue'
import editForm from '@/modules/admin/incidentreport/editForm.vue'

export default {
    components: { incidentForm, complainant, editForm },
    props: {
        complainantProps: Object,
    },
    data: function () {
        return {
            complainant_view_data: {
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
                status: '',
                action: '',
                complainant_attachments: [],
                respondent_id: '',
                offense_id: '',
                offense_multiple: [],
                initial_irr_id: '',
                initial_instance: '',
                progression_occurence_multi: [],
                ageing: '',
                reply_reactivate: '',
            },
            incident_data: {
                complainant_id: '',
                incident_report: '',
                status_id: '',
                respondent_id: '',
                offense_id: '',
                ir_number: '',
                attached_hr: [],
                hrbp_user_id: '',
                reported: '',
                respondent_email: '',
                supervisor_email: '',
                manager_email: '',
                hrbp_user: '',
                date_da: '',
                irr_exist: '',
                hrbp_user_first: '',
                hrbp_user_last: '',
                hrbp_user_employee_no: '',
                hrbp_user_designation: '',
                supervisor_first: '',
                supervisor_last: '',
                supervisor_employee_no: '',
                supervisor_designation: '',
                manager_first: '',
                manager_last: '',
                manager_employee_no: '',
                manager_designation: '',
                progression_offense: '',
                initial_irr_id: '',
                initial_instance: '',
                updated_at: '',
            },
            edit_data: {
                action: '',
                status_id: '',
                complainant_id: '',
                remarks: '',
                is_generate_nte_invalid_ir: '',
                type_of_date: '',
                start_date: '',
                takes_date: '',
                last_date: '',
                date_admin_hearing: '',
                is_under_investigation: '',
                irr_id: '',
                invite_user_id: '',
                witness_user_id: '',
                type_invite: '',
                ageing: '',
                incident_report: '',
                hr_user_id: '',
                nte_upload: '',
                attached_hr: [],
                signed: '',
                respondent_email: '',
                supervisor_email: '',
                manager_email: '',
                first_comment: '',
                second_comment: '',
                mom: '',
                hrbp_user: '',
                offense_id: '',
                prepared_by_first: '',
                prepared_by_last: '',
                prepared_by_designation: '',
                prepared_by_empno: '',
                requested_by_first: '',
                requested_by_last: '',
                requested_by_designation: '',
                requested_by_empno: '',
                approved_by_first: '',
                approved_by_last: '',
                approved_by_designation: '',
                approved_by_empno: '',
                noted1_by_first: '',
                noted1_by_last: '',
                noted1_by_designation: '',
                noted1_by_empno: '',
                noted2_by_first: '',
                noted2_by_last: '',
                noted2_by_designation: '',
                noted2_by_empno: '',
                noted3_by_first: '',
                noted3_by_last: '',
                noted3_by_designation: '',
                noted3_by_empno: '',
                nte_stage_case: '',
            }
        };
    },
    created(){
        if(this.complainantProps.comp_id){
            this.complainant_view_data.respondent_user_id = this.complainantProps.comp_respondent_user_id;
            this.complainant_view_data.respondent_id = this.complainantProps.res_id;
            this.complainant_view_data.action = this.complainantProps.comp_action;
            this.complainant_view_data.complainant_id = this.complainantProps.comp_id;
            this.complainant_view_data.reported = this.complainantProps.comp_reported;
            this.complainant_view_data.ir_number = this.complainantProps.comp_ir_number;
            this.complainant_view_data.complainant = this.complainantProps.comp_complainant;
            this.complainant_view_data.respondent = this.complainantProps.comp_respondent;
            this.complainant_view_data.offense = this.complainantProps.comp_offense;
            this.complainant_view_data.category = this.complainantProps.comp_category;
            this.complainant_view_data.gravity = this.complainantProps.comp_gravity;
            this.complainant_view_data.description = this.complainantProps.comp_description;
            this.complainant_view_data.prescriptive_period = this.complainantProps.comp_prescriptive_period;
            this.complainant_view_data.date_incident_start = this.complainantProps.comp_date_incident_start;
            this.complainant_view_data.date_incident_end = this.complainantProps.comp_date_incident_end;
            this.complainant_view_data.offense_description = this.complainantProps.comp_offense_description;
            this.complainant_view_data.attachments = this.complainantProps.comp_attachments;
            this.complainant_view_data.remarks = this.complainantProps.comp_remarks;
            this.complainant_view_data.respondent_response = this.complainantProps.comp_respondent_response
            this.complainant_view_data.hrbp_acknowledge_response = this.complainantProps.comp_hrbp_acknowledge_response;
            this.complainant_view_data.witness = this.complainantProps.comp_witness;
            this.complainant_view_data.date_response = this.complainantProps.comp_date_response;
            this.complainant_view_data.archived = this.complainantProps.comp_archived;
            this.complainant_view_data.second_respondent_response = this.complainantProps.comp_second_reply
            this.complainant_view_data.second_date_response = this.complainantProps.comp_second_date_reply;

            this.complainant_view_data.first_offense = this.complainantProps.comp_first_offense;
            this.complainant_view_data.second_offense = this.complainantProps.comp_second_offense;
            this.complainant_view_data.third_offense = this.complainantProps.comp_third_offense;
            this.complainant_view_data.fourth_offense = this.complainantProps.comp_fourth_offense;
            this.complainant_view_data.fifth_offense = this.complainantProps.comp_fifth_offense;
            this.complainant_view_data.sixth_offense = this.complainantProps.comp_sixth_offense;
            this.complainant_view_data.seventh_offense = this.complainantProps.comp_seventh_offense;
            this.complainant_view_data.complainant_attachments = this.complainantProps.comp_attachments;
            this.complainant_view_data.offense_id = this.complainantProps.comp_offense_id;
            this.complainant_view_data.ageing = this.complainantProps.comp_ageing;
            this.complainant_view_data.reply_reactivate = this.complainantProps.comp_reply_reactivate;
            this.complainant_view_data.respondent_attachments =
            this.complainantProps.comp_respondent_attachments;

            /* IR History View Button */
            if (this.complainantProps.comp_multi_offense_id) {
                this.complainant_view_data.offense_id = this.complainantProps.comp_multi_offense_id;
            }

            this.complainant_view_data.irr_exist = this.complainantProps.comp_irr_archived;
            if (this.complainantProps.comp_progression_occurence_multi) {
                this.complainant_view_data.progression_occurence_multi = this.complainantProps.comp_progression_occurence_multi
            }

            if (this.complainantProps.comp_initial_irr_id) {
                this.complainant_view_data.initial_irr_id = this.complainantProps.comp_initial_irr_id;
            }
            if (this.complainantProps.comp_initial_instance) {
                this.complainant_view_data.initial_instance = this.complainantProps.comp_initial_instance;
            }

            this.incident_data.respondent_id = this.complainantProps.res_id;
            this.incident_data.complainant_id =   this.complainantProps.comp_id;
            this.incident_data.incident_report =   this.complainantProps.comp_incident_report;
            this.incident_data.status_id =   this.complainantProps.comp_status;
            this.incident_data.respondent_user_id = this.complainantProps.comp_respondent_user_id;
            this.incident_data.prescriptive_period = this.complainantProps.comp_prescriptive_period;
            this.incident_data.complainant_user_id = this.complainantProps.comp_complainant_user_id;
            this.incident_data.offense_id = this.complainantProps.comp_offense_id;
            this.incident_data.ir_number = this.complainantProps.comp_ir_number;
            this.incident_data.attached_hr = this.complainantProps.comp_attached_hr;
            this.incident_data.hrbp_user_id = this.complainantProps.comp_hrbp_user_id;
            this.incident_data.reported = this.complainantProps.comp_reported;
            this.incident_data.respondent_email = this.complainantProps.comp_respondent_email;
            this.incident_data.supervisor_email = this.complainantProps.comp_supervisor_email;
            this.incident_data.manager_email = this.complainantProps.comp_manager_email;
            this.incident_data.hrbp_user = this.complainantProps.comp_hrbp_user;
            this.incident_data.date_da = this.complainantProps.comp_date_da;
            this.incident_data.irr_exist = this.complainantProps.comp_irr_archived;
            this.incident_data.hrbp_user_first = this.complainantProps.comp_hrbp_user_first;
            this.incident_data.hrbp_user_last = this.complainantProps.comp_hrbp_user_last;
            this.incident_data.hrbp_user_employee_no = this.complainantProps.comp_hrbp_user_employee_no;
            this.incident_data.hrbp_user_designation = this.complainantProps.comp_hrbp_user_designation;
            this.incident_data.supervisor_first = this.complainantProps.comp_supervisor_first;
            this.incident_data.supervisor_last = this.complainantProps.comp_supervisor_last;
            this.incident_data.supervisor_employee_no = this.complainantProps.comp_supervisor_employee_no;
            this.incident_data.supervisor_designation = this.complainantProps.comp_supervisor_designation;
            this.incident_data.manager_first = this.complainantProps.comp_manager_first;
            this.incident_data.manager_last = this.complainantProps.comp_manager_last;
            this.incident_data.manager_employee_no = this.complainantProps.comp_manager_employee_no;
            this.incident_data.manager_designation = this.complainantProps.comp_manager_designation;
            this.incident_data.mom_attachment = this.complainantProps.comp_mom_attachment;
            this.incident_data.mom = this.complainantProps.comp_mom;

            if (this.complainantProps.comp_updated_at) {
                this.incident_data.updated_at = this.complainantProps.comp_updated_at;
            }

            if (this.complainantProps.comp_initial_irr_id) {
                this.incident_data.initial_irr_id = this.complainantProps.comp_initial_irr_id;
            }
            if (this.complainantProps.comp_initial_instance) {
                this.incident_data.initial_instance = this.complainantProps.comp_initial_instance;
            }

            // if (this.complainantProps.seventh_offense) {
            //     this.incident_data.progression_offense = 'seventh_offense';
            // } else if (this.complainantProps.sixth_offense) {
            //     this.incident_data.progression_offense = 'sixth_offense';
            // } else if (this.complainantProps.fifth_offense) {
            //     this.incident_data.progression_offense = 'fifth_offense';
            // } else if (this.complainantProps.fourth_offense) {
            //     this.incident_data.progression_offense = 'fourth_offense';
            // } else if (this.complainantProps.third_offense) {
            //     this.incident_data.progression_offense = 'third_offense';
            // } else if (this.complainantProps.second_offense) {
            //     this.incident_data.progression_offense = 'second_offense';
            // } else if (this.complainantProps.first_offense) {
            //     this.incident_data.progression_offense = 'first_offense';
            // } else {
            //     this.incident_data.progression_offense = null;
            // }

            this.edit_data.inc_id = this.complainantProps.inc_id;
            this.edit_data.action = this.complainantProps.comp_action;
            this.edit_data.complainant_id = this.complainantProps.comp_id;
            this.edit_data.status_id = this.complainantProps.comp_status;
            this.edit_data.type_invite = this.complainantProps.comp_type_invite;
            this.edit_data.incident_report =   this.complainantProps.comp_incident_report;
            this.edit_data.respondent_id = this.complainantProps.res_id;
            this.edit_data.hr_user_id = this.complainantProps.comp_hr_user_id;
            this.edit_data.respondent_user_id = this.complainantProps.comp_respondent_user_id;
            this.edit_data.complainant_user_id = this.complainantProps.comp_complainant_user_id;
            this.edit_data.ir_number = this.complainantProps.comp_ir_number;
            this.edit_data.nte_upload = this.complainantProps.comp_nte_upload;
            this.edit_data.attached_hr = this.complainantProps.comp_attached_hr;
            this.edit_data.is_generate_nte_invalid_ir = this.complainantProps.comp_is_generate_nte_invalid_ir;
            this.edit_data.signed = this.complainantProps.comp_signed;
            this.edit_data.rtw = this.complainantProps.comp_rtw;
            this.edit_data.type_of_date = this.complainantProps.comp_type_of_date
            this.edit_data.start_date = this.complainantProps.comp_start_date
            this.edit_data.takes_date = this.complainantProps.comp_takes_date
            this.edit_data.last_date = this.complainantProps.comp_last_date
            this.edit_data.irr_id = this.complainantProps.comp_irr_id;
            this.edit_data.respondent_email = this.complainantProps.comp_respondent_email;
            this.edit_data.supervisor_email = this.complainantProps.comp_supervisor_email;
            this.edit_data.manager_email = this.complainantProps.comp_manager_email;
            this.edit_data.first_comment = this.complainantProps.comp_first_comment
            this.edit_data.second_comment = this.complainantProps.comp_second_comment;
            this.edit_data.mom = this.complainantProps.comp_mom;
            this.edit_data.hrbp_user_id = this.complainantProps.comp_hrbp_user_id;
            this.edit_data.hrbp_user = this.complainantProps.comp_hrbp_user;
            this.edit_data.offense_id = this.complainantProps.comp_offense_id;
            this.edit_data.nte_stage_case = this.complainantProps.comp_nte_stage_case;

            /* Start Signatories */
            this.edit_data.prepared_by_first = this.complainantProps.comp_prepared_by_first;
            this.edit_data.prepared_by_last = this.complainantProps.comp_prepared_by_last;
            this.edit_data.prepared_by_designation = this.complainantProps.comp_prepared_by_designation;
            this.edit_data.prepared_by_empno = this.complainantProps.comp_prepared_by_empno;
            this.edit_data.requested_by_first = this.complainantProps.comp_requested_by_first;
            this.edit_data.requested_by_last = this.complainantProps.comp_requested_by_last;
            this.edit_data.requested_by_designation = this.complainantProps.comp_requested_by_designation;
            this.edit_data.requested_by_empno = this.complainantProps.comp_requested_by_empno;
            this.edit_data.approved_by_first = this.complainantProps.comp_approved_by_first;
            this.edit_data.approved_by_last = this.complainantProps.comp_approved_by_last;
            this.edit_data.approved_by_designation = this.complainantProps.comp_approved_by_designation;
            this.edit_data.approved_by_empno = this.complainantProps.comp_approved_by_empno;
            this.edit_data.noted1_by_first = this.complainantProps.comp_noted1_by_first;
            this.edit_data.noted1_by_last = this.complainantProps.comp_noted1_by_last;
            this.edit_data.noted1_by_designation = this.complainantProps.comp_noted1_by_designation;
            this.edit_data.noted1_by_empno = this.complainantProps.comp_noted1_by_empno;
            this.edit_data.noted2_by_first = this.complainantProps.comp_noted2_by_first;
            this.edit_data.noted2_by_last = this.complainantProps.comp_noted2_by_last;
            this.edit_data.noted2_by_designation = this.complainantProps.comp_noted2_by_designation;
            this.edit_data.noted2_by_empno = this.complainantProps.comp_noted2_by_empno;
            this.edit_data.noted3_by_first = this.complainantProps.comp_noted3_by_first;
            this.edit_data.noted3_by_last = this.complainantProps.comp_noted3_by_last;
            this.edit_data.noted3_by_designation = this.complainantProps.comp_noted3_by_designation;
            this.edit_data.noted3_by_empno = this.complainantProps.comp_noted3_by_empno;
            /* End Signatories */
        }
    },
}
</script>
