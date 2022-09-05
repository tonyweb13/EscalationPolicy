<template>
    <the-form :asterisk="true" @submitForm="eventUpdate" class="m-t-xl" >
        <div slot="form-body">
            <div class="alert alert-danger" v-if="error_data">{{ error_data }}</div>

            <the-label label="Assign to HRBP" :asterisk="true">
                <vSelect
                    v-model="incident.hr_user_id"
                    :options="selectedHrbp"
                    label="text"
                />
            </the-label>

            <the-label label="Status" :asterisk="true">
                <vSelect
                    v-model="incident.status"
                    :options="selectedStatus"
                    label="text"
                    @input="eventStatusChange(incident.status)"
                />
            </the-label>

            <the-label label="Remarks" :asterisk="true">
                <vue-editor
                    v-model="incident.remarks"
                    @input="requiringRemark(incident.remarks)"
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
                        :disabled="disabledStatusIR || incident.status.value != 4"

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
            </the-label>

            <!-- start NTE w/ Preventive Suspension-->
            <the-label label="Preventive Suspension Date" class="col-lg-12"
            v-show="showPSDate" v-if="incident.is_generate_nte_invalid_ir == 4">
                <br>
                <the-label label="Start" :asterisk="true" class="col-lg-6">
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

                <the-label label="End" :asterisk="true" class="col-lg-6">
                <datepicker
                    :input-class="'custom-datepicker'"
                    :format="'yyyy-MM-dd'"
                    :calendar-button="true"
                    :calendar-button-icon="'fa fa-calendar'"
                    :use-utc="true"
                    placeholder="YYYY-MM-DD"
                    v-model="incident.preventive_suspension_end"
                    @input="dateRangeValidation(incident.preventive_suspension_start, incident.preventive_suspension_end)"
                />
                </the-label>
            </the-label>
            <!-- end NTE w/ Preventive Suspension-->

            <!-- start checked radio Date hearing -->
            <label v-show="showDateHearing" >Admin Hearing</label>
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
                <small><i>
                    This will be adding download button for Separation of Notice (SN) and RTW Form in Respondent
                </i></small>
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

            <label>Separation Notice</label>
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
                    @click="addedWitness(1)"
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

            <the-label label="NTE Status Stage of the Case">
                <vSelect
                    :options="selectedNteStageOfTheCase"
                    label="text"
                    v-model="incident.nte_stage_case"
                />
            </the-label>

            <the-label :label="downloadHrAttach" v-if="incident.attached_hr">
                <div v-for="attach1 in incident.attached_hr">
                    <ul v-for="attach2 in attach1.attachments.split(',')">
                        <li v-if="attach2.match(/hr/g)">
                            <a :href="'/api/admin/incidentreport/download/attachment/'+ attach2 | urlReplace">
                                {{ attach2 | urlReplace }}
                            </a>
                            <span v-if="showLabelDownloadAttach(attach2)"/>
                        </li>
                    </ul>
                </div>
            </the-label>

            <the-label :asterisk="true">
                <label-radio>
                    <input type="radio"
                        name="is_under_investigation"
                        id="is_under_investigation2"
                        v-model="incident.is_under_investigation"
                        @input="eventIRR(0)"
                        :value='0'
                        :disabled="disabledIsUnderInvestigation"
                    >
                    <label for="is_under_investigation2"><b> Still Under Investigation </b></label>
                </label-radio>
                <label-radio>
                    <input type="radio"
                        name="is_under_investigation"
                        id="is_under_investigation1"
                        v-model="incident.is_under_investigation"
                        @input="eventIRR(1)"
                        :value='1'
                        :disabled="disabledIsUnderInvestigation"
                    >
                    <label for="is_under_investigation1">
                        <b> Generate Case Closure </b>
                    </label>
                </label-radio>
                <vSelect v-if="incident.is_under_investigation == 1"
                    v-model="incident.irr_id"
                    @input="addLinkForm"
                    :options="selectedIRR"
                    label="text"
                    :disabled="disabledIRR"
                />
                <br>

                <div v-if="incident.offense_multiple.length > 1">
                    <div v-if="incident.irr_id">
                        <div v-for="(off_multi, index) in incident.offense_multiple" :key="index" style="boder:1px solid;">
                            <strong>Provision : </strong> {{ off_multi.category.name }}<br />
                            <strong>Offense : </strong>
                            {{ off_multi.offense }}
                            <strong style="color:red;" v-if="off_multi.offense_archived">
                                {{ off_multi.offense_archived }}
                            </strong><br />
                            <the-label label="Final Instance" :asterisk="true" >
                                <vSelect
                                    v-model="incident.offense_multiple[index].final_instance"
                                    :options="selectedInstance"
                                    @input="multipleFinalInstance"
                                    label="text"
                                />
                            </the-label>

                            <the-label label="Final Case Closure" :asterisk="true">
                                <vSelect
                                    v-model="incident.offense_multiple[index].final_irr"
                                    :options="selectedIRR"
                                    @input="multipleFinalCase"
                                    label="text"
                                />
                            </the-label>
                        </div>
                    </div>
                </div>
                <div v-else-if="!incident.offense_multiple.length > 1 || incident.irr_id">
                    <the-label label="Final Instance" v-if="incident.irr_id" :asterisk="true">
                        <vSelect
                            v-model="incident.final_instance"
                            :options="selectedInstance"
                            @input="finalInstanceIR(incident.remarks)"
                            label="text"
                        />
                    </the-label>
                    <the-label label="Final Case Closure" :asterisk="true">
                        <vSelect
                            v-model="incident.final_irr_single"
                            :options="selectedIRR"
                            @input="finalCaseClosure(incident.remarks)"
                            label="text"
                        />
                    </the-label>
                </div>

                <!-- Start Suspension Dates Multiple / Termination not included-->                
                <the-label v-if="showSuspensionDate == true" label="Suspension Date" :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="this.calendarButton = true"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.suspension_date"
                        @input="eventShowAddSuspension"
                        :disabledDates="disabledDates"
                    />
                </the-label>

                <div v-for="(sus_multi, index) in incident.suspension_multiple">
                    <fieldset class="fieldsetCustom">
                    <legend class="legendCustom" >
                    Suspension Date ( {{ suspensionCnt == index ? 2 : indexCnt+index }} )
                    </legend>

                    <the-label v-if="incident.irr_id" label="Suspension Date" :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="this.calendarButton = true"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="sus_multi.suspension_date_multi"
                        :disabledDates="disabledDates"
                        :asterisk="true"
                    />
                    </the-label>
                    <br>
                    <div class="pull-left" >
                        <button type="button" @click="deleteSuspension(index, sus_multi.suspension_date_multi)" class="btn btn-danger">
                            <i class="fa fa-remove"/> Delete Suspension Date
                            ( {{ suspensionCnt == index ? 2 : indexCnt+index }} )
                        </button>
                    </div>
                    </fieldset>
                </div>

                <div class="addOffenseCustom" v-if="showSuspensionDate == true && showAddSuspensionDate">
                    <div class="pull-right">
                        <button type="button" @click="addSuspension" class="btn btn-info">
                            <i class="fa fa-plus"/> Add Suspension Date
                        </button>
                    </div>
                </div>
                <hr v-if="showSuspensionDate == true && showAddSuspensionDate">
                <!-- End Suspension Dates Multiple-->

                <the-label label="Stage Case" v-if="incident.irr_id">
                    <vSelect
                        v-model="incident.stage_case"
                        :options="selectedStageCase"
                        label="text"
                    />
                </the-label>

                <the-label label="MoM Attachment" v-if="(incident.irr_id.value === 10
                    && incident.date_admin_hearing && incident.time_admin_hearing.value)">
                    <input-single-file-uploader label="Attachments" @change="attachFile" />
                </the-label>

                <label class="m-t-md" v-if="show_cancelled === true && incident.is_under_investigation === 1"> After careful and exhaustive deliberation, we found
                    this incident valid under grievance. HR intervention will be conducted accordingly to resolve the
                    matter at hand, meanwhile please observe confidentiality as we process your concern.

                </label>
                <the-label v-if="show_cancelled === true && incident.is_under_investigation === 1" label="Agreement" :asterisk="true">
                    <input type="text"
                        v-model="incident.agreement"
                        class="form-control"
                        label="text"
                        @input="changeType(incident.agreement)"
                    />
                </the-label>
                <a
                    v-if="show_cancelled && incident.is_under_investigation === 1 && checker  && incident.agreement !== '' && incident.agreement !== null "
                    :href="'/api/admin/incidentreport/downloadHrInterventionDocument/'+ this.editProps.respondent_id + '/' + this.incident.agreement + '/' + this.incident.ir_id + '/' + this.incident.irr_id.value"
                >
                    HR Intervention documentation.pdf
                </a>
                <br>
                <label-radio v-show="show_cancelled === true">
                    <input type="radio"
                        name="type_of_date"
                        id="type_of_date2"
                        v-model="incident.type_of_date"
                        :value='0'
                    >
                    <label for="type_of_date2"><b> Standard Date </b></label>
                </label-radio>
                <label-radio v-show="show_cancelled === true">
                    <input type="radio"
                        name="type_of_date"
                        id="type_of_date1"
                        v-model="incident.type_of_date"
                        :value='1'
                    >
                    <label for="type_of_date1">
                        <b> Immediately </b>
                    </label>
                </label-radio>
                <the-label v-show="show_cancelled === true"
                    label="Start Date"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.start_date"
                    />
                </the-label>
                <the-label v-show="show_cancelled === true && incident.type_of_date === 0"
                    label="Separation Date"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.takes_date"
                    />
                </the-label>
                <the-label v-show="show_cancelled === true && incident.type_of_date === 0"
                    label="Last Working Date"
                    :asterisk="true">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="incident.last_date"
                    />
                </the-label>
                <the-label v-show="show_termination === true"
                    label="Please indicate if employee waived/did not submit any"
                    :asterisk="true">
                    <vue-editor
                        v-model="incident.first_comment"
                        :editorToolbar="customToolbar"
                    />
                </the-label>
                <the-label v-show="show_termination === true"
                    label="State details of associate’s explanation and/or supporting documents"
                    :asterisk="true">
                    <vue-editor
                        v-model="incident.second_comment"
                        :editorToolbar="customToolbar"
                    />
                </the-label>
                <the-label v-show="this.show_mom === true || (incident.irr_id.value === 10
                    && incident.date_admin_hearing && incident.time_admin_hearing.value)  || (incident.irr_id.value
                    && in_lieu === true)"
                    label="Minutes of the Meeting and comments"
                    :asterisk="true">
                    <vue-editor
                        v-model="incident.mom"
                        :editorToolbar="customToolbar"
                    />
                </the-label>

                <the-label v-if="incident.irr_id" label="Additional Notes">
                    <vue-editor
                        v-model="incident.additional_notes"
                        :editorToolbar="customToolbar"
                    />
                </the-label>

            </the-label>

            <fieldset class="fieldsetCustom" v-if="incident.prepared_by_empno || incident.requested_by_empno">
            <legend class="legendCustom" >Signatories</legend>

            <table class="table table-borderless">
                <tr>
                    <td>
                        <fieldset class="fieldsetCustom" style="margin-right: 5px;">
                            <legend class="legendCustom" >Prepared By:</legend>

                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <the-label style="font-size:10px;" label="First Name" :asterisk="true">
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
                                                @input="employeeSignatory(incident.approved_by_empno, 'approved_by')"
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

            <button class="ladda-button btn btn-primary" data-style="slide-right"
            data-color="mint" type="submit" :disabled="!isComplete">
                <i class="fa fa-save"/> Update
            </button>

        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            editProps: Object,
        },

        data: function () {
            return {
                laddabtn : '',
                checkedRequiredRemarks: false,
                requireRemarks: false,
                requireIRR: false,
                form: new FormData,
                incident: {
                    complainant_id: '',
                    remarks: this.editProps.incident_report.remarks,
                    is_generate_nte_invalid_ir: this.editProps.incident_report.is_generate_nte_invalid_ir,
                    date_admin_hearing: this.editProps.incident_report.date_admin_hearing,
                    is_under_investigation: this.editProps.incident_report.is_under_investigation,
                    witness_user_id: this.editProps.incident_report.witness_user,
                    type_invite: this.editProps.incident_report.type_invite,
                    type_of_date: this.editProps.type_of_date,
                    start_date: this.editProps.start_date,
                    takes_date: this.editProps.takes_date,
                    last_date: this.editProps.last_date,
                    witness_multiple: [],
                    invite_multiple: [],
                    meeting_place: '',
                    respondent_user_id: this.editProps.respondent_user_id,
                    complainant_user_id: this.editProps.complainant_user_id,
                    ir_number: this.editProps.ir_number,
                    invite_user_id: '',
                    respondent_id: '',
                    hr_user_id: '',
                    time_admin_hearing: '',
                    irr_id: '',
                    status: '',
                    is_admin_hearing: '',
                    nte_upload: '',
                    attached_hr: [],
                    agreement: this.editProps.incident_report.agreement,
                    signed: '',
                    rtw: 0,
                    rtw_date: '',
                    rtw_advice_date: '',
                    sn_termination_date: '',
                    sn_date_absence_start: '',
                    sn_date_absence_end: '',
                    ir_id: this.editProps.incident_report.id,
                    respondent_email: '',
                    supervisor_email: '',
                    manager_email: '',
                    first_comment: '',
                    second_comment: '',
                    mom: '',
                    offense_id: '',
                    final_instance: '',
                    final_irr_single: '',
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
                    offense_multiple: [],
                    instance_multiple: [],
                    irr_multiple: [],
                    mom_attachment: [],
                    multiple_irr: [],
                    single_irr: '',
                    preventive_suspension_start: '',
                    preventive_suspension_end: '',
                    additional_notes: '',
                    stage_case: '',
                    suspension_date: '',
                    suspension_multiple: [],
                    nte_stage_case: '',
                },
                disabledSignatory: false,
                in_lieu: false,
                disabledIsAdminHearing: false,
                disabledDateHearing: false,
                disabledIsUnderInvestigation: false,
                disabledIRR: false,
                disabledInvite: false,
                disabledInviteType: false,
                disabledWitness: false,
                calendarButton: false,
                checker: false,
                finalInstances: false,
                finalCC: false,
                selectedUser: [],
                selectedIRR: [],
                selectedStageCase: [],
                selectedHrbp: [],
                selectedStatus: this.$constants.Status,
                selectedIsGenerateNte: this.$constants.Is_generate_nte,
                selectedTime: this.$constants.Time,
                selectedInstance: this.$constants.Instance_List,
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
                showTime: true,
                showMeetingPlace: true,
                editChanges: [],
                witness_count: 0,
                selectedDefaultEmpty: {
                    value:"",
                    text:""
                },
                disabledStatusIR: false,
                showDateHearing: false,
                showRtw: false,
                requiredAsterisk: false,
                showRtwDate: false,
                showRtwAdvisedDate: false,
                showSnTerminationDate: false,
                showSnAbsencesDate: false,
                show_cancelled: false,
                show_mom: false,
                show_termination: false,
                disabledStatusIR: false,
                disabledRemarks: false,
                disabledDates: {
                    to: new Date(Date.now() - 8640000)
                },
                showPSDate: false,
                downloadHrAttach: '',
                error_data: '',
                showAddSuspensionDate: false,
                indexCnt: 2,
                suspensionCnt: 0,
                showSuspensionDate: false,
                selectedNteStageOfTheCase: this.$constants.Nte_Stage_Case,
            };
        },
        watch: {
            incident: {
                handler: function(newVal) {
                    if (this.editProps.incident_report.witness_user.length > 0 && newVal.witness_user_id) {
                        for(let w=0; w<newVal.witness_user_id.length; w++){
                            if (newVal.witness_user_id.length > this.witness_count
                            && this.editChanges.indexOf(' Witness') === -1) {
                                this.editChanges.push(' Witness');
                            }
                        }
                    }

                    if(newVal.status && this.editProps.status_id != newVal.status.value
                    && this.editChanges.indexOf(' Status') === -1) {
                        this.editChanges.push(' Status');
                    }

                    if(newVal.remarks && this.editProps.incident_report.remarks != newVal.remarks
                    && this.editChanges.indexOf(' Remarks') === -1) {
                        this.editChanges.push(' Remarks');
                    }

                    if(newVal.date_admin_hearing && this.editChanges.indexOf(' Date of Admin Hearing') === -1
                    && this.editProps.incident_report.date_admin_hearing != newVal.date_admin_hearing) {
                        this.editChanges.push(' Date of Admin Hearing');
                    }

                    if(newVal.time_admin_hearing.value && this.editChanges.indexOf(' Time of Admin Hearing') === -1
                    && this.editProps.incident_report.time_admin_hearing != newVal.time_admin_hearing.value) {
                        this.editChanges.push(' Time of Admin Hearing');
                    }

                    if(newVal.meeting_place.value && this.editChanges.indexOf(' Meeting Place') === -1
                    && this.editProps.incident_report.meeting_place != newVal.meeting_place.value) {
                        this.editChanges.push(' Meeting Place');
                    }
                },
                deep: true,
            },
        },
        created(){
            this.incident.incident_report = this.editProps.incident_report;
            this.incident.respondent_id = this.editProps.respondent_id;
            this.incident.respondent_user_id = this.editProps.respondent_user_id;
            this.incident.ir_number = this.editProps.ir_number;
            this.incident.type_of_date = this.editProps.type_of_date;
            this.incident.start_date = this.editProps.start_date;
            this.incident.takes_date = this.editProps.takes_date;
            this.incident.last_date = this.editProps.last_date;
            this.incident.first_comment = this.editProps.first_comment;
            this.incident.second_comment = this.editProps.second_comment;
            this.incident.mom = this.editProps.mom;
            this.incident.mom_attachments = [];

            if (this.editProps.complainant_id) {
                this.incident.complainant_id = this.editProps.complainant_id;
                this.incident.complainant_user_id = this.editProps.complainant_user_id;
            }

            if(this.editProps.incident_report.invite_user) {
                this.incident.invite_user_id = this.editProps.incident_report.invite_user;
            }

            if (this.incident.is_under_investigation != 1) {
                this.requireRemarks = true;
            }

            if (this.editProps.is_generate_nte_invalid_ir == 1) {
                this.eventGenerateNTE(1);
                this.incident.is_generate_nte_invalid_ir = 1;

            } else if (this.editProps.is_generate_nte_invalid_ir == 2 ||
            this.editProps.is_generate_nte_invalid_ir == 3) {
                // this.incident.is_under_investigation = 0;
                this.disabledIsUnderInvestigation = true;
                this.disabledIRR = true;
                // this.incident.irr_id = "";
            } else if (this.editProps.is_generate_nte_invalid_ir == 4){
                this.editProps.is_admin_hearing = 1
                this.eventGenerateNTE(4);
            }

            if (this.editProps.status_id > 1) {
                for (let s = 0; s < this.selectedStatus.length; s++) {

                    if (this.editProps.status_id == this.selectedStatus[s].value) {
                        this.incident.status = {
                            text: this.selectedStatus[s].text,
                            value: this.selectedStatus[s].value,
                            selected: "selected",
                        }
                    }
                }
            }

            if (this.editProps.incident_report.hrbp_user) {
                this.incident.hr_user_id = {
                            text: this.editProps.incident_report.hrbp_user.first_name
                            + " " + this.editProps.incident_report.hrbp_user.last_name,
                            value: this.editProps.incident_report.hrbp_user.id,
                            selected: "selected",
                        }
            } else if(this.editProps.hrbp_user) {
                this.incident.hr_user_id = {
                            text: this.editProps.hrbp_user,
                            value: this.editProps.hrbp_user_id,
                            selected: "selected",
                        }
            }

            if (this.editProps.incident_report.witness_user.length > 0) {
                this.witness_count = this.editProps.incident_report.witness_user.length;

                for(let n=0; n<this.editProps.incident_report.witness_user.length; n++){
                    this.disabledWitness = false;

                    if (this.editProps.incident_report.witness_user[n].witness_fullname) {
                        this.incident.witness_user_id[n] = {
                            text: this.editProps.incident_report.witness_user[n].witness_fullname.first_name
                                + " " + this.editProps.incident_report.witness_user[n].witness_fullname.last_name,
                            value: this.editProps.incident_report.witness_user[n].witness_fullname.id,
                            selected: "selected"
                        };
                    }
                }
            }

            if (this.editProps.incident_report.invite_user.length > 0 ) {
                for(let w=0; w<this.editProps.incident_report.invite_user.length; w++){
                    this.disabledInvite = false;

                    if (this.editProps.incident_report.invite_user[w].invite_fullname) {
                        this.incident.invite_user_id[w] = {
                            text: this.editProps.incident_report.invite_user[w].invite_fullname.first_name
                                + " " + this.editProps.incident_report.invite_user[w].invite_fullname.last_name,
                            value: this.editProps.incident_report.invite_user[w].invite_fullname.id,
                            selected: "selected"
                        };
                    }
                }
            }

            if (this.editProps.incident_report && this.editProps.irr_id) {
                this.incident.irr_id = {
                    text: this.incident.incident_report.irr.name,
                    value: this.incident.incident_report.irr.id,
                    selected: "selected",
                }
            }

            if (this.editProps.incident_report.date_admin_hearing) {
                this.incident.is_admin_hearing = 1
                this.calendarButton = true
                this.eventDateHearing(1);

            } else if (this.editProps.incident_report.rtw == 1) {
                this.incident.is_admin_hearing = 2;
                this.calendarButton = true;
                this.incident.rtw = this.editProps.incident_report.rtw;
                this.incident.rtw_date = this.editProps.incident_report.rtw_date;
                this.incident.rtw_advice_date = this.editProps.incident_report.rtw_advice_date;
                this.incident.sn_termination_date = this.editProps.incident_report.sn_termination_date;
                this.incident.sn_date_absence_start = this.editProps.incident_report.sn_date_absence_start;
                this.incident.sn_date_absence_end = this.editProps.incident_report.sn_date_absence_end;
                this.eventDateHearing(2);
            } else {
                this.incident.is_admin_hearing = 0
                this.calendarButton = false
                this.requireIRR = true
                this.showTime = false;
                this.showMeetingPlace = false;
                this.disabledDateHearing = true;
                this.eventDateHearing(0);
            }

            if (this.incident.incident_report.time_admin_hearing) {
                this.incident.time_admin_hearing = {
                    text: this.incident.incident_report.time_admin_hearing,
                    value: this.incident.incident_report.time_admin_hearing,
                    selected: "selected",
                }
            }

            if (this.incident.incident_report.meeting_place) {
                this.incident.meeting_place = {
                    text: this.incident.incident_report.meeting_place,
                    value: this.incident.incident_report.meeting_place,
                    selected: "selected",
                }
            }

            if (this.editProps.attached_hr) {
                this.incident.attached_hr = this.editProps.attached_hr;
            }

            if (this.editProps.signed) {
                this.incident.signed = this.editProps.signed;
            }
            this.incident.agreement = this.editProps.incident_report.agreement;
            if(this.incident.agreement) {
                this.checker = true
            } else {
                this.checker = false
            }

            this.incident.respondent_email = this.editProps.respondent_email;

            if (this.editProps.supervisor_email) {
                this.incident.supervisor_email = this.editProps.supervisor_email;
            }

            if (this.editProps.manager_email) {
                this.incident.manager_email = this.editProps.manager_email;
            }

            if (this.editProps.offense_id) {
                this.incident.offense_id = this.editProps.offense_id
            }

            if (this.editProps.incident_report && this.editProps.is_generate_nte_invalid_ir == 4) {
                this.showPSDate = true
                this.incident.preventive_suspension_start = this.editProps.incident_report.preventive_suspension_start
                this.incident.preventive_suspension_end = this.editProps.incident_report.preventive_suspension_end
            }

            if (this.editProps.incident_report.ir_signatories) {
                if (this.editProps.prepared_by_empno) {
                    this.incident.prepared_by_first = this.editProps.prepared_by_first;
                    this.incident.prepared_by_last = this.editProps.prepared_by_last;
                    this.incident.prepared_by_designation = this.editProps.prepared_by_designation;
                    this.incident.prepared_by_empno = this.editProps.prepared_by_empno;
                }

                if (this.editProps.requested_by_empno) {
                    this.incident.requested_by_first = this.editProps.requested_by_first;
                    this.incident.requested_by_last = this.editProps.requested_by_last;
                    this.incident.requested_by_designation = this.editProps.requested_by_designation;
                    this.incident.requested_by_empno = this.editProps.requested_by_empno;
                }

                if (this.editProps.approved_by_empno) {
                    this.incident.approved_by_first = this.editProps.approved_by_first;
                    this.incident.approved_by_last = this.editProps.approved_by_last;
                    this.incident.approved_by_designation = this.editProps.approved_by_designation;
                    this.incident.approved_by_empno = this.editProps.approved_by_empno;
                }

                if (this.editProps.noted1_by_empno) {
                    this.incident.noted1_by_first = this.editProps.noted1_by_first;
                    this.incident.noted1_by_last = this.editProps.noted1_by_last;
                    this.incident.noted1_by_designation = this.editProps.noted1_by_designation;
                    this.incident.noted1_by_empno = this.editProps.noted1_by_empno;
                }

                if (this.editProps.noted2_by_empno) {
                    this.incident.noted2_by_first = this.editProps.noted2_by_first;
                    this.incident.noted2_by_last = this.editProps.noted2_by_last;
                    this.incident.noted2_by_designation = this.editProps.noted2_by_designation;
                    this.incident.noted2_by_empno = this.editProps.noted2_by_empno;
                }

                if (this.editProps.noted3_by_empno) {
                    this.incident.noted3_by_first = this.editProps.noted3_by_first;
                    this.incident.noted3_by_last = this.editProps.noted3_by_last;
                    this.incident.noted3_by_designation = this.editProps.noted3_by_designation;
                    this.incident.noted3_by_empno = this.editProps.noted3_by_empno;
                }

            }

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

            this.$constants.Admin_API.get("/hrbp/cluster/dropdown/hrbpById")
                .then(response => {
                this.selectedHrbp = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.$constants.Admin_API.get("/incidentreport/dropdown/office/location")
                .then(response => {
                this.selectedMeetingPlace = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
            for(let c=0; c < this.incident.offense_id.length; c++){
                this.findMultipleOffense(this.incident.offense_id[c]);
            }
            if (JSON.parse(this.editProps.incident_report.initial_irr_id).length > 0) {
                // this.incident.multiple_irr = JSON.parse(this.editProps.incident_report.initial_irr_id.replace(/\\|"/gi,''));
                this.incident.multiple_irr = this.editProps.incident_report.initial_irr_id.replace(/\\|"/gi,'');
            } else {
                this.incident.single_irr = this.editProps.incident_report.initial_irr_id;

            }

            this.selectedStageCase = this.$constants.Stage_case

            if (this.editProps.nte_stage_case) {
                this.incident.nte_stage_case = this.editProps.nte_stage_case;
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.incident.irr_id.value == 10) { //Generate Case Closure - Termination
                    return this.incident.hr_user_id && this.incident.status && this.requireIRR
                    && this.requireRemarks && this.incident.is_generate_nte_invalid_ir && this.finalCC
                    && this.finalInstances && this.incident.first_comment && this.incident.second_comment
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;

                } else if (this.incident.is_under_investigation == 1) { //Generate Case Closure and Final Instance
                    if (this.incident.is_under_investigation == 1 && this.showSuspensionDate == true  
                    && this.incident.suspension_date && this.incident.suspension_multiple.length > 0) {
                        for(let s=0; s<this.incident.suspension_multiple.length; s++){
                            return this.incident.suspension_multiple[s].suspension_date_multi && this.incident.hr_user_id 
                            && this.incident.status && this.requireIRR && this.finalCC
                            && this.finalInstances && this.requireRemarks && this.incident.is_generate_nte_invalid_ir
                            && this.incident.prepared_by_empno && this.incident.prepared_by_first
                            && this.incident.prepared_by_last && this.incident.prepared_by_designation
                            && this.incident.requested_by_empno && this.incident.requested_by_first
                            && this.incident.requested_by_last && this.incident.requested_by_designation
                            && this.incident.suspension_date;
                        }
                    } else if (this.incident.is_under_investigation == 1 && this.showSuspensionDate == true && this.incident.suspension_date) {
                        return this.incident.hr_user_id && this.incident.status && this.requireIRR && this.finalCC
                            && this.finalInstances && this.requireRemarks && this.incident.is_generate_nte_invalid_ir
                            && this.incident.prepared_by_empno && this.incident.prepared_by_first
                            && this.incident.prepared_by_last && this.incident.prepared_by_designation
                            && this.incident.requested_by_empno && this.incident.requested_by_first
                            && this.incident.requested_by_last && this.incident.requested_by_designation
                            && this.incident.suspension_date;

                    } else if (this.incident.is_under_investigation == 1 && this.showSuspensionDate == false) {
                        return this.incident.hr_user_id && this.incident.status && this.requireIRR && this.finalCC
                        && this.finalInstances && this.requireRemarks && this.incident.is_generate_nte_invalid_ir
                        && this.incident.prepared_by_empno && this.incident.prepared_by_first
                        && this.incident.prepared_by_last && this.incident.prepared_by_designation
                        && this.incident.requested_by_empno && this.incident.requested_by_first
                        && this.incident.requested_by_last && this.incident.requested_by_designation;
                    }
                } else if (this.incident.is_generate_nte_invalid_ir == 1 && this.incident.is_admin_hearing == 1) { //Generate NTE w/ Admin Hearing
                    return this.incident.hr_user_id && this.incident.status && this.requireRemarks
                    && this.incident.is_generate_nte_invalid_ir && this.incident.date_admin_hearing
                    && this.incident.time_admin_hearing && this.incident.meeting_place
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;

                } else if (this.incident.is_generate_nte_invalid_ir == 4 && this.incident.is_admin_hearing == 1) { //Generate NTE w/ PS
                    return this.incident.hr_user_id && this.incident.status
                    && this.incident.is_generate_nte_invalid_ir && this.incident.time_admin_hearing
                    && this.incident.meeting_place && this.incident.date_admin_hearing && this.requireRemarks
                    && this.incident.preventive_suspension_start && this.incident.preventive_suspension_end
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;


                } else if (this.incident.is_admin_hearing == 2 && this.incident.rtw == 1) { //Report to work order (RTW)
                    return this.incident.hr_user_id && this.incident.status
                    && this.requireRemarks && this.incident.is_generate_nte_invalid_ir
                    && this.incident.rtw_date && this.incident.rtw_advice_date && this.incident.sn_termination_date
                    && this.incident.sn_date_absence_start && this.incident.sn_date_absence_end
                    && this.incident.prepared_by_empno && this.incident.prepared_by_first
                    && this.incident.prepared_by_last && this.incident.prepared_by_designation
                    && this.incident.requested_by_empno && this.incident.requested_by_first
                    && this.incident.requested_by_last && this.incident.requested_by_designation;

                } else { //Generate NTE
                    return this.incident.hr_user_id && this.incident.status
                    && this.requireRemarks && this.incident.is_generate_nte_invalid_ir
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
                    getAxios = this.$constants.Admin_API.get("/incidentreport/signatory/employee/"+employee_no);

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
            attachFile(e){
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    this.incident.mom_attachment = [];
                    return false;
                }
                for (let value of selectedFiles) {
                    this.incident.mom_attachment.push(value);
                }
            },
            multipleFinalInstance(e) {
                for(let c=0; c < this.incident.offense_id.length; c++){
                    if (this.incident.offense_multiple[c].final_instance) {
                            this.finalInstances = true;
                    } else {
                            this.finalInstances = false;
                        break;
                    }
                }
            },
            multipleFinalCase(e) {
                for(let c=0; c < this.incident.offense_id.length; c++){
                    if  (this.incident.offense_multiple[c].final_irr) {
                            this.finalCC = true;
                    } else {
                        this.finalCC = false;
                        break;
                    }
                }
            },
            findMultipleOffense(offense_id) {
                this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                .then(response => {
                    this.incident.offense_multiple.push(response.data);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            changeType(agreement) {
                if(agreement) {
                    this.checker = true
                } else {
                    this.checker = false
                }
            },
            finalInstanceIR(remarks) {
                if ((remarks != ' ' || remarks != '' || remarks != null)
                    && this.incident.is_under_investigation == 1
                    && (this.incident.irr_id.value != undefined || this.incident.irr_id.value != ' ')) {
                    this.finalInstances = true;
                } else if ((remarks == ' ' || remarks == '' ||
                    remarks == null) && this.incident.is_under_investigation == 1
                    && (this.incident.irr_id.value != undefined || this.incident.irr_id.value == ' ')) {
                    this.finalInstances = false;
                }

                if (this.incident.final_instance) {
                    this.finalInstances = true;
                } else {
                    this.finalInstances = false;
                }
                console.log('THIS.finalInstances', this.finalInstances)

            },
            finalCaseClosure(remarks) {
                if ((remarks != ' ' || remarks != '' || remarks != null)
                    && this.incident.is_under_investigation == 1
                    && (this.incident.irr_id.value != undefined || this.incident.irr_id.value != ' ')) {
                    this.finalCC = true;
                } else if ((remarks == ' ' || remarks == '' ||
                    remarks == null) && this.incident.is_under_investigation == 1
                    && (this.incident.irr_id.value != undefined || this.incident.irr_id.value == ' ')) {
                    this.finalCC = false;
                }

                if (this.incident.final_irr_single) {
                    this.finalCC = true;
                } else {
                    this.finalCC = false;
                }
                console.log('THIS.FINALCC', this.finalCC)
            },
            addLinkForm(e) {
                if (e) {
                    let searchSuspension = e.text.indexOf("Suspension");
                    if (searchSuspension > 0) {
                        this.showSuspensionDate = true; 
                    } else {
                        this.showSuspensionDate = false;
                        this.incident.suspension_date = '';
                        this.incident.suspension_multiple = [];
                    }
                }

                if (e.text === 'Cancelled/Retracted') {
                    this.show_cancelled = true;
                    this.requireIRR = true;
                    this.show_termination = false;
                    this.show_mom = false;
                } else if (e.text === 'Termination of Employment') {
                    this.show_termination = true;
                    this.requireIRR = true;
                    this.show_cancelled = false;
                    this.show_mom = false;
                    let moms = (this.showDateHearing && this.showTime && this.showMeetingPlace) ? true : false;
                    if (moms == true) {
                        // TODO: ihihiwalay ng table
                        this.show_mom = true;
                    }
                } else if (e.text != undefined) {
                    this.show_cancelled = false;
                    this.show_termination = false;
                    this.requireIRR = true;
                    this.show_mom = false;
                    if (this.incident.remarks != ' ' && this.incident.is_under_investigation == 1
                        && (this.incident.irr_id.value != undefined || this.incident.irr_id.value != ' ')
                        && this.incident.remarks != '' && this.incident.remarks != null) {
                        this.requireRemarks = true;
                    } else if (this.incident.remarks == ' ' && this.incident.is_under_investigation == 1
                        && (this.incident.irr_id.value != undefined || this.incident.irr_id.value == ' ')
                        && this.incident.remarks == ''  && this.incident.remarks == null) {
                        this.requireRemarks = false;
                    }
                } else {
                    this.show_cancelled = false;
                    this.show_mom = false;
                    this.requireIRR = false;
                    this.show_termination = false;
                }
                // for in-lieu ToE
                if (this.incident.single_irr == 10 && (this.showDateHearing && this.showTime && this.showMeetingPlace)) {
                    this.in_lieu = true;
                } else if (this.incident.multiple_irr && (this.showDateHearing && this.showTime && this.showMeetingPlace)) {
                    for (var i = 0; i < this.incident.multiple_irr.length; i++) {
                        if (this.incident.multiple_irr[i] == 10) {
                            this.in_lieu = true;
                        }
                    }
                }
            },
            eventUpdate() {
                if (this.incident.witness_user_id.length > 0) {
                    for(let w=0; w<this.incident.witness_user_id.length; w++){
                        this.incident.witness_multiple.push(this.incident.witness_user_id[w].value);
                    }
                }

                if (this.incident.invite_user_id.length > 0) {
                    for(let w=0; w<this.incident.invite_user_id.length; w++){
                        this.incident.invite_multiple.push(this.incident.invite_user_id[w].value);
                    }
                }

                if (this.incident.offense_id.length > 0) {
                    for(let i=0; i<this.incident.offense_id.length; i++){
                        if (this.incident.offense_multiple[i].final_instance) {
                            this.incident.instance_multiple.push(this.incident.offense_multiple[i].final_instance.text);
                        } else {
                            this.incident.instance_multiple = 0;
                        }

                        if (this.incident.offense_multiple[i].final_irr) {
                            this.incident.irr_multiple.push(this.incident.offense_multiple[i].final_irr.value);
                        } else {
                            this.incident.irr_multiple = 0;
                        }
                    }
                }

                if (this.incident.type_of_date) {
                    this.incident.takes_date = null;
                    this.incident.last_date = null;
                }

                this.laddabtn.start();
                let incident = this.incident;
                let getAxios = '';
                if (this.incident.mom_attachment.length > 0) {
                    for(let i=0; i<this.incident.mom_attachment.length;i++){
                        this.form.append('pics[]', this.incident.mom_attachment[i]);
                    }
                    const config = { headers: { 'Content-Type': 'multipart/form-data' } }

                    this.$constants.Admin_API.post('/incidentreport/mom_attach/file', this.form, config)
                        .then(response => {
                            incident.mom_attachment = response.data.uploaded

                            this.$constants.Admin_API.get("/incidentreport/update/"+this.editProps.inc_id, {
                                params: {
                                    complainant_id: incident.complainant_id,
                                    remarks: incident.remarks,
                                    is_generate_nte_invalid_ir: incident.is_generate_nte_invalid_ir,
                                    preventive_suspension_start: incident.preventive_suspension_start,
                                    preventive_suspension_end: incident.preventive_suspension_end,
                                    date_admin_hearing: incident.date_admin_hearing,
                                    time_admin_hearing: incident.time_admin_hearing.value,
                                    meeting_place: incident.meeting_place.text,
                                    is_under_investigation: incident.is_under_investigation,
                                    irr_id: incident.irr_id.value,
                                    final_instance: incident.final_instance.text,
                                    final_irr_single: incident.final_irr_single.value,
                                    status: incident.status.value,
                                    witness_user_id: incident.witness_multiple,
                                    invite_user_id: incident.invite_multiple,
                                    type_invite: incident.type_invite,
                                    type_of_date: incident.type_of_date,
                                    start_date: incident.start_date,
                                    takes_date: incident.takes_date,
                                    last_date: incident.last_date,
                                    respondent_id: incident.respondent_id,
                                    hr_user_id: incident.hr_user_id.value,
                                    ir_number: incident.ir_number,
                                    complainant_user_id: incident.complainant_user_id,
                                    respondent_user_id: incident.respondent_user_id,
                                    editChanges: this.editChanges,
                                    agreement: incident.agreement,
                                    rtw: incident.rtw,
                                    rtw_date: incident.rtw_date,
                                    rtw_advice_date: incident.rtw_advice_date,
                                    sn_termination_date: incident.sn_termination_date,
                                    sn_date_absence_start: incident.sn_date_absence_start,
                                    sn_date_absence_end: incident.sn_date_absence_end,
                                    respondent_email: incident.respondent_email,
                                    supervisor_email: incident.supervisor_email,
                                    manager_email: incident.manager_email,
                                    first_comment: incident.first_comment,
                                    second_comment: incident.second_comment,
                                    mom: incident.mom,
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
                                    instance_multiple: incident.instance_multiple,
                                    irr_multiple: incident.irr_multiple,
                                    mom_attachment: incident.mom_attachment,
                                    additional_notes: incident.additional_notes,
                                    stage_case: incident.stage_case,
                                    suspension_date: incident.suspension_date,
                                    suspension_multiple: incident.suspension_multiple,
                                    nte_stage_case: incident.nte_stage_case,
                                }
                            }).then(response => {
                                this.incident = response.data;
                                this.$bus.$emit('init_modal', false);
                                this.laddabtn.stop();
                                this.$success('Successfully Update!');
                                this.$bus.$emit('updateList');
                                this.$bus.$emit('updateDashboard');
                            })
                            .catch(error => {
                                this.error_data = this.error_validation(error.response.data.message);

                                if (this.error_data) {
                                    this.laddabtn.stop();
                                } else {
                                    this.globalErrorHandling(error);
                                }
                            });
                        }).catch(error => {
                            this.globalErrorHandling(error);
                        });

                } else {
                    getAxios = this.$constants.Admin_API.get("/incidentreport/update/"+this.editProps.inc_id, {
                        params: {
                            complainant_id: incident.complainant_id,
                            remarks: incident.remarks,
                            is_generate_nte_invalid_ir: incident.is_generate_nte_invalid_ir,
                            preventive_suspension_start: incident.preventive_suspension_start,
                            preventive_suspension_end: incident.preventive_suspension_end,
                            date_admin_hearing: incident.date_admin_hearing,
                            time_admin_hearing: incident.time_admin_hearing.value,
                            meeting_place: incident.meeting_place.text,
                            is_under_investigation: incident.is_under_investigation,
                            irr_id: incident.irr_id.value,
                            final_instance: incident.final_instance.text,
                            final_irr_single: incident.final_irr_single.value,
                            status: incident.status.value,
                            witness_user_id: incident.witness_multiple,
                            invite_user_id: incident.invite_multiple,
                            type_invite: incident.type_invite,
                            type_of_date: incident.type_of_date,
                            start_date: incident.start_date,
                            takes_date: incident.takes_date,
                            last_date: incident.last_date,
                            respondent_id: incident.respondent_id,
                            hr_user_id: incident.hr_user_id.value,
                            ir_number: incident.ir_number,
                            complainant_user_id: incident.complainant_user_id,
                            respondent_user_id: incident.respondent_user_id,
                            editChanges: this.editChanges,
                            agreement: incident.agreement,
                            rtw: incident.rtw,
                            rtw_date: incident.rtw_date,
                            rtw_advice_date: incident.rtw_advice_date,
                            sn_termination_date: incident.sn_termination_date,
                            sn_date_absence_start: incident.sn_date_absence_start,
                            sn_date_absence_end: incident.sn_date_absence_end,
                            respondent_email: incident.respondent_email,
                            supervisor_email: incident.supervisor_email,
                            manager_email: incident.manager_email,
                            first_comment: incident.first_comment,
                            second_comment: incident.second_comment,
                            mom: incident.mom,
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
                            instance_multiple: incident.instance_multiple,
                            irr_multiple: incident.irr_multiple,
                            mom_attachment: incident.mom_attachment,
                            additional_notes: incident.additional_notes,
                            stage_case: incident.stage_case,
                            suspension_date: incident.suspension_date,
                            suspension_multiple: incident.suspension_multiple,
                            nte_stage_case: incident.nte_stage_case,
                        }
                    });

                    getAxios.then(response => {
                        this.incident = response.data;
                        this.$bus.$emit('init_modal', false);
                        this.laddabtn.stop();
                        this.$success('Successfully Update!');
                        this.$bus.$emit('updateList');
                        this.$bus.$emit('updateDashboard');
                    })
                    .catch(error => {
                        this.error_data = this.error_validation(error.response.data.message);

                        if (this.error_data) {
                            this.laddabtn.stop();
                        } else {
                            this.globalErrorHandling(error);
                        }
                    });
                }
            },
            eventDateHearing(checkedRadio) {
                if(checkedRadio == 1){
                    this.disabledDateHearing = false;
                    this.calendarButton = true;
                    this.showTime = true;
                    this.showMeetingPlace = true;

                    if (this.incident.single_irr == 10) {
                        this.in_lieu = true;
                        this.show_mom = true;
                    } else if (this.incident.multiple_irr) {
                        for (var i = 0; i < this.incident.multiple_irr.length; i++) {
                            if (this.incident.multiple_irr[i] == 10) {
                                this.in_lieu = true;
                                this.show_mom = true;
                            }
                        }
                    }
                    if(this.incident.time_admin_hearing = "") {
                        this.incident.time_admin_hearing = this.selectedDefaultEmpty;
                    }

                    if(this.incident.meeting_place = "") {
                        this.incident.meeting_place = this.selectedDefaultEmpty;
                    }
                    this.showDateHearing = true;
                    this.incident.rtw = 0;
                    this.showRtw = false;
                    this.showRtwDate = false;
                    this.showRtwAdvisedDate = false;
                    this.showSnTerminationDate = false;
                    this.showSnAbsencesDate = false;

                } else if(checkedRadio == 2){
                    this.disabledDateHearing = true;
                    this.calendarButton = true;
                    this.incident.date_admin_hearing = "";
                    this.showTime = false;
                    this.showMeetingPlace = false;
                    this.incident.time_admin_hearing = this.selectedDefaultEmpty;
                    this.incident.meeting_place = this.selectedDefaultEmpty;
                    this.showDateHearing = false;
                    this.incident.rtw = 1;
                    this.showRtw = true;
                    this.showRtwDate = true;
                    this.showRtwAdvisedDate = true;
                    this.showSnTerminationDate = true;
                    this.showSnAbsencesDate = true;
                    this.in_lieu = false;
                    this.show_mom = false;
                } else if(checkedRadio == 0){
                    this.disabledDateHearing = true;
                    this.calendarButton = false;
                    this.incident.date_admin_hearing = "";
                    this.showTime = false;
                    this.showMeetingPlace = false;
                    this.incident.time_admin_hearing = this.selectedDefaultEmpty;
                    this.incident.meeting_place = this.selectedDefaultEmpty;
                    this.showDateHearing = false;
                    this.incident.rtw = 0;
                    this.showRtw = false;
                    this.showRtwDate = false;
                    this.showRtwAdvisedDate = false;
                    this.showSnTerminationDate = false;
                    this.showSnAbsencesDate = false;
                    this.in_lieu = false;

                    this.show_mom = false;
                }
            },
            eventIRR(underInvestigation) {
                if(underInvestigation == 1){
                    this.incident.final_instance = "";
                    this.incident.final_irr_single = "";
                    this.requireIRR = false;
                    this.disabledIRR = false;
                    this.requiredAsterisk = true;
                    this.requireRemarks = false;
                    this.checkedRequiredRemarks = true;
                    if (this.incident.remarks != null && this.checkedRequiredRemarks && (this.incident.irr_id.value != undefined || this.incident.irr_id.value != ' ')) {
                        this.requireRemarks = true;
                    }

                    if (this.editProps.incident_report.irr &&
                    this.editProps.incident_report.irr.name == 'Cancelled/Retracted') {
                        this.show_cancelled = true;
                    }
                } else {
                    this.disabledIRR = true;
                    this.requireIRR = true;
                    this.incident.irr_id = "";
                    this.show_cancelled = false;
                    this.requiredAsterisk = false;
                    this.requireRemarks = true;
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
                    this.disabledIsUnderInvestigation = false
                    this.disabledIsAdminHearing = false
                    this.disabledWitness = false
                    this.disabledInvite = false
                    this.showPSDate = false
                    this.disabledPSDate = false

                } else if (generateNTE == 2 || generateNTE == 3) {
                    this.disabledDateHearing = true
                    this.disabledIsAdminHearing = true
                    this.disabledWitness = true
                    this.disabledInvite = true
                    this.disabledIsUnderInvestigation = true
                    this.disabledIRR = true
                    // this.incident.irr_id = this.selectedDefaultEmpty;
                    this.incident.is_admin_hearing = 0;
                    this.eventDateHearing(0);
                    // this.eventIRR(0);
                    this.incident.witness_user_id = "";
                    this.incident.invite_user_id = "";
                    this.incident.type_invite = "";
                    this.incident.is_under_investigation = 0;
                    this.incident.irr_id = "";
                    this.incident.final_instance = "";
                    this.incident.final_irr_single = "";
                    this.showPSDate = false
                    this.disabledPSDate = false

                } else if (generateNTE == 4) {
                    this.disabledIsUnderInvestigation = false
                    this.disabledIsAdminHearing = true
                    this.disabledWitness = false
                    this.disabledInvite = false
                    this.incident.is_admin_hearing = 1
                    this.incident.irr_id = "";
                    this.incident.final_instance = "";
                    this.incident.final_irr_single = "";
                    this.showPSDate = true
                    this.disabledPSDate = true
                    this.eventDateHearing(1);
                }
            },
            addedWitness(selectedAdded) {
                if(selectedAdded == 1) {
                    this.witness_count = 1;
                }
            },
            eventDownloadAttachment: function(fileAttach) {
                this.$swal({
                    title: 'Are you sure you want to Download Attachment file?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {

                            let attach = fileAttach.replace(/app|models|admin|settings|coc|\\|\[|\]|"|{|}/gi, '');

                            window.location.href = "/api/admin/incidentreport/download/attachment/"+attach;
                            this.$success('Attachment downloaded successfully!');
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Attachment has been cancelled');
                        }
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            requiringRemark(remark){
                if ((remark != '' || remark !== ' ' || remark != null || remark)
                    && (this.incident.irr_id.value != undefined || this.incident.irr_id.value != ' ')) {
                        this.requireRemarks = true;
                    if (this.incident.is_under_investigation === 1 && this.incident.irr_id) {
                        this.requireRemarks = true;
                    } else if (this.incident.is_under_investigation === 1 && !this.incident.irr_id) {
                        this.requireRemarks = false;
                    }
                } else if ((remark == '' || remark === ' ' || remark == null  || !remark)
                    && (this.incident.irr_id.value == undefined || this.incident.irr_id.value == ' ')) {
                    if (remark == '' || remark === ' ' ) {
                        this.incident.remarks = ''
                    }
                    this.requireRemarks = false;
                }
                if (!remark) {
                    this.requireRemarks = false;
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
                    this.incident.irr_id = '';
                    for(let i=0; i<this.incident.offense_multiple.length; i++){
                        this.incident.offense_multiple[i].final_instance = '';
                        this.incident.offense_multiple[i].final_irr = '';
                    }
                    this.incident.stage_case = '';
                    this.incident.is_under_investigation = '';
                    this.disabledIsUnderInvestigation = true;
                    this.disabledIRR = true;
                    this.finalCC = true;
                    this.finalInstance = true;
                    this.disabledSignatory = true;
                } else if (status_id.value == 2 || status_id.value == 3) { /* In Progress / On Hold */
                    this.disabledStatusIR = false;
                    this.disabledWitness = false;
                    this.disabledInvite = false;
                    this.disabledInviteType = false;
                    this.disabledIsAdminHearing = false;
                    this.incident.is_under_investigation = 0;
                    this.incident.is_generate_nte_invalid_ir = this.editProps.is_generate_nte_invalid_ir;
                    this.disabledIsUnderInvestigation = false;
                    this.disabledIRR = false;
                    this.disabledSignatory = false;
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
                this.downloadHrAttach = 'Download HR Attachments';
            },
            error_validation (error) {
                return String(error).replace(/"irr_id"|"final_instance"|"final_irr_single"|\:|\\|\[|\]|\"|{|}/gi, '');
            },
            eventShowAddSuspension () {
                this.showAddSuspensionDate = true;
            },
            addSuspension: function () {
                this.incident.suspension_multiple.push({
                    suspension_date_multi: ''
                });
            },
            deleteSuspension: function (index, suspension) {
                // this.incident.suspension_multiple.splice(this.incident.suspension_multiple.indexOf(suspension), 1);
                this.incident.suspension_multiple.splice(index, 1);
                if (index == 0) {
                    this.showAddSuspensionDate = true
                }
                if(index < 0)
                this.addSuspension()
            },
        }
    }
</script>
