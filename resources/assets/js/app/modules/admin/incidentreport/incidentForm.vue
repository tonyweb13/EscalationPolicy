<template>
    <div>
        <!-- View Button IR History-->
        <div v-if="incident.incident_report != null">
            <table class="table borderless">
                <tbody>
                    <tr>
                        <td width="50%">
                            <strong>HRBP Name: </strong>
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
                            <span v-if="!incident.status_id">
                                    Closed
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
                            <span v-if="incident.date_da">
                                <strong>Date IR Closed : </strong>
                                {{ incident.date_da | formatDateCompleteMonth }}
                            </span>
                            <span v-else>
                                <strong>Date IR Closed : </strong>
                                {{ incident.updated_at | formatDateCompleteMonth }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <strong>Generate NTE / Invalid IR : </strong>
                            <span v-for="generateNte in selectedIsGenerateNte">
                                <span v-if="incident.incident_report.is_generate_nte_invalid_ir == generateNte.value">
                                    {{ generateNte.text }}
                                </span>
                            </span>
                        </td>
                        <td width="50%" >
                            <strong>Type of Invite : </strong>
                            <span v-if="incident.incident_report.type_invite">
                                {{incident.incident_report.type_invite}}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="role === 'Super Admin Access' || role === 'HR Admin Access' ||
                        role === 'HR Access'">
                        <td colspan="2" width="100%" v-if="incident.incident_report.remarks">
                            <strong>Remarks:</strong>
                            <div v-html="incident.incident_report.remarks"/>
                        </td>
                    </tr>
                    <!-- Start Preventive Suspension -->
                    <tr v-if="incident.incident_report.is_generate_nte_invalid_ir == 4">
                        <td width="50%" >
                            <strong>Preventive Suspension Start :</strong>
                            <span v-if="incident.incident_report.preventive_suspension_start">
                                {{ incident.incident_report.preventive_suspension_start }}
                            </span>
                        </td>
                        <td width="50%" >
                            <strong>Preventive Suspension End :</strong>
                            <span v-if="incident.incident_report.preventive_suspension_end">
                                {{ incident.incident_report.preventive_suspension_end }}
                            </span>
                        </td>
                    </tr>
                    <!-- End Preventive Suspension -->
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
                        <td width="50%" v-if="incident.incident_report.invite_user.length > 1
                            || incident.incident_report.invite_user != undefined">
                            <strong>HR Invite : </strong>
                            <span v-for="invite in incident.incident_report.invite_user">
                                <span v-if="invite.invite_fullname">
                                    {{ invite.invite_fullname.first_name }}
                                    {{ invite.invite_fullname.last_name }}
                                </span><br />
                            </span>
                        </td>
                        <td width="50%" v-if="incident.incident_report.witness_user.length > 1
                            || incident.incident_report.witness_user != undefined">
                            <strong>HR Witness : </strong>
                            <span v-for="witness in incident.incident_report.witness_user">
                                <span v-if="witness.witness_fullname">
                                    {{ witness.witness_fullname.first_name }}
                                    {{ witness.witness_fullname.last_name }}
                                </span><br />
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" width="100%">
                            <strong v-if="downloadHrAttach">
                                {{ downloadHrAttach }}
                                <br><br>
                            </strong>
                            <div v-for="attach1 in incident.attached_hr">
                                <ul v-for="attach2 in attach1.attachments.split(',')"
                                v-if='attach1.attachments'>
                                    <li v-if="attach2.match(/hr/g)">
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
                    <tr v-if="incident.incident_report.date_admin_hearing
                        || incident.incident_report.time_admin_hearing">
                        <td colspan="2">
                            <button class='btn btn-success btn-xs'
                                @click="eventGenerateAdminHearing(incident.complainant_id,
                                incident.respondent_user_id)">
                                <i class="fa fa-download"></i>
                                Download Admin Hearing
                            </button>
                        </td>
                    </tr>
                    <tr v-if="incident.incident_report.irr && role != 'Regular User Access'
                        && designation.toLowerCase().indexOf('supervisor') < 0
                        && designation.toLowerCase().indexOf('team') < 0">
                        <td colspan="2">
                        <strong>Case Closure: </strong>
                        {{ incident.incident_report.irr.name }}
                        <strong style="color:red;" v-if="incident.irr_exist">
                            {{ incident.irr_exist }}
                        </strong>
                        </td>
                    </tr>
                    <tr v-if="incident.mom_attachment">
                        <td colspan="2">
                            <div v-if='incident.mom_attachment.attachments'>
                                <div v-for="momAttach in incident.mom_attachment.attachments.split(',')"
                                v-if="incident.mom_attachment.attachments != 'null'">
                                    <strong>
                                        Download Minutes Of The Meeting Attachment :
                                        <br><br>
                                        <li >
                                            <a :href="'/api/admin/incidentreport/download/mom_attachment/'
                                            + momAttach | urlReplace">
                                                {{ momAttach | urlReplace }}
                                            </a>
                                        </li>
                                    </strong>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="incident.mom">
                        <td colspan="2">
                            <strong>Minutes of The Meeting and comments:</strong>
                            <div v-html="incident.mom" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Review Button -->
        <the-form v-else :asterisk="true" @submitForm="eventUpdate" class="m-t-xl" >
            <div slot="form-body">
                <the-label label="Assign to HRBP" :asterisk="true">
                    <vSelect
                        v-model="incident.hr_user_id"
                        :options="selectedHrbp"
                        label="text"
                        :disabled="disabledAssignHR"
                    />
                </the-label>
                <the-label label="Status" :asterisk="true">
                    <vSelect
                        v-model="incident.status_id"
                        :options="selectedStatus"
                        label="text"
                        @input="eventStatusChange(incident.status_id)"
                        :disabled="disabledStatus"
                    />
                </the-label>

                <the-label label="Remarks" :asterisk="true">
                    <vue-editor
                        v-model="incident.remarks"
                        @input="changeRemark(incident.remarks)"
                        :editorToolbar="customToolbar"
                        :disabled="disabledRemarks"
                    />
                </the-label>
                <div class="form-group">
                    <div class="asterisk_label">*</div>
                    <label-radio>
                        <input type="radio"
                            name="is_generate_nte_invalid_ir"
                            id="is_generate_nte_invalid_ir1"
                            :value='1'
                            v-model="incident.is_generate_nte_invalid_ir"
                            @input="eventGenerateNTE(1)"
                            :disabled="disabledStatusIR"
                        >
                        <label class="m-l-xs" for="is_generate_nte_invalid_ir1"><b> Generate NTE </b></label>
                    </label-radio>
                    <label-radio>
                        <input type="radio"
                            name="is_generate_nte_invalid_ir"
                            id="is_generate_nte_invalid_ir4"
                            :value='4'
                            v-model="incident.is_generate_nte_invalid_ir"
                            @input="eventGenerateNTE(4)"
                            :disabled="disabledStatusIR"
                        >
                        <label class="m-l-xs" for="is_generate_nte_invalid_ir4"><b> Generate NTE with PS</b></label>
                    </label-radio>
                    <label-radio>
                        <input type="radio"
                            name="is_generate_nte_invalid_ir"
                            id="is_generate_nte_invalid_ir2"
                            :value='2'
                            v-model="incident.is_generate_nte_invalid_ir"
                            @input="eventGenerateNTE(2)"
                            :disabled="disabledStatusIR || incident.status_id.value != 4"
                        >
                        <label for="is_generate_nte_invalid_ir2"><b> Invalid IR </b></label>
                    </label-radio>
                    <label-radio>
                        <input type="radio"
                            name="is_generate_nte_invalid_ir"
                            id="is_generate_nte_invalid_ir3"
                            :value='3'
                            v-model="incident.is_generate_nte_invalid_ir"
                            @input="eventGenerateNTE(3)"
                            :disabled="disabledStatusIR"
                        >
                        <label for="is_generate_nte_invalid_ir3"><b> In Review </b></label>
                    </label-radio>
                </div>
                <the-label>

                    <label-radio>
                        <input type="radio"
                            name="is_admin_hearing"
                            id="is_admin_hearing1"
                            @input="eventDateHearing(1)"
                            v-model="incident.is_admin_hearing"
                            value='1'
                            :disabled="disabledIsAdminHearing"
                        >
                        <label for="is_admin_hearing1"><b> Date of Admin Hearing </b></label>
                    </label-radio>

                    <label-radio>
                        <input type="radio"
                            name="is_admin_hearing"
                            id="is_admin_hearing2"
                            @input="eventDateHearing(2)"
                            v-model="incident.is_admin_hearing"
                            value='2'
                            :disabled="disabledIsAdminHearing"
                        >
                        <label for="is_admin_hearing2"><b> Report to work order (RTW) </b></label>
                    </label-radio>

                    <label-radio>
                        <input type="radio"
                            name="is_admin_hearing"
                            id="is_admin_hearing0"
                            @input="eventDateHearing(0)"
                            v-model="incident.is_admin_hearing"
                            value='0'
                            :disabled="disabledIsAdminHearing"
                        >
                        <label for="is_admin_hearing0"><b> N/A </b></label>
                    </label-radio>

                    <!-- <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        :disabled="disabledDateHearing"
                        v-model="incident.date_admin_hearing"
                    /> -->

                </the-label>

                <!-- Start Preventive Suspension -->
                <the-label label="Preventive Suspension Date" class="m-t-lg"
                style="margin-bottom: 80px;" v-show="showPSDate">
                    <br>
                    <div class="col-lg-6">
                        <the-label label="Start" :asterisk="true">
                        <datepicker
                            :input-class="'custom-datepicker'"
                            :format="'yyyy-MM-dd'"
                            :calendar-button="true"
                            :calendar-button-icon="'fa fa-calendar'"
                            :use-utc="true"
                            placeholder="YYYY-MM-DD"
                            v-model="incident.preventive_suspension_start"
                        />
                        </the-label>
                    </div>
                    <div class="col-lg-6">
                        <the-label label="End" :asterisk="true">
                        <datepicker
                            :input-class="'custom-datepicker'"
                            :format="'yyyy-MM-dd'"
                            :calendar-button="true"
                            :calendar-button-icon="'fa fa-calendar'"
                            :use-utc="true"
                            placeholder="YYYY-MM-DD"
                            v-model="incident.preventive_suspension_end"
                            @input="dateRangeValidation(incident.preventive_suspension_start,
                            incident.preventive_suspension_end)"
                        />
                        </the-label>
                    </div>
                </the-label>
                <!-- End Preventive Suspension -->

                <!-- start checked radio Date hearing -->
                <the-label v-show="showDateHearing"
                    label="Date of Admin Hearing"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.date_admin_hearing"
                        :disabledDates="disabledDates"
                    />
                </the-label>

                <the-label v-show="showTime"
                    label="Time of Admin Hearing"
                    :asterisk="true">
                    <vSelect
                        v-model="incident.time_admin_hearing"
                        :options="selectedTime"
                        label="text"
                    />
                </the-label>

                <the-label v-show="showMeetingPlace"
                    label="Meeting Place of Admin Hearing"
                    :asterisk="true">
                    <vSelect
                        v-model="incident.meeting_place"
                        :options="selectedMeetingPlace"
                        label="text"
                    />
                </the-label>
                <!-- end checked radio Date hearing -->

                <!-- start checked radio RTW -->
                <the-label v-show="showRtw" label="Note: ">
                    <small><i>This will be adding download button for Separation of Notice (SN) and
                        RTW Form  in Respondent</i></small>
                </the-label>

                <the-label v-show="showRtwDate"
                    label="Return to work order date"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.rtw_date"
                    />
                </the-label>

                <the-label v-show="showRtwAdvisedDate"
                    label="Advised to return to work later than"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.rtw_advice_date"
                    />
                </the-label>

                <label v-show="showSnTerminationDate">Separation Notice</label>
                <the-label v-show="showSnTerminationDate"
                    label="Termnination Date"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.sn_termination_date"
                    />
                </the-label>

                <the-label v-show="showSnAbsencesDate"
                    label="Date of Absences"
                    :asterisk="true">
                    <br>
                    <div class="col-lg-6 m-b-md">
                        <small>Start</small>
                        <datepicker
                            :input-class="'custom-datepicker'"
                            :format="'yyyy-MM-dd'"
                            :calendar-button="calendarButton"
                            :calendar-button-icon="'fa fa-calendar'"
                            :use-utc="true"
                            v-model="incident.sn_date_absence_start" />
                    </div>
                    <div class="col-lg-6 m-b-md">
                        <small>End</small>
                        <datepicker
                            :input-class="'custom-datepicker'"
                            :format="'yyyy-MM-dd'"
                            :calendar-button="calendarButton"
                            :calendar-button-icon="'fa fa-calendar'"
                            :use-utc="true"
                            v-model="incident.sn_date_absence_end" />
                    </div>
                </the-label>

                <the-label label="HR Witness">
                    <vSelect
                        multiple
                        v-model="incident.witness_user_id"
                        :options="selectedUser"
                        label="text"
                        :disabled="disabledWitness"
                    />
                </the-label>

                <the-label label="HR Invite">
                    <vSelect
                        multiple
                        v-model="incident.invite_user_id"
                        :options="selectedUser"
                        label="text"
                        @input="eventInvite"
                        :disabled="disabledInvite"
                    />
                </the-label>

                <the-label label="Type of Invite">
                    <vSelect
                        :options="selectedTypeInvite"
                        label="text"
                        v-model="incident.type_invite"
                        :disabled="disabledInviteType"
                    />
                </the-label>
                <input-file-uploader label="Attachments" @change="attachFile" />

                <div v-if="!incident.offense">
                    <the-label label="Initial Instance" :asterisk="true" >
                        <vSelect
                            v-model="incident.initial_instance"
                            :options="selectedInstance"
                            :disabled="disabledInitials"
                            @input="initials"
                            label="text"
                        />
                    </the-label>

                    <the-label label="Initial Case Closure" :asterisk="true">
                        <vSelect
                            v-model="incident.initial_irr_id"
                            :options="selectedIRR"
                            :disabled="disabledInitials"
                            @input="initials"
                            label="text"
                        />
                    </the-label>
                </div>

                <div v-else>
                    <div v-for="(off_multi, index) in incident.offense_multiple" :key="index"
                    style="boder:1px solid;">
                        <strong>Provision : </strong> {{ off_multi.category.name }}<br />
                        <strong>Offense : </strong>
                        {{ off_multi.offense }}
                        <strong style="color:red;" v-if="off_multi.offense_archived">
                            {{ off_multi.offense_archived }}
                        </strong><br />
                        <the-label label="Initial Instance" :asterisk="true" >
                            <vSelect
                                v-model="incident.offense_instance[index]"
                                :options="selectedInstance"
                                :disabled="disabledInitials"
                                @input="multipleInitialInstance(index)"
                                label="text"
                            />
                        </the-label>
                        <the-label label="Initial Case Closure" :asterisk="true">
                            <vSelect
                                v-model="incident.offense_case_closure[index]"
                                :options="selectedIRR"
                                :disabled="disabledInitials"
                                @input="multipleInitialCase"
                                label="text"
                            />
                        </the-label>
                    </div>
                </div>

                <fieldset class="fieldsetCustom">
                <legend class="legendCustom" >Signatories</legend>

                <table class="table table-borderless">
                    <tr>
                        <td>
                            <fieldset class="fieldsetCustom" style="margin-right: 5px;">
                                <legend class="legendCustom" >Prepared By:</legend>

                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            <the-label style="font-size:10px;" label="First Name"
                                            :asterisk="true">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.prepared_by_first"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Last Name" :asterisk="true">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.prepared_by_last"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Designation" :asterisk="true">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.prepared_by_designation"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Employee Number" :asterisk="true">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.prepared_by_empno"
                                                    @input="employeeSignatory(incident.prepared_by_empno, 'prepared_by')"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>

                        <td>
                            <fieldset class="fieldsetCustom" style="margin-left: 5px;">
                                <legend class="legendCustom" >Requested By:</legend>

                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            <the-label style="font-size:10px;" label="First Name" :asterisk="true">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.requested_by_first"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Last Name" :asterisk="true">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.requested_by_last"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Designation" :asterisk="true">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.requested_by_designation"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Employee Number" :asterisk="true">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.requested_by_empno"
                                                    @input="employeeSignatory(incident.requested_by_empno, 'requested_by')"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <table class="table table-borderless">
                    <tr>
                        <td>
                            <fieldset class="fieldsetCustom" style="margin-right: 5px;">
                                <legend class="legendCustom" >Approved By:</legend>

                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            <the-label style="font-size:10px;" label="First Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.approved_by_first"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Last Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.approved_by_last"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Designation">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.approved_by_designation"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Employee Number">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.approved_by_empno"
                                                    @input="employeeSignatory(incident.approved_by_empno,
                                                    'approved_by')"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>

                        <td>
                            <fieldset class="fieldsetCustom" style="margin-left: 5px;">
                                <legend class="legendCustom" >Noted By:</legend>

                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            <the-label style="font-size:10px;" label="First Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted1_by_first"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Last Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted1_by_last"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Designation">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted1_by_designation"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Employee Number">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted1_by_empno"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <table class="table table-borderless">
                    <tr>
                        <td>
                            <fieldset class="fieldsetCustom" style="margin-right: 5px;">
                                <legend class="legendCustom" >Noted By:</legend>

                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            <the-label style="font-size:10px;" label="First Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted2_by_first"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Last Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted2_by_last"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Designation">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted2_by_designation"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Employee Number">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted2_by_empno"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>

                        <td>
                            <fieldset class="fieldsetCustom" style="margin-left: 5px;">
                                <legend class="legendCustom" >Noted By:</legend>

                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            <the-label style="font-size:10px;" label="First Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted3_by_first"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Last Name">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted3_by_last"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Designation">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted3_by_designation"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>

                                            <the-label style="font-size:10px;" label="Employee Number">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="margin-bottom: 10px;"
                                                    v-model="incident.noted3_by_empno"
                                                    :disabled="disabledSignatory"
                                                />
                                            </the-label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                </fieldset>

                <button-submit :disabled='!isComplete'/>

            </div>
        </the-form>
    </div>
</template>
<script>

    export default {
        props: {
            incidentProps: Object,
        },

        data: function () {
            return {
                laddabtn : '',
                requireRemarks: false,
                user: this.$ep.user,
                role: this.$ep.role,
                designation: this.$ep.user.designation.name,
                incident: {
                    respondent_id: '',
                    irr_exist: '',
                    complainant_id: '',
                    hrbp_user_id: '',
                    remarks: '',
                    is_generate_nte_invalid_ir: '',
                    is_admin_hearing: 0,
                    date_admin_hearing: '',
                    date_da: '',
                    is_under_investigation: 0,
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
                    offense_multiple: [],
                    offense_case_closure: [],
                    instance_multiple: [],
                    offense_instance: [],
                    irr_multiple: [],
                    time_admin_hearing:'',
                    meeting_place:'',
                    ir_number: '',
                    attachments: '',
                    status_exist_id: '',
                    mom_attachment: [],
                    mom: '',
                    rtw: 0,
                    rtw_date: '',
                    rtw_advice_date: '',
                    sn_termination_date: '',
                    sn_date_absence_start: '',
                    sn_date_absence_end: '',
                    reported: '',
                    attached_hr: [],
                    respondent_email: '',
                    supervisor_email: '',
                    manager_email: '',
                    hrbp_user: '',
                    created_at: '',
                    updated_at: '',
                    status_id: {
                        text: "In Progress",
                        value: 2,
                        selected: "selected",
                    },
                    hr_user_id: {
                        value: '',
                        text: '',
                        selected: "selected"
                    },
                    initial_instance: '',
                    initial_irr_id: '',
                    preventive_suspension_start: '',
                    preventive_suspension_end: '',
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
                },
                disabledIsAdminHearing: true,
                disabledSignatory: false,
                disabledInitials: false,
                requiredInitials: false,
                requiredAsterisk: false,
                disabledDateHearing: true,
                disabledIsUnderInvestigation: true,
                disabledIRR: true,
                disabledInvite: true,
                disabledInviteType: true,
                disabledWitness: true,
                calendarButton: false,
                selectedUser: [],
                selectedIRR: [],
                disabledRemarks: false,
                disabledAssignHR: false,
                disabledStatus: false,
                selectedHrbp: [],
                selectedStatus: this.$constants.Status,
                selectedIsGenerateNte: this.$constants.Is_generate_nte,
                selectedTime: this.$constants.Time,
                selectedMeetingPlace: [],
                selectedTypeInvite: [
                    'For Investigation',
                    'For Admin Hearing'
                ],
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                showTime: false,
                showMeetingPlace: false,
                form: new FormData,
                disabledStatusIR: false,
                irStatusNotNew: 0,
                showDateHearing: false,
                showRtw: false,
                showRtwDate: false,
                showRtwAdvisedDate: false,
                showSnTerminationDate: false,
                showSnAbsencesDate: false,
                disabledDates: {
                    to: new Date(Date.now() - 8640000)
                },
                selectedInstance: this.$constants.Instance_List,
                showPSDate: false,
                downloadHrAttach: '',
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
            this.incident.hrbp_user_id = this.incidentProps.hrbp_user_id;
            this.incident.reported = this.incidentProps.reported;
            this.incident.date_da = this.incidentProps.date_da;
            this.incident.irr_exist = this.incidentProps.irr_exist;
            this.incident.mom_attachment = this.incidentProps.mom_attachment;
            this.incident.mom = this.incidentProps.mom;

            if (this.incidentProps.updated_at) {
                this.incident.updated_at = this.incidentProps.updated_at;
            }

            // console.log("incidentform this.incidentProps.progression_offense==",
            // this.incidentProps.progression_offense)

            // if (this.incidentProps.progression_offense == 'seventh_offense') {
            //     this.incident.initial_instance = { "value": 7, "text": "7th Instance" };
            // } else if (this.incidentProps.progression_offense == 'sixth_offense') {
            //     this.incident.initial_instance = { "value": 7, "text": "7th Instance" };
            // } else if (this.incidentProps.progression_offense == 'fifth_offense') {
            //     this.incident.initial_instance = { "value": 6, "text": "6th Instance" };
            // } else if (this.incidentProps.progression_offense == 'fourth_offense') {
            //     this.incident.initial_instance = { "value": 5, "text": "5th Instance" };
            // } else if (this.incidentProps.progression_offense == 'third_offense') {
            //     this.incident.initial_instance = { "value": 4, "text": "4th Instance" };
            // } else if (this.incidentProps.progression_offense == 'second_offense') {
            //     this.incident.initial_instance = { "value": 3, "text": "3rd Instance" };
            // } else if (this.incidentProps.progression_offense == 'first_offense') {
            //     this.incident.initial_instance = { "value": 2, "text": "2nd Instance" };
            // } else {
            //     if (typeof this.incident.offense_id != "undefined" && this.incident.offense_id
            // != null && this.incident.offense_id.length != null
            //         && this.incident.offense_id.length > 0) {
            //         this.incident.initial_instance = '';
            //     } else {
            //         this.incident.initial_instance = { "value": 1, "text": "1st Instance" };
            //     }
            // }

            if (this.incidentProps.initial_instance == "1st Instance") {
                this.incident.initial_instance = { "value": 1, "text": "1st Instance" };

            } else if (this.incidentProps.initial_instance == "2nd Instance") {
                this.incident.initial_instance = { "value": 2, "text": "2nd Instance" };

            } else if (this.incidentProps.initial_instance == "3rd Instance") {
                this.incident.initial_instance = { "value": 3, "text": "3rd Instance" };

            } else if (this.incidentProps.initial_instance == "4th Instance") {
                this.incident.initial_instance = { "value": 4, "text": "4th Instance" };

            } else if (this.incidentProps.initial_instance == "5th Instance") {
                this.incident.initial_instance = { "value": 5, "text": "5th Instance" };

            } else if (this.incidentProps.initial_instance == "6th Instance") {
                this.incident.initial_instance = { "value": 6, "text": "6th Instance" };

            } else if (this.incidentProps.initial_instance == "7th Instance") {
                this.incident.initial_instance = { "value": 7, "text": "7th Instance" };

            } else {
                this.incident.initial_instance = { "value": 1, "text": "1st Instance" };
            }

            if (this.incidentProps.hrbp_user_employee_no) {
                this.incident.prepared_by_first = this.incidentProps.hrbp_user_first;
                this.incident.prepared_by_last = this.incidentProps.hrbp_user_last;
                this.incident.prepared_by_empno = this.incidentProps.hrbp_user_employee_no;
                this.incident.prepared_by_designation = this.incidentProps.hrbp_user_designation;
            }

            if (this.incidentProps.supervisor_employee_no) {
                this.incident.requested_by_first = this.incidentProps.supervisor_first;
                this.incident.requested_by_last = this.incidentProps.supervisor_last;
                this.incident.requested_by_empno = this.incidentProps.supervisor_employee_no;
                this.incident.requested_by_designation = this.incidentProps.supervisor_designation;
            }

            if (this.incidentProps.manager_employee_no) {
                this.incident.approved_by_first = this.incidentProps.manager_first;
                this.incident.approved_by_last = this.incidentProps.manager_last;
                this.incident.approved_by_empno = this.incidentProps.manager_employee_no;
                this.incident.approved_by_designation = this.incidentProps.manager_designation;
            }

            if (this.incidentProps.hrbp_user) {
                    this.incident.hrbp_user = this.incidentProps.hrbp_user;
            }

            if (this.incidentProps.incident_report) {
                this.incident.status_id = this.incidentProps.status_id;

            } else if(this.incidentProps.status_id > 1) {
                this.incident.status_id = this.incidentProps.status_id;
            }

            if (this.incidentProps.attached_hr) {
                this.incident.attached_hr = this.incidentProps.attached_hr;
            }

            if (this.incidentProps.hrbp_user_id == this.user.id) {
                this.incident.hr_user_id = {
                    text: this.user.first_name +" "+ this.user.last_name,
                    value: this.user.id,
                    selected: "selected"
                };
            } else {
                this.incident.hr_user_id = '';
            }

            this.incident.respondent_email = this.incidentProps.respondent_email;

            if (this.incidentProps.supervisor_email) {
                this.incident.supervisor_email = this.incidentProps.supervisor_email;
            }

            if (this.incidentProps.manager_email) {
                this.incident.manager_email = this.incidentProps.manager_email;
            }

            this.$constants.Admin_API.get("/hrbp/cluster/dropdown/hrbpById")
                .then(response => {
                this.selectedHrbp = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.$constants.Default_API.get("/dropdown/users")
                .then(response => {
                this.selectedUser = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.$constants.Coc_API.get("/dropdown/irr")
                .then(response => {
                this.selectedIRR = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.incident.attachments = [];

            this.$constants.Admin_API.get("/incidentreport/dropdown/office/location")
                .then(response => {
                this.selectedMeetingPlace = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.checkArray(this.incident.offense_id);
            if (this.incident.offense_id) {
                for (let c in this.incident.offense_id){
                    this.findMultipleOffense(this.incident.offense_id[c]);
                    // this.findMultipleInstance(this.incident.offense_id[c]);
                }
            }
        },
        mounted() {
            if(this.incident.incident_report == null){
                this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
            }
        },
        computed: {
            isComplete () {
                if (this.incident.is_generate_nte_invalid_ir == 1 && this.incident.is_admin_hearing == 1)
                { //Generate NTE w/ Admin hearing
                    return this.incident.hr_user_id && this.incident.status_id && this.requireRemarks
                    && this.incident.is_generate_nte_invalid_ir && this.incident.date_admin_hearing
                    && this.incident.time_admin_hearing && this.incident.meeting_place && this.requiredInitials
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;

                } else if (this.incident.is_generate_nte_invalid_ir == 4 && this.incident.is_admin_hearing == 1)
                { //Generate NTE w/ PS
                    return this.incident.hr_user_id && this.incident.status_id && this.requireRemarks
                    && this.incident.is_generate_nte_invalid_ir && this.incident.date_admin_hearing
                    && this.incident.time_admin_hearing && this.incident.meeting_place && this.requiredInitials
                    && this.incident.preventive_suspension_start && this.incident.preventive_suspension_end
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;

                } else if (this.incident.is_admin_hearing == 2 && this.incident.rtw == 1) { //Report to work order (RTW)
                    return this.incident.hr_user_id && this.incident.status_id && this.requireRemarks
                    && this.incident.is_generate_nte_invalid_ir && this.incident.rtw_date
                    && this.incident.rtw_advice_date && this.incident.sn_termination_date
                    && this.incident.sn_date_absence_start && this.requiredInitials
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;

                } else {
                    return this.incident.hr_user_id && this.incident.status_id && this.requireRemarks
                    && this.incident.is_generate_nte_invalid_ir && this.requiredInitials
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;
                }
            },
        },
        methods: {
            employeeSignatory(employee_no, signatory) {
                let check_employee_no = employee_no.length;

                if (check_employee_no >= 8) {
                    let getAxios = '';
                    getAxios = this.$constants.Admin_API.get("/incidentreport/signatory/employee/"
                    +employee_no);

                    getAxios.then(response => {
                        if (response.data.employee_no) {
                            if (signatory == 'prepared_by') {
                                this.incident.prepared_by_designation = response.data.designation.name;
                                this.incident.prepared_by_last = response.data.last_name;
                                this.incident.prepared_by_first = response.data.first_name;
                            } else if (signatory == 'requested_by') {
                                this.incident.requested_by_designation = response.data.designation.name;
                                this.incident.requested_by_last = response.data.last_name;
                                this.incident.requested_by_first = response.data.first_name;
                            } else if (signatory == 'approved_by') {
                                this.incident.approved_by_designation = response.data.designation.name;
                                this.incident.approved_by_last = response.data.last_name;
                                this.incident.approved_by_first = response.data.first_name;
                            }
                        }

                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            eventUpdate() {

                for(let w=0; w<this.incident.witness_user_id.length; w++){
                    this.incident.witness_multiple.push(this.incident.witness_user_id[w].value);
                }

                for(let i=0; i<this.incident.invite_user_id.length; i++){
                    this.incident.invite_multiple.push(this.incident.invite_user_id[i].value);
                }

                if (this.incident.offense_id.length > 0) {
                    for(let c=0; c<this.incident.offense_id.length; c++){

                        if (this.incident.offense_instance[c].text) {
                            this.incident.offense_instance[c] = this.incident.offense_instance[c].text;
                        }

                        this.incident.instance_multiple.push(this.incident.offense_instance[c]);
                    }

                    for(let l=0; l<this.incident.offense_id.length; l++){
                        this.incident.irr_multiple.push(this.incident.offense_case_closure[l].value);
                    }
                }

                for(let r=0; r<this.incident.attachments.length;r++){
                    this.form.append('pics[]', this.incident.attachments[r]);
                }
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }

                this.laddabtn.start();
                let incident = this.incident;
                let getAxios = '';
                let instance = '';
                let irr_id = '';
                this.$constants.Incident_API.post('/attach/file', this.form, config)
                    .then(response => {

                    incident.attachments = response.data.uploaded

                    getAxios = this.$constants.Admin_API.get("/incidentreport/create", {
                        params: {
                            respondent_id: incident.respondent_id,
                            complainant_id: incident.complainant_id,
                            status_id: incident.status_id.value,
                            remarks: incident.remarks,
                            is_generate_nte_invalid_ir: incident.is_generate_nte_invalid_ir,
                            preventive_suspension_start: incident.preventive_suspension_start,
                            preventive_suspension_end: incident.preventive_suspension_end,
                            date_admin_hearing: incident.date_admin_hearing,
                            time_admin_hearing: incident.time_admin_hearing.value,
                            is_under_investigation: incident.is_under_investigation,
                            witness_user_id: incident.witness_multiple,
                            invite_user_id: incident.invite_multiple,
                            type_invite: incident.type_invite,
                            respondent_user_id: incident.respondent_user_id,
                            prescriptive_period: incident.prescriptive_period,
                            complainant_user_id: incident.complainant_user_id,
                            offense_id: incident.offense_id,
                            hr_user_id: incident.hr_user_id.value,
                            meeting_place: incident.meeting_place.text,
                            ir_number: incident.ir_number,
                            attachments: incident.attachments,
                            rtw: incident.rtw,
                            rtw_date: incident.rtw_date,
                            rtw_advice_date: incident.rtw_advice_date,
                            sn_termination_date: incident.sn_termination_date,
                            sn_date_absence_start: incident.sn_date_absence_start,
                            sn_date_absence_end: incident.sn_date_absence_end,
                            reported: incident.reported,
                            respondent_email: incident.respondent_email,
                            supervisor_email: incident.supervisor_email,
                            manager_email: incident.manager_email,
                            initial_irr_id: incident.initial_irr_id.value,
                            initial_instance: incident.initial_instance.text,
                            instance_multiple: incident.instance_multiple,
                            irr_multiple: incident.irr_multiple,
                            prepared_by_first: incident.prepared_by_first,
                            prepared_by_last: incident.prepared_by_last,
                            prepared_by_designation: incident.prepared_by_designation,
                            prepared_by_empno: incident.prepared_by_empno,
                            requested_by_first: incident.requested_by_first,
                            requested_by_last: incident.requested_by_last,
                            requested_by_designation: incident.requested_by_designation,
                            requested_by_empno: incident.requested_by_empno,
                            approved_by_first: incident.approved_by_first,
                            approved_by_last: incident.approved_by_last,
                            approved_by_designation: incident.approved_by_designation,
                            approved_by_empno: incident.approved_by_empno,
                            noted1_by_first: incident.noted1_by_first,
                            noted1_by_last: incident.noted1_by_last,
                            noted1_by_designation: incident.noted1_by_designation,
                            noted1_by_empno: incident.noted1_by_empno,
                            noted2_by_first: incident.noted2_by_first,
                            noted2_by_last: incident.noted2_by_last,
                            noted2_by_designation: incident.noted2_by_designation,
                            noted2_by_empno: incident.noted2_by_empno,
                            noted3_by_first: incident.noted3_by_first,
                            noted3_by_last: incident.noted3_by_last,
                            noted3_by_designation: incident.noted3_by_designation,
                            noted3_by_empno: incident.noted3_by_empno,
                        }
                    });

                    getAxios.then(response => {
                        this.laddabtn.stop();
                        this.$bus.$emit('init_modal', false);
                        this.incident = response.data;
                        this.$success('Successfully Saved!')
                        this.$bus.$emit('updateList');
                        this.$bus.$emit('updateDashboard');
                        this.$bus.$emit('changeIrStatusNotNew', this.irStatusNotNew);
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                })
                .catch(error => { //end attachments
                    this.globalErrorHandling(error);
                });
            },
            findMultipleOffense(offense_id) {
                this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                .then(respo => {
                    this.incident.offense_multiple.push(respo.data);
                    this.findMultipleInstance(offense_id);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            findMultipleInstance(offense_id) {
                this.$constants.Coc_API.get("/offense/progression/"+offense_id+"/"
                +this.incidentProps.respondent_user_id)
                .then(response => {

                    let instance_multiple = [];
                    let case_closure_multiple = [];
                    if (response.data == 'No occurrence') {
                        instance_multiple = "1st Instance";
                        case_closure_multiple["text"] = 'Verbal Written Warning';
                        case_closure_multiple["value"] = 1;
                    }

                    if (response.data.seventh_respondent_id) {
                        instance_multiple = "7th Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.seventh_instance.name
                        ? response.data.offense.instance.seventh_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.seventh_instance.id
                        ? response.data.offense.instance.seventh_instance.id : '';

                    } else if (response.data.sixth_respondent_id) {
                        instance_multiple = "7th Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.seventh_instance.name
                        ? response.data.offense.instance.seventh_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.seventh_instance.id
                        ? response.data.offense.instance.seventh_instance.id : '';

                    } else if (response.data.fifth_respondent_id) {
                        instance_multiple = "6th Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.sixth_instance.name
                        ? response.data.offense.instance.sixth_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.sixth_instance.id
                        ? response.data.offense.instance.sixth_instance.id : '';

                    } else if (response.data.fourth_respondent_id) {
                        instance_multiple = "5th Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.fifth_instance.name
                        ? response.data.offense.instance.fifth_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.fifth_instance.id
                        ? response.data.offense.instance.fifth_instance.id : '';

                    } else if (response.data.third_respondent_id) {
                        instance_multiple = "4th Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.fourth_instance.name
                        ? response.data.offense.instance.fourth_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.fourth_instance.id
                        ? response.data.offense.instance.fourth_instance.id : '';

                    } else if (response.data.second_respondent_id) {
                        instance_multiple = "3rd Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.third_instance.name
                        ? response.data.offense.instance.third_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.third_instance.id
                        ? response.data.offense.instance.third_instance.id : '';

                    } else if (response.data.first_respondent_id) {
                        instance_multiple = "2nd Instance";
                        case_closure_multiple["text"] = response.data.offense.instance.second_instance.name
                        ? response.data.offense.instance.second_instance.name : '';
                        case_closure_multiple["value"] = response.data.offense.instance.second_instance.id
                        ? response.data.offense.instance.second_instance.id : '';
                    }

                    this.incident.offense_instance.push(instance_multiple)
                    this.incident.offense_case_closure.push(case_closure_multiple);

                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

            },
            // findMultipleInstance(offense_id) {
            //     this.$constants.Coc_API.get("/offense/progression/"+offense_id+"/"+this.incidentProps.respondent_user_id)
            //     .then(response => {
            //         let instance_multiple = [];
            //         if (response.data == 'No occurrence') {
            //            instance_multiple = { "value": 1, "text": "1st Instance" };
            //         } else if (response.data.seventh_respondent_id) {
            //             instance_multiple = { "value": 7, "text": "7th Instance" };
            //         } else if (response.data.sixth_respondent_id) {
            //             instance_multiple = { "value": 7, "text": "7th Instance" };
            //         } else if (response.data.fifth_respondent_id) {
            //             instance_multiple = { "value": 6, "text": "6th Instance" };
            //         } else if (response.data.fourth_respondent_id) {
            //             instance_multiple = { "value": 5, "text": "5th Instance" };
            //         } else if (response.data.third_respondent_id) {
            //             instance_multiple = { "value": 4, "text": "4th Instance" };
            //         } else if (response.data.second_respondent_id) {
            //             instance_multiple = { "value": 3, "text": "3rd Instance" };
            //         } else if (response.data.first_respondent_id) {
            //             instance_multiple = { "value": 2, "text": "2nd Instance" };
            //         }
            //         this.incident.offense_instance.push(instance_multiple)
            //         this.incident.offense_case_closure.push(null)
            //     })
            //     .catch(error => {
            //         this.globalErrorHandling(error);
            //     });
            // },
            eventSingleCaseClosure(instance, offense_id){
                this.$constants.Coc_API.get("/offense/coc/"+offense_id)
                .then(response => {
                    if (instance == 'one') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.first_instance.id,
                            'text': response.data.instance.first_instance.name
                        }
                    }
                    if (instance == 'two') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.second_instance.id,
                            'text': response.data.instance.second_instance.name
                        }
                    }
                    if (instance == 'three') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.third_instance.id,
                            'text': response.data.instance.third_instance.name
                        }
                    }
                    if (instance == 'four') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.fourth_instance.id,
                            'text': response.data.instance.fourth_instance.name
                        }
                    }
                    if (instance == 'five') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.fifth_instance.id,
                            'text': response.data.instance.fifth_instance.name
                        }
                    }
                    if (instance == 'six') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.sixth_instance.id,
                            'text': response.data.instance.sixth_instance.name
                        }
                    }
                    if (instance == 'seven') {
                        this.incident.initial_irr_id = {
                            'value': response.data.instance.seventh_instance.id,
                            'text': response.data.instance.seventh_instance.name
                        }
                    }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventMultipleCaseClosure(instance, index){
                let offense_id = this.incidentProps.offense_id[index]
                let case_closure = [];

                this.$constants.Coc_API.get("/offense/coc/"+ offense_id)
                .then(response => {
                    if (instance == 'one') {
                        case_closure = {
                            'value': response.data.instance.first_instance.id,
                            'text': response.data.instance.first_instance.name,
                        }
                    }
                    if (instance == 'two') {
                        case_closure = {
                            'value': response.data.instance.second_instance.id,
                            'text': response.data.instance.second_instance.name,
                        }
                    }
                    if (instance == 'three') {
                        case_closure = {
                            'value': response.data.instance.third_instance.id,
                            'text': response.data.instance.third_instance.name,
                        }
                    }
                    if (instance == 'four') {
                        case_closure = {
                            'value': response.data.instance.fourth_instance.id,
                            'text': response.data.instance.fourth_instance.name,
                        }
                    }
                    if (instance == 'five') {
                        case_closure = {
                            'value': response.data.instance.fifth_instance.id,
                            'text': response.data.instance.fifth_instance.name,
                        }
                    }
                    if (instance == 'six') {
                        case_closure = {
                            'value': response.data.instance.sixth_instance.id,
                            'text': response.data.instance.sixth_instance.name,
                        }
                    }
                    if (instance == 'seven') {
                        case_closure = {
                            'value': response.data.instance.seventh_instance.id,
                            'text': response.data.instance.seventh_instance.name,
                        }
                    }
                    this.$set(this.incident.offense_case_closure, index, case_closure);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            checkArray(offense) {
                this.incident.offense = Array.isArray(offense);
            },
            multipleInitialInstance(index) {
                if (this.incident.offense_instance[index].text == '7th Instance') {
                    this.eventMultipleCaseClosure('seven', index);
                }
                if (this.incident.offense_instance[index].text == '6th Instance') {
                    this.eventMultipleCaseClosure('six', index)
                }
                if (this.incident.offense_instance[index].text == '5th Instance') {
                    this.eventMultipleCaseClosure('five', index);
                }
                if (this.incident.offense_instance[index].text == '4th Instance') {
                    this.eventMultipleCaseClosure('four', index);
                }
                if (this.incident.offense_instance[index].text == '3rd Instance') {
                    this.eventMultipleCaseClosure('three', index);
                }
                if (this.incident.offense_instance[index].text == '2nd Instance') {
                    this.eventMultipleCaseClosure('two', index);
                }
                if (this.incident.offense_instance[index].text == '1st Instance') {
                   this.eventMultipleCaseClosure('one', index);
                }

                // for(let c=0; c < this.incident.offense_id.length; c++){
                for (let c in this.incident.offense_id){
                    if (this.incident.offense_instance[c]
                        && this.incident.offense_case_closure[c]) {
                            this.requiredInitials = true;
                    } else {
                        this.requiredInitials = false;
                        break;
                    }
                }
            },
            multipleInitialCase(e) {
                // for(let c=0; c < this.incident.offense_id.length; c++){
                    for(let c in this.incident.offense_id){
                    if (this.incident.offense_instance[c]
                        && this.incident.offense_case_closure[c]) {
                            this.requiredInitials = true;
                    } else {
                        this.requiredInitials = false;
                        break;
                    }
                }
            },
            initials(e) {
                if (this.incident.initial_instance && this.incident.initial_irr_id) {
                    this.requiredInitials = true;
                } else {
                    this.requiredInitials = false;
                }
                if (e.text === "7th Instance") {
                    this.eventSingleCaseClosure('seven', this.incidentProps.offense_id);
                } else if (e.text === "6th Instance") {
                    this.eventSingleCaseClosure('six', this.incidentProps.offense_id);
                } else if (e.text === "5th Instance") {
                    this.eventSingleCaseClosure('five', this.incidentProps.offense_id);
                } else if (e.text === "4th Instance") {
                    this.eventSingleCaseClosure('four', this.incidentProps.offense_id);
                } else if (e.text === "3rd Instance") {
                    this.eventSingleCaseClosure('three', this.incidentProps.offense_id);
                } else if (e.text === "2nd Instance") {
                    this.eventSingleCaseClosure('two', this.incidentProps.offense_id);
                } else if (e.text === "1st Instance") {
                    this.eventSingleCaseClosure('one', this.incidentProps.offense_id);
                }
            },
            eventDateHearing(checkedRadio) {
                if(checkedRadio == 1){
                    // this.disabledDateHearing = false;
                    this.calendarButton = true;
                    this.showTime = true;
                    this.showMeetingPlace = true;
                    this.showDateHearing = true;
                    this.incident.rtw = 0;
                    this.showRtw = false;
                    this.showRtwDate = false;
                    this.showRtwAdvisedDate = false;
                    this.showSnTerminationDate = false;
                    this.showSnAbsencesDate = false;
                    // this.asteriskAttachment = false;

                } else if(checkedRadio == 2){
                    // this.disabledDateHearing = true;
                    this.calendarButton = true;
                    this.incident.date_admin_hearing = "";
                    this.incident.time_admin_hearing = "";
                    this.incident.meeting_place = "";
                    this.showTime = false;
                    this.showMeetingPlace = false;
                    this.showDateHearing = false;
                    this.incident.rtw = 1;
                    this.showRtw = true;
                    this.showRtwDate = true;
                    this.showRtwAdvisedDate = true;
                    this.showSnTerminationDate = true;
                    this.showSnAbsencesDate = true;
                    // this.asteriskAttachment = true;

                 } else if(checkedRadio == 0){
                    this.disabledDateHearing = true;
                    this.calendarButton = false;
                    this.incident.date_admin_hearing = "";
                    this.incident.time_admin_hearing = "";
                    this.incident.meeting_place = "";
                    this.showTime = false;
                    this.showMeetingPlace = false;
                    this.showDateHearing = false;
                    this.incident.rtw = 0;
                    this.showRtw = false;
                    this.showRtwDate = false;
                    this.showRtwAdvisedDate = false;
                    this.showSnTerminationDate = false;
                    this.showSnAbsencesDate = false;
                    // this.asteriskAttachment = false;
                }
            },
            eventIRR(underInvestigation) {
                if(underInvestigation == 1){
                    this.disabledIRR = false;
                } else {
                    this.disabledIRR = true;
                    // this.incident.irr_id = "";
                }
            },
            eventInvite() {
                if (this.incident.invite_user_id) {
                    this.disabledInviteType = false;
                } else {
                    this.incident.type_invite = "";
                    this.disabledInviteType = true;
                }
            },
            eventGenerateNTE(generateNTE) {
                if (generateNTE == 1) {
                    this.disabledIsAdminHearing = false
                    this.disabledWitness = false
                    this.disabledInvite = false
                    this.showPSDate = false

                } else if (generateNTE == 2 || generateNTE == 3) {
                    this.disabledDateHearing = true
                    this.disabledIsAdminHearing = true
                    this.disabledWitness = true
                    this.disabledInvite = true

                    this.eventDateHearing(0);
                    this.eventIRR(0);
                    this.incident.witness_user_id = "";
                    this.incident.invite_user_id = "";
                    this.incident.type_invite = "";
                    this.incident.is_admin_hearing = 0;
                    this.showPSDate = false

                } else if (generateNTE == 4) {
                    this.disabledIsUnderInvestigation = false
                    this.disabledIsAdminHearing = false
                    this.disabledWitness = false
                    this.disabledInvite = false
                    this.disabledIsAdminHearing = true
                    this.incident.is_admin_hearing = 1
                    this.showPSDate = true
                    this.eventDateHearing(1);
                }
            },
            attachFile(e){
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    return false;
                }

                for(let i=0; i<selectedFiles.length;i++){
                    this.incident.attachments.push(selectedFiles[i]);
                }
            },
            eventStatusChange(status_id){

                if (status_id.value == 4) { //Closed
                    this.eventDateHearing(0)
                    this.disabledStatusIR = true;
                    this.disabledWitness = true;
                    this.disabledInvite = true;
                    this.disabledInviteType = true;
                    this.disabledIsAdminHearing = true;
                    this.incident.is_generate_nte_invalid_ir = 2;
                    this.incident.is_admin_hearing = 0;
                    this.incident.date_admin_hearing = '';
                    this.incident.time_admin_hearing = '';
                    this.incident.meeting_place = '';
                    this.incident.witness_user_id = '';
                    this.incident.invite_user_id = '';
                    this.incident.type_invite = '';
                    this.incident.is_under_investigation = '';
                    this.disabledIsUnderInvestigation = true;
                    this.disabledIRR = true;
                    this.requiredInitials = true;
                    this.disabledSignatory = true;
                    this.disabledInitials = true;
                    this.disabledAssignHR = true;
                    this.disabledStatus = true;

                // } else {
                //     console.log("eventStatusChange else");
                //     this.incident.is_generate_nte_invalid_ir = '';
                //     this.disabledStatusIR = false;
                //     this.disabledWitness = false;
                //     this.disabledInvite = false;
                //     this.disabledInviteType = false;
                //     this.disabledIsAdminHearing = false;
                //     this.disabledRemarks = false;
                //     this.incident.is_under_investigation = 0;
                //     this.disabledIsUnderInvestigation = false;
                //     this.disabledIRR = false;
                //     this.requireRemarks = false;
                //     this.requiredInitials = false;
                //     this.disabledSignatory = false;
                //     this.disabledInitials = false;
                }
            },
            eventGenerateAdminHearing: function (complainant_id, respondent_user) {
                this.$swal({
                    title: 'Do You Want To Download this Admin Hearing?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            window.location.href = "/api/admin/incidentreport/generate_admin_hearing/"
                            + complainant_id + "/" + respondent_user;
                            this.$success('Download Admin Hearing downloaded successfully!');
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Download Admin Hearing has been cancelled');
                        }
                    })
            },

            changeRemark(e){
                if (this.incident.remarks != '' || this.incident.remarks !== ' '
                    || this.incident.remarks != null || this.incident.remarks) {
                    this.requireRemarks = true;
                } else if (this.incident.remarks == '' || this.incident.remarks === ' '
                || this.incident.remarks == null  || !this.incident.remarks) {
                    if (this.incident.remarks == '' || this.incident.remarks === ' ' ) {
                        this.incident.this.incident.remarks = ''
                    }
                    this.requireRemarks = false;
                }
                if (!this.incident.remarks) {
                    this.requireRemarks = false;
                }
            },
            dateRangeValidation(start, end){
                if (end < start) {
                    this.$swal({
                        text: "Preventive Suspension Date is not valid range",
                        type: "error",
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        this.incident.preventive_suspension_start = '';
                        this.incident.preventive_suspension_end = '';
                    });
                }
            },
            showLabelDownloadAttach(attach){
                this.downloadHrAttach = 'Download HR Attachments :';
            }
        }
    }
</script>
