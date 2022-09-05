<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <the-label label="Date of Incident" :asterisk="true" class="m-t-lg" 
                style="margin-bottom: 80px;">
                    <br>
                    <div class="col-lg-6">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <small>Start</small>
                            <datepicker
                                :input-class="'custom-datepicker'"
                                :format="'yyyy-MM-dd'"
                                :calendar-button="true"
                                :calendar-button-icon="'fa fa-calendar'"
                                :use-utc="true"
                                placeholder="YYYY-MM-DD"
                                v-model="coaching_log.date_start"
                                :disabled-dates="maxDate.disabledDates"
                            />

                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </div>

                    <div class="col-lg-6">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <small>End</small>
                            <datepicker
                                :input-class="'custom-datepicker'"
                                :format="'yyyy-MM-dd'"
                                :calendar-button="true"
                                :calendar-button-icon="'fa fa-calendar'"
                                :use-utc="true"
                                placeholder="YYYY-MM-DD"
                                v-model="coaching_log.date_end"
                                :disabled-dates="maxDate.disabledDates"
                                @input="dateRangeValidation(coaching_log.date_start, coaching_log.date_end)"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </div>
                </the-label>

                <the-label label="Number of Occurrences">
                    <input type='text' id="occurrences" class="form-control" 
                    v-model="coaching_log.number_occurrence" />
                </the-label>

                <hr v-if="showAddOffense">
                <the-label label="Classification of Coaching" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="coaching_log.category"
                            :options="selectedCategory"
                            label="text"
                            @input="eventOffense"
                            :disabled="categoryDisabled"
                        />
                        <span class="style-red" >{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Description" :asterisk="true" v-if="showOffense" >
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="coaching_log.offense"
                            :options="selectedOffense"
                            label="text"
                            :disabled="offenseDisabled"
                            @input="eventCoc"
                        />
                        <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                        <br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>
                <the-label label="Description:" v-if="showOthers" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="others" rows="4" cols="104.5" style="width:100%;" 
                        v-model="coaching_log.others" />
                        <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                        <br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>
                <the-label label="Description:" v-if="showPerformanceReview" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="others" rows="4" cols="104.5" style="width:100%;" 
                        v-model="coaching_log.performance_review" />
                        <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                        <br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label v-if="coaching_log.offense_description" label="Description:" 
                v-show="showDescription">
                    <div style="background: #eee; padding: 10px;" 
                    v-html="coaching_log.offense_description"/>
                </the-label>

                <the-label v-if="coaching_log.gravity" label="Gravity:" v-show="showGravity">
                    <div style="background: #eee; padding: 10px;" v-html="coaching_log.gravity"/>
                </the-label>

                <!-- start multiple -->
                <div v-for="(multiple_off, index) in provision_multiple">
                    <fieldset class="fieldsetCustom">
                    <legend class="legendCustom" >
                    Offense ( {{ offenseCnt == index ? 2 : indexCnt+index }} )
                    </legend>
                    <!-- multiple_off.category_multi == {{ multiple_off.category_multi.value }} -->
                    <the-label label="Classification of Coaching" v-show="showProvisionMultiple" 
                    :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <vSelect
                                v-model="multiple_off.category_multi"
                                :options="selectedCategory"
                                label="text"
                                @input="eventOffenseMultiple(index)"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="Description" v-show="showOffenseMultiple" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <vSelect
                                v-model="multiple_off.offense_multi"
                                :options="selectedOffense"
                                label="text"
                                :disabled="offenseMultipleDisabled"
                                @input="eventCocMultiple(index)"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label v-if="multiple_off.offense_description_multi" label="Description:" 
                    v-show="showDescriptionMultiple">
                        <div style="background: #eee; padding: 10px;" 
                        v-html="multiple_off.offense_description_multi"/>
                    </the-label>

                    <the-label v-if="multiple_off.gravity_multi" label="Gravity:" 
                    v-show="showGravityMultiple">
                        <div style="background: #eee; padding: 10px;" 
                        v-html="multiple_off.gravity_multi"/>
                    </the-label>

                    <div class="pull-left" v-if="showDeleteOffense">
                        <button type="button" @click="deleteOffense(index, multiple_off.offense_multi)" 
                        class="btn btn-danger">
                            <i class="fa fa-remove"/> Delete Offense
                            ( {{ offenseCnt == index ? 2 : indexCnt+index }} )
                        </button>
                    </div>
                    </fieldset>
                </div>

                <div class="addOffenseCustom" v-if="showAddOffense">
                    <div class="pull-right">
                        <button type="button" @click="addOffense" class="btn btn-info">
                            <i class="fa fa-plus"/> Add Another Offense
                        </button>
                    </div>
                </div>
                <hr v-if="showAddOffense">
                <!-- end multiple -->

                <the-label label="Findings / Coaching Opportunities / Areas of Improvement" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="findings" rows="4" cols="104.5" style="width:100%;" 
                        v-model="coaching_log.findings" />
                        <span class="style-red" >{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Point of Discussion/ Coaching Details" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="point_of_disscussion" rows="4" cols="104.5" 
                        style="width:100%;" v-model="coaching_log.point_of_disscussion" />
                        <span class="style-red" >{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Action Plans" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="action_plans" rows="4" cols="104.5" style="width:100%;" 
                        v-model="coaching_log.action_plans" />
                        <span class="style-red" >{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Employee’s Commitment" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="agent_commitment" rows="4" cols="104.5" style="width:100%;" 
                        v-model="coaching_log.agents_commitment" />
                        <span class="style-red" >{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Supervisor’s Commitment" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea rows="4" cols="104.5" style="width:100%;" 
                        v-model="coaching_log.supervisors_commitment" />
                        <span class="style-red" >{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Select Form Template" >
                    <vSelect
                        v-model="coaching_log.selectedForm"
                        :options="listForms"
                        label="text"
                    />
                </the-label>

                <the-label label="Image Attachment" :asterisk="false" class="image">
                    <div>
                        <label for="image" class="btn btn-success custom-single-file-button">
                            <i class="fa fa-upload m-r-xs" />
                            Upload
                        </label>
                        <input id="image"
                            type="file"
                            class="form-control"
                            @change="fieldChangeImage"
                        />
                    </div>
                </the-label>
                <!-- Start Of Added File -->

                <!-- Stage One -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 0" >
                    <legend>
                        Stage One:
                    </legend>
                    <!-- Loan Amount -->
                    <the-label label="Loan Amount Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Loan Amount Rating EOM" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_rating_eom"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Loan Amount Rating W2" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_rating_w2"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Rating W3" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_rating_w3"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Rating W4" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_rating_w4"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.loan_amount_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Knowledge -->
                    <the-label label="Knowledge Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.knowledge_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Knowledge Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.knowledge_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Knowledge Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.knowledge_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Knowledge Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.knowledge_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- QA Score -->
                    <the-label label="QA Score Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.qa_score_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Score Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.qa_score_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Score Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.qa_score_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Score Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.qa_score_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Coaching Log -->
                    <the-label label="Coaching Log Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.coaching_log_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Coaching Log Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.coaching_log_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Coaching Log Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.coaching_log_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Coaching Log Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.coaching_log_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.attendance_reliability_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.attendance_reliability_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_one.attendance_reliability_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Strengths" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="stage_one.strengths" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Weakness" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="stage_one.weakness" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="stage_one.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Stage Two to Five -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 1" >
                    <legend>
                        Stage Two To Five:
                    </legend>
                    <the-label label="Stage (Stage 2 / Stage 3 / Stage 4 / Stage 5)" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.stage"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Loan Amount -->
                    <the-label label="Loan Amount Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.loan_amount_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.loan_amount_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.loan_amount_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Loan Amount Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.loan_amount_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Knowledge -->
                    <the-label label="QA Compliance Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_compliance_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Compliance Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_compliance_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Compliance Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_compliance_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Compliance Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_compliance_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- QA Score -->
                    <the-label label="QA Score Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_score_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Score Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_score_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Score Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_score_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="QA Score Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.qa_score_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Coaching Log -->
                    <the-label label="Correction Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.correction_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Correction Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.correction_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Correction Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.correction_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Correction Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.correction_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.attendance_reliability_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Computation" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.attendance_reliability_computation"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="stage_two_to_five.attendance_reliability_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Strengths" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="stage_two_to_five.strengths" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Weakness" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="stage_two_to_five.weakness" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="stage_two_to_five.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Admin -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 2" >
                    <legend>
                        Admin Form:
                    </legend>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.attendance_reliability_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Weight of KPI" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.attendance_reliability_weight_of_kpi"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.attendance_reliability_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.attendance_reliability_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Punctuality -->
                    <the-label label="Punctuality Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.punctuality_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Punctuality Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.punctuality_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Punctuality Weight of KPI" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.punctuality_weight_of_kpi"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Punctuality Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.punctuality_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Punctuality Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.punctuality_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Process Knowledge -->
                    <the-label label="Process Knowledge Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.process_knowledge_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Process Knowledge Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.process_knowledge_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Process Knowledge Weight of KPI" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.process_knowledge_weight_of_kpi"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Process Knowledge Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.process_knowledge_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Process Knowledge Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.process_knowledge_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Work Ethics Number of Escalation -->
                    <the-label label="Work Ethics Number of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_number_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_number_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Weight of KPI" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_number_weight_of_kpi"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_number_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_number_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Work Ethics No NTE -->
                    <the-label label="Work Ethics No NTE Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_no_nte_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_no_nte_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Weight of KPI" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_no_nte_weight_of_kpi"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_no_nte_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.work_ethics_no_nte_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Total -->
                    <the-label label="Total" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="admin.total"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Development Plan -->
                    <the-label label="Development Plan" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="admin.development_plan" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!--  -->
                    <the-label label="Weakness" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="admin.weakness" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <the-label label="Strengths" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea id="strengths" rows="4" cols="104.5" style="width:100%;" 
                            v-model="admin.strengths" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="IMMEDIATE MANAGER'S FEEDBACK" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="admin.managers_feedback" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="EMPLOYEE'S ACKNOWLEDGEMENT" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="admin.employees_acknowledge" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>
                <!-- Angel -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 3" >
                    <legend>
                        HRIS Form:
                    </legend>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Timely And Accuraccy of On-Boarding -->
                    <the-label label="Timely And Accuraccy of On-Boarding Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_on_boarding_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely And Accuraccy of On-Boarding Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_on_boarding_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely And Accuraccy of On-Boarding Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_on_boarding_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely And Accuraccy of On-Boarding Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_on_boarding_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>


                    <!-- Timely And Accuraccy of On-Boarding -->
                    <the-label label="Timely And Accuraccy of Off-Boarding Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_off_boarding_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely And Accuraccy of Off-Boarding Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_off_boarding_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely And Accuraccy of Off-Boarding Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_off_boarding_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely And Accuraccy of Off-Boarding Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_off_boarding_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Timely and Accuracy of Updating in Masterfile -->
                    <the-label label="Timely and Accuracy of Updating in Masterfile Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_updating_in_masterfile_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely and Accuracy of Updating in Masterfile Weight" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_updating_in_masterfile_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely and Accuracy of Updating in Masterfile Rating Scale" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_updating_in_masterfile_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely and Accuracy of Updating in Masterfile Final Score" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.timely_and_accuraccy_of_updating_in_masterfile_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Submission of weekly reports -->
                    <the-label label="Submission of weekly reports Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.submission_of_weekly_reports_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of weekly reports Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.submission_of_weekly_reports_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of weekly reports Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.submission_of_weekly_reports_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of weekly reports Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.submission_of_weekly_reports_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Final Pay Concerns -->
                    <the-label label="Final Pay Concerns Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.final_pay_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Final Pay Concerns Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.final_pay_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Final Pay Concerns Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.final_pay_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Final Pay Concerns Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.final_pay_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Number of Escalations -->
                    <the-label label="Number of Escalations Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.number_of_escalation_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number of Escalations Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.number_of_escalation_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number of Escalations Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.number_of_escalation_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number of Escalations Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.number_of_escalation_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="hris.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="hris.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="hris.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- C&B -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 4" >
                    <legend>
                        C&B Form:
                    </legend>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Payroll File -->
                    <the-label label="Payroll File Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.payroll_file_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Payroll File Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.payroll_file_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Payroll File Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.payroll_file_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Payroll File Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.payroll_file_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Timekeeping -->
                    <the-label label="Timekeeping Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.timekeeping_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timekeeping Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.timekeeping_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timekeeping Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.timekeeping_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timekeeping Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.timekeeping_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Processing of Benefits reports -->
                    <the-label label="Processing of Benefits reports Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.processing_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Processing of Benefits reports Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.processing_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Processing of Benefits reports Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.processing_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Processing of Benefits reports Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.processing_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- HMO Enrollment -->
                    <the-label label="HMO Enrollment Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.hmo_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HMO Enrollment Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.hmo_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HMO Enrollment Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.hmo_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HMO Enrollment Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.hmo_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Inssuance of Employee -->
                    <the-label label="Inssuance of Employee Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.inssuance_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Inssuance of Employee Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.inssuance_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Inssuance of Employee Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.inssuance_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Inssuance of Employee Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.inssuance_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Final Pay Processing -->
                    <the-label label="Final Pay Processing Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.final_pay_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Final Pay Processing Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.final_pay_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Final Pay Processing Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.final_pay_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Final Pay Processing Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.final_pay_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="cnb.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="cnb.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="cnb.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Final Pay -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 5" >
                    <legend>
                        Final Pay Form:
                    </legend>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Accuraccy of Computation of Final Pay -->
                    <the-label label="Accuraccy of Computation of Final Pay Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.accuracy_computation_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Accuraccy of Computation of Final Pay Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.accuracy_computation_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Accuraccy of Computation of Final Pay Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.accuracy_computation_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Accuraccy of Computation of Final Pay Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.accuracy_computation_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Timely Processing of Final Pay (bi-weekly) Submission -->
                    <the-label label="Timely Processing of Final Pay (bi-weekly) Submission Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.timely_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely Processing of Final Pay (bi-weekly) Submission Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.timely_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely Processing of Final Pay (bi-weekly) Submission Rating Scale" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.timely_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Timely Processing of Final Pay (bi-weekly) Submission Final Score" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.timely_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Submission of Final Pay Report (bi-weekly): -->
                    <the-label label="Submission of Final Pay Report (bi-weekly):" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.submission_of_final_rating_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of Final Pay Report (bi-weekly) Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.submission_of_final_rating_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of Final Pay Report (bi-weekly) Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.submission_of_final_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of Final Pay Report (bi-weekly) Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.submission_of_final_rating_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Number of Escalations -->
                    <the-label label="Number of Escalations Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.number_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number of Escalations Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.number_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number of Escalations Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.number_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number of Escalations Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.number_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="final_pay.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;"
                             v-model="final_pay.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="final_pay.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Manager Rating HRBP Site Lead -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 6" >
                    <legend>
                        Manager Rating HRBP Site Lead Form:
                    </legend>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Leadership -->
                    <the-label label="Leadership Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.leadership_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Leadership Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.leadership_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Leadership Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.leadership_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Leadership Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.leadership_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Monthly Data Analysis -->
                    <the-label label="Monthly Data Analysis Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_data_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Data Analysis Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_data_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Data Analysis Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_data_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Data Analysis Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_data_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Monthly Site Satisfaction -->
                    <the-label label="Monthly Site Satisfaction Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_site_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Site Satisfaction Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_site_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Site Satisfaction Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_site_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Site Satisfaction Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.monthly_site_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Productivity -->
                    <the-label label="Productivity Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.productivity_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.productivity_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.productivity_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.productivity_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="manager_rating_sl.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="manager_rating_sl.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="manager_rating_sl.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Onboarding -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 7" >
                    <legend>
                        Onboarding Form:
                    </legend>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Completion of Requirements -->
                    <the-label label="Completion of Requirements Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.completion_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Completion of Requirements Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.completion_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Completion of Requirements Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.completion_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Completion of Requirements Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.completion_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Submission of ID Creation -->
                    <the-label label="Submission of ID Creation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of ID Creation Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of ID Creation Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission of ID Creation Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Bank Enrollment -->
                    <the-label label="Bank Enrollment Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.bank_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Bank Enrollment Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.bank_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Bank Enrollment Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.bank_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Bank Enrollment Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.bank_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- HMO Enrollment -->
                    <the-label label="HMO Enrollment Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.hmo_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HMO Enrollment Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.hmo_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HMO Enrollment Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.hmo_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HMO Enrollment Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.hmo_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Number Of Escalation -->
                    <the-label label="Number Of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.escalation_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number Of Escalation Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.escalation_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number Of Escalation Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.escalation_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number Of Escalation Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.escalation_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Inssuance of Employee -->
                    <the-label label="Inssuance of Employee Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.inssuance_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Inssuance of Employee Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.inssuance_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Inssuance of Employee Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.inssuance_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Inssuance of Employee Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.inssuance_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Submission Of by-weekly Reports Processing -->
                    <the-label label="Submission Of by-weekly Reports Processing Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_by_weekly_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission Of by-weekly Reports Processing Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_by_weekly_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission Of by-weekly Reports Processing Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_by_weekly_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Submission Of by-weekly Reports Processing Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.submission_by_weekly_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="onboard.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="onboard.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="onboard.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Payroll -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 8" >
                    <legend>
                        Payroll Form:
                    </legend>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Payroll Reports -->
                    <the-label label="Payroll Reports Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.payroll_reports_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Payroll Reports Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.payroll_reports_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Payroll Reports Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.payroll_reports_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Payroll Reports Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.payroll_reports_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Government Remittance -->
                    <the-label label="Government Remittance Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.government_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Government Remittance Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.government_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Government Remittance Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.government_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Government Remittance Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.government_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Bank Enrollment -->
                    <the-label label="Bank Enrollment Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.bank_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Bank Enrollment Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.bank_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Bank Enrollment Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.bank_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Bank Enrollment Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.bank_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Number Of Escalation -->
                    <the-label label="Number Of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.escalation_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number Of Escalation Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.escalation_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number Of Escalation Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.escalation_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Number Of Escalation Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.escalation_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="payroll.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="payroll.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="payroll.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Recruitment -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 9" >
                    <legend>
                        Recruitment Form:
                    </legend>
                    <!-- Recruitment Productivity Number of Hires -->
                    <the-label label="Recruitment Productivity Number of Hires Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.number_of_hires_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Number of Hires Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.number_of_hires_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Number of Hires Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.number_of_hires_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Number of Hires Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.number_of_hires_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Number of Hires Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.number_of_hires_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Recruitment Productivity Time to Fill -->
                    <the-label label="Recruitment Productivity Time to Fill Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.time_to_fill_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Time to Fill Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.time_to_fill_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Time to Fill Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.time_to_fill_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Time to Fill Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.time_to_fill_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Productivity Time to Fill Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.time_to_fill_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics Number of Escalation -->
                    <the-label label="Work Ethics Number of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_number_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_number_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_number_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_number_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_number_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics No NTE -->
                    <the-label label="Work Ethics No NTE Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_nte_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_nte_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_nte_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_nte_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.work_ethics_nte_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Recruitment Satisfaction Survey -->
                    <the-label label="Recruitment Satisfaction Survey Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.recruitment_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Satisfaction Survey Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.recruitment_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Satisfaction Survey Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.recruitment_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Satisfaction Survey Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.recruitment_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Satisfaction Survey Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.recruitment_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="recruitment.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Development Plan -->
                    <the-label label="Development Plan" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="recruitment.development_plan" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!--  -->
                    <the-label label="Strengths" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea id="strengths" rows="4" cols="104.5" style="width:100%;" 
                            v-model="recruitment.strengths" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="IMMEDIATE MANAGER'S FEEDBACK" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="recruitment.managers_feedback" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="EMPLOYEE'S ACKNOWLEDGEMENT" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="recruitment.employees_acknowledge" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Self Rating HRBP -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 10" >
                    <legend>
                        Self Rating HRBP Form:
                    </legend>
                    <!-- Critical Thinking -->
                    <the-label label="Critical Thinking Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.critical_thinking_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Critical Thinking Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.critical_thinking_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Critical Thinking Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.critical_thinking_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Critical Thinking Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.critical_thinking_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Data Analysis -->
                    <the-label label="Data Analysis Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.data_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Data Analysis Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.data_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Data Analysis Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.data_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Data Analysis Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.data_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- HR Intervention -->
                    <the-label label="HR Intervention Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.intervention_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HR Intervention Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.intervention_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HR Intervention Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.intervention_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HR Intervention Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.intervention_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Cluster Satisfaction -->
                    <the-label label="Cluster Satisfaction Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.cluster_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Cluster Satisfaction Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.cluster_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Cluster Satisfaction Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.cluster_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Cluster Satisfaction Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.cluster_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Productivity -->
                    <the-label label="Productivity Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.productivity_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.productivity_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.productivity_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.productivity_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="self_rating_hrbp.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="self_rating_hrbp.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>
                <!-- Self Rating HRBP Site Lead -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 11" >
                    <legend>
                        Self Rating HRBP Site Lead Form:
                    </legend>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Leadership -->
                    <the-label label="Leadership Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.leadership_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Leadership Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.leadership_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Leadership Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.leadership_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Leadership Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.leadership_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.attendance_reliability_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.attendance_reliability_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.attendance_reliability_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.attendance_reliability_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Monthly Data Analysis -->
                    <the-label label="Monthly Data Analysis Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.analysis_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Data Analysis Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.analysis_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Data Analysis Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.analysis_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Data Analysis Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.analysis_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Monthly Site Satisfaction Ration -->
                    <the-label label="Monthly Site Satisfaction Ration Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.satisfaction_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Site Satisfaction Ration Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.satisfaction_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Site Satisfaction Ration Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.satisfaction_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Monthly Site Satisfaction Ration Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.satisfaction_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Productivity -->
                    <the-label label="Productivity Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.productivity_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.productivity_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.productivity_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.productivity_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="self_rating_hrbp_site_lead.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="self_rating_hrbp_site_lead.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="self_rating_hrbp_site_lead.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>
                <!-- Sourcing -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 12" >
                    <legend>
                        Sourcing Form:
                    </legend>
                    <!-- Sourcing Productivity Number of ShowUp -->
                    <the-label label="Sourcing Productivity Number of ShowUp Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.show_up_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Number of ShowUp Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.show_up_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Number of ShowUp Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.show_up_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Number of ShowUp Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.show_up_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Number of ShowUp Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.show_up_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Sourcing Productivity Time to Fill -->
                    <the-label label="Sourcing Productivity Time to Fill Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.time_to_fill_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Time to Fill Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.time_to_fill_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Time to Fill Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.time_to_fill_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Time to Fill Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.time_to_fill_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Productivity Time to Fill Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.time_to_fill_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics Number of Escalation -->
                    <the-label label="Work Ethics Number of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.escalation_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.escalation_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.escalation_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.escalation_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.escalation_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics No NTE -->
                    <the-label label="Work Ethics No NTE Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.no_nte_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.no_nte_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.no_nte_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.no_nte_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics No NTE Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.no_nte_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Sourcing Initiative -->
                    <the-label label="Sourcing Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.initiative_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Sourcing Initiative Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.initiative_comment"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="sourcing.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Develop Plan -->
                    <the-label label="Develop Plan" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="sourcing.development_plan" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Weaknesses -->
                    <the-label label="Weaknesses" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="sourcing.weaknesses" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Strengths -->
                    <the-label label="Strengths" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="sourcing.strengths" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="IMMEDIATE MANAGER'S FEEDBACK" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="sourcing.managers_feedback" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="EMPLOYEE'S ACKNOWLEDGEMENT" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="sourcing.employees_acknowledge" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>

                <!-- Recruitment Supervisor -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 13" >
                    <legend>
                        Recruitment Supervisor Form:
                    </legend>
                    <!-- Recruitment Team's Productivity Support Hiring -->
                    <the-label label="Recruitment Team's Productivity Support Hiring Target" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.support_hiring_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Support Hiring Actual Rating" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.support_hiring_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Support Hiring Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.support_hiring_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Support Hiring Rating Scale" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.support_hiring_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Support Hiring Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.support_hiring_comments"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Recruitment Team's Productivity Agent Hiring to Fill -->
                    <the-label label="Recruitment Team's Productivity Agent Hiring to Fill Target" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.agent_hiring_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Agent Hiring to Fill Actual Rating" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.agent_hiring_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Agent Hiring to Fill Weight" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.agent_hiring_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Agent Hiring to Fill Rating Scale" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.agent_hiring_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Productivity Agent Hiring to Fill Comment" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.agent_hiring_comments"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- People Development -->
                    <the-label label="People Development Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.development_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="People Development Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.development_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="People Development Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.development_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="People Development Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.development_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="People Development Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.development_comments"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics Number of Escalation -->
                    <the-label label="Work Ethics Number of Escalation Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.escalation_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.escalation_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.escalation_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.escalation_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Number of Escalation Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.escalation_comments"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics infractions in the code of conduct -->
                    <the-label label="Work Ethics infractions in the code of conduct Target" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.infraction_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics infractions in the code of conduct Actual Rating" 
                    :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.infraction_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics infractions in the code of conduct Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.infraction_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics infractions in the code of conduct Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.infraction_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics infractions in the code of conduct Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.infraction_comments"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Recruitment Team's Satisfaction Survey -->
                    <the-label label="Recruitment Team's Satisfaction Survey Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.recruitment_teams_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Satisfaction Survey Actual Rating" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.recruitment_teams_actual_rating"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Satisfaction Survey Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.recruitment_teams_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Satisfaction Survey Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.recruitment_teams_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Recruitment Team's Satisfaction Survey Comment" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.recruitment_teams_comments"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_recruitment.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Develop Plan -->
                    <the-label label="Develop Plan" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="supervisor_recruitment.development_plan" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Weaknesses -->
                    <the-label label="Weaknesses" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="supervisor_recruitment.weaknesses" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Strengths -->
                    <the-label label="Strengths" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="supervisor_recruitment.strengths" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="IMMEDIATE MANAGER'S FEEDBACK" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="supervisor_recruitment.managers_feedback" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label label="EMPLOYEE'S ACKNOWLEDGEMENT" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" 
                            v-model="supervisor_recruitment.employees_acknowledge" />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>
                <!-- Supervisor Rating HRBP -->
                <div v-if="coaching_log.selectedForm && coaching_log.selectedForm.value === 14" >
                    <legend>
                        Supervisor Rating HRBP Form:
                    </legend>
                    <!-- Self Thinking -->
                    <the-label label="Self Thinking Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.self_thinking_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Self Thinking Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.self_thinking_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Self Thinking Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.self_thinking_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Self Thinking Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.self_thinking_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Initiative -->
                    <the-label label="Initiative Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.initiative_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.initiative_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.initiative_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Initiative Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.initiative_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Work Ethics -->
                    <the-label label="Work Ethics Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.work_ethics_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.work_ethics_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.work_ethics_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Work Ethics Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.work_ethics_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Responsiveness -->
                    <the-label label="Responsiveness Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.responsiveness_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.responsiveness_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span>
                            <br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.responsiveness_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Responsiveness Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.responsiveness_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Attendance Reliability -->
                    <the-label label="Attendance Reliability Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.attendance_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.attendance_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.attendance_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Attendance Reliability Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.attendance_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Data Analysis Survey -->
                    <the-label label="Data Analysis Survey Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.data_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Data Analysis Survey Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.data_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Data Analysis Survey Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.data_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Data Analysis Survey Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.data_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- HR Intervention -->
                    <the-label label="HR Intervention Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.intervention_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HR Intervention Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.intervention_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HR Intervention Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.intervention_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="HR Intervention Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.intervention_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Cluster Satisfaction -->
                    <the-label label="Cluster Satisfaction Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.cluster_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Cluster Satisfaction Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.cluster_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Cluster Satisfaction Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.cluster_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Cluster Satisfaction Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.cluster_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <!-- Productivity -->
                    <the-label label="Productivity Target" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.productivity_target"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Weight" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.productivity_weight"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Rating Scale" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.productivity_rating_scale"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>
                    <the-label label="Productivity Final Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.productivity_final_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
        	        </the-label>

                    <!-- Total Score -->
                    <the-label label="Total Score" :asterisk="true" >
                        <validation-provider rules="required" v-slot="{ errors }">
                            <input
                                type='text'
                                class="form-control"
                                v-model="supervisor_hrbp.total_score"
                            />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Commendation -->
                    <the-label label="Commendation" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="supervisor_hrbp.commendation" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                    <!-- Action Plans -->
                    <the-label label="Action Plans" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <textarea rows="4" cols="104.5" style="width:100%;" v-model="supervisor_hrbp.action_plans" />
                            <span class="style-red" >{{ errors[0] }}</span><br v-if="errors[0]"/>
                            <br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>
                </div>


                <button-save class="ladda-button btn btn-primary"/>
            </div>
        </the-form>
    </ValidationObserver>
</template>
<script>
    export default {
        props: {
            employee_no: String,
        },

        data: function () {
            return {
    			coaching_log: {
                    employee_no: '',
                    selectedForm: null,
                    findings: '',
                    point_of_disscussion: '',
                    action_plans: '',
                    agents_commitment: '',
                    supervisors_commitment: '',
                    category: '',
                    offense: '',
                    gravity: '',
                    others: '',
                    performance_review: '',
                    offense_description: '',
                    offense_multiple: [],
                    infraction: '',
                    attendance_points: '',
                    definition: '',
                    attachments: [],
                    attachment_image: [],
                    selectedForm: null,
                },
                stage_one: {
                    loan_amount_target: '',
                    loan_amount_rating_eom: '',
                    loan_amount_rating_w2: '',
                    loan_amount_rating_w3: '',
                    loan_amount_rating_w4: '',
                    loan_amount_computation: '',
                    loan_amount_comment: '',
                    knowledge_target: '',
                    knowledge_rating: '',
                    knowledge_computation: '',
                    knowledge_comment: '',
                    qa_score_target: '',
                    qa_score_rating: '',
                    qa_score_computation: '',
                    qa_score_comment: '',
                    coaching_log_target: '',
                    coaching_log_rating: '',
                    coaching_log_computation: '',
                    coaching_log_comment: '',
                    attendance_reliability_target: '',
                    attendance_reliability_rating: '',
                    attendance_reliability_computation: '',
                    attendance_reliability_comment: '',
                    strengths: '',
                    weakness: '',
                    action_plans: '',
                },
                stage_two_to_five: {
                    stage: '',
                    loan_amount_target: '',
                    loan_amount_rating: '',
                    loan_amount_computation: '',
                    loan_amount_comment: '',
                    qa_compliance_target: '',
                    qa_compliance_rating: '',
                    qa_compliance_computation: '',
                    qa_compliance_comment: '',
                    qa_score_target: '',
                    qa_score_rating: '',
                    qa_score_computation: '',
                    qa_score_comment: '',
                    correction_target: '',
                    correction_rating: '',
                    correction_computation: '',
                    correction_comment: '',
                    attendance_reliability_target: '',
                    attendance_reliability_rating: '',
                    attendance_reliability_computation: '',
                    attendance_reliability_comment: '',
                    strengths: '',
                    weakness: '',
                    action_plans: '',
                },
                admin: {
                    attendance_reliability_target: '',
                    attendance_reliability_actual_rating: '',
                    attendance_reliability_weight_of_kpi: '',
                    attendance_reliability_rating: '',
                    attendance_reliability_comment: '',
                    punctuality_target: '',
                    punctuality_actual_rating: '',
                    punctuality_weight_of_kpi: '',
                    punctuality_rating: '',
                    punctuality_comment: '',
                    process_knowledge_target: '',
                    process_knowledge_rating: '',
                    process_knowledge_weight_of_kpi: '',
                    process_knowledge_rating: '',
                    process_knowledge_comment: '',
                    work_ethics_number_target: '',
                    work_ethics_number_actual_rating: '',
                    work_ethics_number_weight_of_kpi: '',
                    work_ethics_number_rating: '',
                    work_ethics_number_comment: '',
                    work_ethics_no_nte_target: '',
                    work_ethics_no_nte_actual_rating: '',
                    work_ethics_no_nte_weight_of_kpi: '',
                    work_ethics_no_nte_rating: '',
                    work_ethics_no_nte_comment: '',
                    total: '',
                    development_plan: '',
                    weakness: '',
                    strengths: '',
                    managers_feedback: '',
                    employees_acknowledge: '',
                },
                hris: {
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    timely_and_accuraccy_of_on_boarding_target: '',
                    timely_and_accuraccy_of_on_boarding_weight: '',
                    timely_and_accuraccy_of_on_boarding_rating_scale: '',
                    timely_and_accuraccy_of_on_boarding_final_score: '',

                    timely_and_accuraccy_of_off_boarding_target: '',
                    timely_and_accuraccy_of_off_boarding_weight: '',
                    timely_and_accuraccy_of_off_boarding_rating_scale: '',
                    timely_and_accuraccy_of_off_boarding_final_score: '',

                    timely_and_accuraccy_of_updating_in_masterfile_target: '',
                    timely_and_accuraccy_of_updating_in_masterfile_weight: '',
                    timely_and_accuraccy_of_updating_in_masterfile_rating_scale: '',
                    timely_and_accuraccy_of_updating_in_masterfile_final_score: '',
                    submission_of_weekly_reports_target: '',
                    submission_of_weekly_reports_weight: '',
                    submission_of_weekly_reports_rating_scale: '',
                    submission_of_weekly_reports_final_score: '',
                    final_pay_target: '',
                    final_pay_weight: '',
                    final_pay_rating_scale: '',
                    final_pay_final_score: '',
                    number_of_escalation_target: '',
                    number_of_escalation_weight: '',
                    number_of_escalation_rating_scale: '',
                    number_of_escalation_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                cnb: {
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    payroll_file_target: '',
                    payroll_file_weight: '',
                    payroll_file_rating_scale: '',
                    payroll_file_final_score: '',
                    timekeeping_target: '',
                    timekeeping_weight: '',
                    timekeeping_rating_scale: '',
                    timekeeping_final_score: '',
                    processing_target: '',
                    processing_weight: '',
                    processing_rating_scale: '',
                    processing_final_score: '',
                    hmo_target: '',
                    hmo_weight: '',
                    hmo_rating_scale: '',
                    hmo_final_score: '',
                    inssuance_target: '',
                    inssuance_weight: '',
                    inssuance_rating_scale: '',
                    inssuance_final_score: '',
                    final_pay_target: '',
                    final_pay_weight: '',
                    final_pay_rating_scale: '',
                    final_pay_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                final_pay: {
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    accuracy_computation_target: '',
                    accuracy_computation_weight: '',
                    accuracy_computation_rating_scale: '',
                    accuracy_computation_final_score: '',
                    timely_target: '',
                    timely_weight: '',
                    timely_rating_scale: '',
                    timely_final_score: '',
                    submission_of_final_rating_target:'',
                    submission_of_final_rating_weight:'',
                    submission_of_final_rating_scale:'',
                    submission_of_final_rating_final_score:'',
                    number_target: '',
                    number_weight: '',
                    number_rating_scale: '',
                    number_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                manager_rating_sl: {
                    work_ethics_target:'',
                    work_ethics_weight:'',
                    work_ethics_rating_scale:'',
                    work_ethics_final_score:'',
                    leadership_target:'',
                    leadership_weight:'',
                    leadership_rating_scale:'',
                    leadership_final_score:'',
                    attendance_reliability_target:'',
                    attendance_reliability_weight:'',
                    attendance_reliability_rating_scale:'',
                    attendance_reliability_final_score:'',
                    monthly_data_target:'',
                    monthly_data_weight:'',
                    monthly_data_rating_scale:'',
                    monthly_data_final_score:'',
                    monthly_site_target:'',
                    monthly_site_weight:'',
                    monthly_site_rating_scale:'',
                    monthly_site_final_score:'',
                    productivity_target:'',
                    productivity_weight:'',
                    productivity_rating_scale:'',
                    productivity_final_score:'',
                    total_score:'',
                    commendation:'',
                    action_plans:'',
                },
                onboard: {
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    completion_target: '',
                    completion_weight: '',
                    completion_rating_scale: '',
                    completion_final_score: '',
                    submission_target: '',
                    submission_weight: '',
                    submission_rating_scale: '',
                    submission_final_score: '',
                    bank_target: '',
                    bank_weight: '',
                    bank_rating_scale: '',
                    bank_final_score: '',
                    hmo_target: '',
                    hmo_weight: '',
                    hmo_rating_scale: '',
                    hmo_final_score: '',
                    escalation_target: '',
                    escalation_weight: '',
                    escalation_rating_scale: '',
                    escalation_final_score: '',
                    inssuance_target: '',
                    inssuance_weight: '',
                    inssuance_rating_scale: '',
                    inssuance_final_score: '',
                    submission_by_weekly_target: '',
                    submission_by_weekly_weight: '',
                    submission_by_weekly_rating_scale: '',
                    submission_by_weekly_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                payroll: {
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    payroll_reports_target: '',
                    payroll_reports_weight: '',
                    payroll_reports_rating_scale: '',
                    payroll_reports_final_score: '',
                    government_target: '',
                    government_weight: '',
                    government_rating_scale: '',
                    government_final_score: '',
                    bank_target: '',
                    bank_weight: '',
                    bank_rating_scale: '',
                    bank_final_score: '',
                    escalation_target: '',
                    escalation_weight: '',
                    escalation_rating_scale: '',
                    escalation_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                recruitment: {
                    number_of_hires_target: '',
                    number_of_hires_actual_rating: '',
                    number_of_hires_weight: '',
                    number_of_hires_rating_scale: '',
                    number_of_hires_comment: '',
                    time_to_fill_target: '',
                    time_to_fill_actual_rating: '',
                    time_to_fill_weight: '',
                    time_to_fill_rating_scale: '',
                    time_to_fill_comment: '',
                    work_ethics_number_target: '',
                    work_ethics_number_actual_rating: '',
                    work_ethics_number_weight: '',
                    work_ethics_number_rating_scale: '',
                    work_ethics_number_comment: '',
                    work_ethics_nte_target: '',
                    work_ethics_nte_actual_rating: '',
                    work_ethics_nte_weight: '',
                    work_ethics_nte_rating_scale: '',
                    work_ethics_nte_comment: '',
                    recruitment_target: '',
                    recruitment_actual_rating: '',
                    recruitment_weight: '',
                    recruitment_rating_scale: '',
                    recruitment_comment: '',
                    total_score: '',
                    development_plan: '',
                    weakness: '',
                    strengths: '',
                    managers_feedback: '',
                    employees_acknowledge: '',
                },
                self_rating_hrbp: {
                    self_thinking_target: '',
                    self_thinking_weight: '',
                    self_thinking_rating_scale: '',
                    self_thinking_final_score: '',
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    data_target: '',
                    data_weight: '',
                    data_rating_scale: '',
                    data_final_score: '',
                    intervention_target: '',
                    intervention_weight: '',
                    intervention_rating_scale: '',
                    intervention_final_score: '',
                    cluster_target: '',
                    cluster_weight: '',
                    cluster_rating_scale: '',
                    cluster_final_score: '',
                    productivity_target: '',
                    productivity_weight: '',
                    productivity_rating_scale: '',
                    productivity_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                self_rating_hrbp_site_lead: {
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    leadership_target: '',
                    leadership_weight: '',
                    leadership_rating_scale: '',
                    leadership_final_score: '',
                    attendance_reliability_target: '',
                    attendance_reliability_weight: '',
                    attendance_reliability_rating_scale: '',
                    attendance_reliability_final_score: '',
                    analysis_target: '',
                    analysis_weight: '',
                    analysis_rating_scale: '',
                    analysis_final_score: '',
                    satisfaction_target: '',
                    satisfaction_weight: '',
                    satisfaction_rating_scale: '',
                    satisfaction_final_score: '',
                    productivity_target: '',
                    productivity_weight: '',
                    productivity_rating_scale: '',
                    productivity_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                sourcing: {
                    show_up_target: '',
                    show_up_weight: '',
                    show_up_rating_scale: '',
                    show_up_final_score: '',
                    time_to_fill_target: '',
                    time_to_fill_weight: '',
                    time_to_fill_rating_scale: '',
                    time_to_fill_final_score: '',
                    escalation_target: '',
                    escalation_weight: '',
                    escalation_rating_scale: '',
                    escalation_final_score: '',
                    no_nte_target: '',
                    no_nte_weight: '',
                    no_nte_rating_scale: '',
                    no_nte_final_score: '',
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    total_score: '',
                    development_plan: '',
                    weaknesses: '',
                    strengths: '',
                    managers_feedback: '',
                    employees_acknowledge: '',
                },
                supervisor_recruitment: {
                    support_hiring_target: '',
                    support_hiring_actual_rating: '',
                    support_hiring_weight: '',
                    support_hiring_rating_scale: '',
                    support_hiring_comments: '',
                    agent_hiring_target: '',
                    agent_hiring_actual_rating: '',
                    agent_hiring_weight: '',
                    agent_hiring_rating_scale: '',
                    agent_hiring_comments: '',
                    development_target: '',
                    development_actual_rating: '',
                    development_weight: '',
                    development_rating_scale: '',
                    development_comments: '',
                    escalation_target: '',
                    escalation_actual_rating: '',
                    escalation_weight: '',
                    escalation_rating_scale: '',
                    escalation_comments: '',
                    infraction_target: '',
                    infraction_actual_rating: '',
                    infraction_weight: '',
                    infraction_rating_scale: '',
                    infraction_comments: '',
                    recruitment_teams_target: '',
                    recruitment_teams_actual_rating: '',
                    recruitment_teams_weight: '',
                    recruitment_teams_rating_scale: '',
                    recruitment_teams_comments: '',
                    total_score: '',
                    development_plan: '',
                    weaknesses: '',
                    strengths: '',
                    managers_feedback: '',
                    employees_acknowledge: '',
                },
                supervisor_hrbp: {
                    self_thinking_target: '',
                    self_thinking_weight: '',
                    self_thinking_rating_scale: '',
                    self_thinking_final_score: '',
                    initiative_target: '',
                    initiative_weight: '',
                    initiative_rating_scale: '',
                    initiative_final_score: '',
                    work_ethics_target: '',
                    work_ethics_weight: '',
                    work_ethics_rating_scale: '',
                    work_ethics_final_score: '',
                    responsiveness_target: '',
                    responsiveness_weight: '',
                    responsiveness_rating_scale: '',
                    responsiveness_final_score: '',
                    attendance_target: '',
                    attendance_weight: '',
                    attendance_rating_scale: '',
                    attendance_final_score: '',
                    data_target: '',
                    data_weight: '',
                    data_rating_scale: '',
                    data_final_score: '',
                    intervention_target: '',
                    intervention_weight: '',
                    intervention_rating_scale: '',
                    intervention_final_score: '',
                    cluster_target: '',
                    cluster_weight: '',
                    cluster_rating_scale: '',
                    cluster_final_score: '',
                    total_score: '',
                    commendation: '',
                    action_plans: '',
                },
                selectedCategory: [],
                selectedOffense: [],
                listForms: this.$constants.CL_Form_Type,
                offenseDisabled: true,
                disabledWitness: false,
                disabledAttachmentType: false,
                showOffense: false,
                showOthers: false,
                showPerformanceReview: false,
                showGravity: false,
                showDescription: false,
                showTypeInfraction: false,
                showAttendancePoints: false,
                showDefinition: false,
                infractionDisabled: false,
                provision_multiple: [],
                offenseMultipleDisabled: true,
                categoryDisabled: false,
                showProvisionMultiple: false,
                showOffenseMultiple: false,
                showGravityMultiple: false,
                showDescriptionMultiple: false,
                showAddOffense: false,
                showDeleteOffense: false,
                maxDate: null,
                indexCnt: 2,
                offenseCnt: 0,
                offense_remove: [],
                offense_index: [],
                attachments: [],
                attachment_image: [],
                fileData: new FormData(),
                fileImageData: new FormData(),
            };
        },

        created() {
            this.eventCategory();
            this.eventOffense();

            this.coaching_log.respondent = '';
            this.coaching_log.category = null;
            this.coaching_log.offense = null;
            this.coaching_log.gravity = '';
            this.coaching_log.offense_description = '';
            this.coaching_log.attachment_type = { "value": 0, "text": "Others" };
            this.coaching_log.attachment_type = { "value": 100, "text": "Performance Review" };
            let today = new Date()
            let tomorrow = new Date(today)
            tomorrow.setDate(tomorrow.getDate() + 1)
            this.maxDate = {
              disabledDates: {
                from: tomorrow,
              }
            }
        },
        methods: {
            fieldChange(e) {
                this.attachments = [];
                let selectedFiles = e.target.files

                if (!selectedFiles.length) {
                    return false
                }

                for (let value of selectedFiles) {
                    let type = value.name.split('.');

                    if (type[type.length - 1] == "csv") {
                        this.attachments.push(value);
                    } else {
                        this.attachments = [];
                        this.$swal({
                            title: 'Invalid File Format',
                            text: 'Invalid File Format, only .csv file is allowed',
                            type: "error",
                            showConfirmButton: true
                        });
                        let csv = document.querySelectorAll("[id='file-upload']");
                        csv[0].value = "";
                    }
                }

            },

            fieldChangeImage(e) {
                this.attachment_image = [];
                let image = e.target.files

                if (!image.length) {
                    return false
                }

                for (let value of image) {
                    let type = value.name.split('.');

                    if (type[type.length - 1] == "jpg" || type[type.length - 1] == "png" || type[type.length - 1] == "jpeg") {
                        this.attachment_image.push(value);
                    } else {
                        this.attachment_image = [];
                        this.$swal({
                            title: 'Invalid File Format',
                            text: 'Please use (.jpeg .png .jpg)',
                            type: "error",
                            showConfirmButton: true
                        });
                        document.getElementById("image").value = '';
                    }
                }

            },

            upload: function () {
				if (this.attachments.length === 0) {
                	this.$swal({
	                    title: 'Please Choose A File',
	                    type: "error",
	                    showCancelButton: false,
	                    showConfirmButton: true
	                });
                    this.attachments = [];
	            } else {
	                for (let value of this.attachments) {
	                    this.fileData.append('attachments[]', value);
	                }
					this.$constants.Coaching_API.post('/importKPI', this.fileData)
					.then(response => {
						if (response.data.isvalid) {
                            this.attachments = [];
							this.$swal({
			                    title: 'Upload successfully',
			                    type: "success",
			                    showCancelButton: false,
			                    showConfirmButton: true
			                })
				                .then((result) => {
				                    if (result.value) {
				                        location.reload();
				                    }
				                })
                        } else {
                            this.attachments = [];
							let message_array = response.data.errors.toString();
							let message = message_array.replace(",", "<br />");
							this.$swal({
    		                    title: "Upload Error !!",
    		                    type: "error",
								html: message,
    		                    showCancelButton: false,
    		                    showConfirmButton: true
    		                }).then((result) => {
				                    if (result.value) {
				                        location.reload();
				                    }
				                })
                        }
	                })
	                .catch(error => {
						if (error.status == 503) {
							this.$swal({
			                    title: 'Please Wait Momentarily',
			                    type: "success",
			                    showCancelButton: false,
			                    showConfirmButton: true
			                });
						} else {
							this.globalErrorHandling(error);
						}
	                });
				}
            },
            dropdownOffenseCategory(category_value, offense_remove, index) {
                this.offense_index.push(index);

                this.$constants.Coc_API.get("/dropdown/offense/category/multiple/"
                +category_value+"/"
                +offense_remove)
                .then(response => {
                        this.selectedOffense = response.data;
                        // this.deleteDropdownSelectedOffense(index);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            dropdownInfraction() {
                this.$constants.Coc_API.get("/dropdown/attendance/infraction")
                .then(response => {
                    this.selectedInfraction = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventCategory(){
                this.$constants.Coc_API.get("/dropdown/category")
                    .then(response => {
                        this.selectedCategory = response.data;
                        this.selectedCategory.push({text: 'Performance Review',value:100})
                        this.selectedCategory.push({text: 'Others',value:0})
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            eventOffense(){
                if (this.coaching_log.category.value != undefined) {
                    if(this.coaching_log.category.value == 15){
                        this.infractionDisabled = false;
                        this.offenseDisabled = true;
                        this.disabledWitness = true;

                        this.coaching_log.offense = '';
                        this.coaching_log.gravity = '';

                        this.showPerfor = false;
                        this.showOthers = false;
                        this.showPerformanceReview = false;
                        this.showOffense = false;
                        this.showGravity = false;
                        this.showDescription = false;
                        this.showTypeInfraction = true;
                        this.showAttendancePoints = true;
                        this.showDefinition = true;
                        this.coaching_log.others = null;
                        this.coaching_log.performance_review = null;

                        this.dropdownInfraction();

                    } else if(this.coaching_log.category.value == 0) {
                        this.showOffense = false;
                        this.coaching_log.offense = null;
                        this.showPerformanceReview = false;
                        this.showOthers = true;
                    } else if(this.coaching_log.category.value == 100) {
                        this.showOffense = false;
                        this.coaching_log.offense = null;
                        this.showPerformanceReview = true;
                        this.showOthers = false;
                    } else {
                        this.infractionDisabled = true;
                        this.showOthers = false;
                        this.showPerformanceReview = false;
                        this.coaching_log.others = null;
                        this.coaching_log.performance_review = null;
                        this.offenseDisabled = false;
                        this.coaching_log.offense = '';
                        this.coaching_log.gravity = '';
                        this.disabledWitness = false;

                        this.showOffense = true;
                        this.showGravity = true;
                        this.showDescription = true;
                        this.showTypeInfraction = false;
                        this.showAttendancePoints = false;
                        this.showDefinition = false;

                        this.$constants.Coc_API.get("/dropdown/offense/category/"+this.coaching_log.category.value)
                        .then(response => {
                                this.selectedOffense = response.data;
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    }
                }
            },
            eventUpdate() {
                $('.ladda-button').attr("disabled", true);
                if (this.coaching_log.offense) {
                    this.coaching_log.offense_multiple.push(this.coaching_log.offense.value);
                }

                if (this.provision_multiple.length > 0) {
                    for(let o=0; o<this.provision_multiple.length; o++){
                        if (this.provision_multiple[o].offense_multi.value) {
                            this.coaching_log.offense_multiple.push(this.provision_multiple[o].offense_multi.value);
                        }
                    }
                }
                let getAxios = "";
                if (this.coaching_log.infraction.value) {
                    getAxios = this.$constants.Coaching_API.get('/coaching_log_form', {
                        params: {
                            employee_no: this.employee_no,
                            findings: this.coaching_log.findings,
                            point_of_disscussion: this.coaching_log.point_of_disscussion,
                            action_plans: this.coaching_log.action_plans,
                            agents_commitment: this.coaching_log.agents_commitment,
                            supervisors_commitment: this.coaching_log.supervisors_commitment,
                            date_start: this.coaching_log.date_start,
                            date_end: this.coaching_log.date_end,
                            number_occurrence: this.coaching_log.number_occurrence,
                            attendancepointssystem_id: this.coaching_log.infraction.value,
                            form_type: (this.coaching_log.selectedForm) ? this.coaching_log.selectedForm.value : '',
                        },
                    })
                } else if (this.coaching_log.others) {
                    getAxios = this.$constants.Coaching_API.get('/coaching_log_form', {
                        params: {
                            employee_no: this.employee_no,
                            findings: this.coaching_log.findings,
                            point_of_disscussion: this.coaching_log.point_of_disscussion,
                            action_plans: this.coaching_log.action_plans,
                            agents_commitment: this.coaching_log.agents_commitment,
                            supervisors_commitment: this.coaching_log.supervisors_commitment,
                            date_start: this.coaching_log.date_start,
                            date_end: this.coaching_log.date_end,
                            number_occurrence: this.coaching_log.number_occurrence,
                            others: this.coaching_log.others,
                            form_type: (this.coaching_log.selectedForm) ? this.coaching_log.selectedForm.value : '',
                        },
                    })
                } else if (this.coaching_log.performance_review) {
                   getAxios = this.$constants.Coaching_API.get('/coaching_log_form', {
                       params: {
                           employee_no: this.employee_no,
                           findings: this.coaching_log.findings,
                           point_of_disscussion: this.coaching_log.point_of_disscussion,
                           action_plans: this.coaching_log.action_plans,
                           agents_commitment: this.coaching_log.agents_commitment,
                           supervisors_commitment: this.coaching_log.supervisors_commitment,
                           date_start: this.coaching_log.date_start,
                           date_end: this.coaching_log.date_end,
                           number_occurrence: this.coaching_log.number_occurrence,
                           performance_review: this.coaching_log.performance_review,
                           form_type: (this.coaching_log.selectedForm) ? this.coaching_log.selectedForm.value : '',
                       },
                   })
                } else if (this.coaching_log.offense_multiple.length > 1) {
                    getAxios = this.$constants.Coaching_API.get('/coaching_log_form', {
                        params: {
                            employee_no: this.employee_no,
                            findings: this.coaching_log.findings,
                            point_of_disscussion: this.coaching_log.point_of_disscussion,
                            action_plans: this.coaching_log.action_plans,
                            agents_commitment: this.coaching_log.agents_commitment,
                            supervisors_commitment: this.coaching_log.supervisors_commitment,
                            date_start: this.coaching_log.date_start,
                            date_end: this.coaching_log.date_end,
                            number_occurrence: this.coaching_log.number_occurrence,
                            offense_multiple: this.coaching_log.offense_multiple,
                            form_type: (this.coaching_log.selectedForm) ? this.coaching_log.selectedForm.value : '',
                        },
                    })
                } else {
                    getAxios = this.$constants.Coaching_API.get('/coaching_log_form', {
                        params: {
                            employee_no: this.employee_no,
                            findings: this.coaching_log.findings,
                            point_of_disscussion: this.coaching_log.point_of_disscussion,
                            action_plans: this.coaching_log.action_plans,
                            agents_commitment: this.coaching_log.agents_commitment,
                            supervisors_commitment: this.coaching_log.supervisors_commitment,
                            date_start: this.coaching_log.date_start,
                            date_end: this.coaching_log.date_end,
                            number_occurrence: this.coaching_log.number_occurrence,
                            offense_id: this.coaching_log.offense.value,
                            form_type: (this.coaching_log.selectedForm) ? this.coaching_log.selectedForm.value : '',
                        },
                    })
                }

                getAxios.then(response => {
                    if (this.coaching_log.selectedForm && response.data.cl_number) {
                        if (this.coaching_log.selectedForm.value === 0) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    loan_amount_target: this.stage_one.loan_amount_target,
                                    loan_amount_rating_eom: this.stage_one.loan_amount_rating_eom,
                                    loan_amount_rating_w2: this.stage_one.loan_amount_rating_w2,
                                    loan_amount_rating_w3: this.stage_one.loan_amount_rating_w3,
                                    loan_amount_rating_w4: this.stage_one.loan_amount_rating_w4,
                                    loan_amount_computation: this.stage_one.loan_amount_computation,
                                    loan_amount_comment: this.stage_one.loan_amount_comment,
                                    knowledge_target: this.stage_one.knowledge_target,
                                    knowledge_rating: this.stage_one.knowledge_rating,
                                    knowledge_computation: this.stage_one.knowledge_computation,
                                    knowledge_comment: this.stage_one.knowledge_comment,
                                    qa_score_target: this.stage_one.qa_score_target,
                                    qa_score_rating: this.stage_one.qa_score_rating,
                                    qa_score_computation: this.stage_one.qa_score_computation,
                                    qa_score_comment: this.stage_one.qa_score_comment,
                                    coaching_log_target: this.stage_one.coaching_log_target,
                                    coaching_log_rating: this.stage_one.coaching_log_rating,
                                    coaching_log_computation: this.stage_one.coaching_log_computation,
                                    coaching_log_comment: this.stage_one.coaching_log_comment,
                                    attendance_reliability_target: this.stage_one.attendance_reliability_target,
                                    attendance_reliability_rating: this.stage_one.attendance_reliability_rating,
                                    attendance_reliability_computation: this.stage_one.attendance_reliability_computation,
                                    attendance_reliability_comment: this.stage_one.attendance_reliability_comment,
                                    strengths: this.stage_one.strengths,
                                    weakness: this.stage_one.weakness,
                                    action_plans: this.stage_one.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 1) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    stage: this.stage_two_to_five.stage,
                                    loan_amount_target: this.stage_two_to_five.loan_amount_target,
                                    loan_amount_rating: this.stage_two_to_five.loan_amount_rating,
                                    loan_amount_computation: this.stage_two_to_five.loan_amount_computation,
                                    loan_amount_comment: this.stage_two_to_five.loan_amount_comment,
                                    qa_compliance_target: this.stage_two_to_five.qa_compliance_target,
                                    qa_compliance_rating: this.stage_two_to_five.qa_compliance_rating,
                                    qa_compliance_computation: this.stage_two_to_five.qa_compliance_computation,
                                    qa_compliance_comment: this.stage_two_to_five.qa_compliance_comment,
                                    qa_score_target: this.stage_two_to_five.qa_score_target,
                                    qa_score_rating: this.stage_two_to_five.qa_score_rating,
                                    qa_score_computation: this.stage_two_to_five.qa_score_computation,
                                    qa_score_comment: this.stage_two_to_five.qa_score_comment,
                                    correction_target: this.stage_two_to_five.correction_target,
                                    correction_rating: this.stage_two_to_five.correction_rating,
                                    correction_computation: this.stage_two_to_five.correction_computation,
                                    correction_comment: this.stage_two_to_five.correction_comment,
                                    attendance_reliability_target: this.stage_two_to_five.attendance_reliability_target,
                                    attendance_reliability_rating: this.stage_two_to_five.attendance_reliability_rating,
                                    attendance_reliability_computation: this.stage_two_to_five.attendance_reliability_computation,
                                    attendance_reliability_comment: this.stage_two_to_five.attendance_reliability_comment,
                                    strengths: this.stage_two_to_five.strengths,
                                    weakness: this.stage_two_to_five.weakness,
                                    action_plans: this.stage_two_to_five.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 2) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    attendance_reliability_target: this.admin.attendance_reliability_target,
                                    attendance_reliability_actual_rating: this.admin.attendance_reliability_actual_rating,
                                    attendance_reliability_weight_of_kpi: this.admin.attendance_reliability_weight_of_kpi,
                                    attendance_reliability_rating: this.admin.attendance_reliability_rating,
                                    attendance_reliability_comment: this.admin.attendance_reliability_comment,
                                    punctuality_target: this.admin.punctuality_target,
                                    punctuality_actual_rating: this.admin.punctuality_actual_rating,
                                    punctuality_weight_of_kpi: this.admin.punctuality_weight_of_kpi,
                                    punctuality_rating: this.admin.punctuality_rating,
                                    punctuality_comment: this.admin.punctuality_comment,
                                    process_knowledge_target: this.admin.process_knowledge_target,
                                    process_knowledge_actual_rating: this.admin.process_knowledge_actual_rating,
                                    process_knowledge_weight_of_kpi: this.admin.process_knowledge_weight_of_kpi,
                                    process_knowledge_rating: this.admin.process_knowledge_rating,
                                    process_knowledge_comment: this.admin.process_knowledge_comment,
                                    work_ethics_number_target: this.admin.work_ethics_number_target,
                                    work_ethics_number_actual_rating: this.admin.work_ethics_number_actual_rating,
                                    work_ethics_number_weight_of_kpi: this.admin.work_ethics_number_weight_of_kpi,
                                    work_ethics_number_rating: this.admin.work_ethics_number_rating,
                                    work_ethics_number_comment: this.admin.work_ethics_number_comment,
                                    work_ethics_no_nte_target: this.admin.work_ethics_no_nte_target,
                                    work_ethics_no_nte_actual_rating: this.admin.work_ethics_no_nte_actual_rating,
                                    work_ethics_no_nte_weight_of_kpi: this.admin.work_ethics_no_nte_weight_of_kpi,
                                    work_ethics_no_nte_rating: this.admin.work_ethics_no_nte_rating,
                                    work_ethics_no_nte_comment: this.admin.work_ethics_no_nte_comment,
                                    total: this.admin.total,
                                    development_plan: this.admin.development_plan,
                                    weakness: this.admin.weakness,
                                    strengths: this.admin.strengths,
                                    managers_feedback: this.admin.managers_feedback,
                                    employees_acknowledge: this.admin.employees_acknowledge,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 3) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    initiative_target: this.hris.initiative_target,
                                    initiative_weight: this.hris.initiative_weight,
                                    initiative_rating_scale: this.hris.initiative_rating_scale,
                                    initiative_final_score: this.hris.initiative_final_score,
                                    work_ethics_target: this.hris.work_ethics_target,
                                    work_ethics_weight: this.hris.work_ethics_weight,
                                    work_ethics_rating_scale: this.hris.work_ethics_rating_scale,
                                    work_ethics_final_score: this.hris.work_ethics_final_score,
                                    responsiveness_target: this.hris.responsiveness_target,
                                    responsiveness_weight: this.hris.responsiveness_weight,
                                    responsiveness_rating_scale: this.hris.responsiveness_rating_scale,
                                    responsiveness_final_score: this.hris.responsiveness_final_score,
                                    attendance_reliability_target: this.hris.attendance_reliability_target,
                                    attendance_reliability_weight: this.hris.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.hris.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.hris.attendance_reliability_final_score,
                                    timely_and_accuraccy_of_on_boarding_target: this.hris.timely_and_accuraccy_of_on_boarding_target,
                                    timely_and_accuraccy_of_on_boarding_weight: this.hris.timely_and_accuraccy_of_on_boarding_weight,
                                    timely_and_accuraccy_of_on_boarding_rating_scale: this.hris.timely_and_accuraccy_of_on_boarding_rating_scale,
                                    timely_and_accuraccy_of_on_boarding_final_score: this.hris.timely_and_accuraccy_of_on_boarding_final_score,
                                    timely_and_accuraccy_of_off_boarding_target: this.hris.timely_and_accuraccy_of_off_boarding_target,
                                    timely_and_accuraccy_of_off_boarding_weight: this.hris.timely_and_accuraccy_of_off_boarding_weight,
                                    timely_and_accuraccy_of_off_boarding_rating_scale: this.hris.timely_and_accuraccy_of_off_boarding_rating_scale,
                                    timely_and_accuraccy_of_off_boarding_final_score: this.hris.timely_and_accuraccy_of_off_boarding_final_score,
                                    timely_and_accuraccy_of_updating_in_masterfile_target: this.hris.timely_and_accuraccy_of_updating_in_masterfile_target,
                                    timely_and_accuraccy_of_updating_in_masterfile_weight: this.hris.timely_and_accuraccy_of_updating_in_masterfile_weight,
                                    timely_and_accuraccy_of_updating_in_masterfile_rating_scale: this.hris.timely_and_accuraccy_of_updating_in_masterfile_rating_scale,
                                    timely_and_accuraccy_of_updating_in_masterfile_final_score: this.hris.timely_and_accuraccy_of_updating_in_masterfile_final_score,
                                    submission_of_weekly_reports_target: this.hris.submission_of_weekly_reports_target,
                                    submission_of_weekly_reports_weight: this.hris.submission_of_weekly_reports_weight,
                                    submission_of_weekly_reports_rating_scale: this.hris.submission_of_weekly_reports_rating_scale,
                                    submission_of_weekly_reports_final_score: this.hris.submission_of_weekly_reports_final_score,
                                    final_pay_target: this.hris.final_pay_target,
                                    final_pay_weight: this.hris.final_pay_weight,
                                    final_pay_rating_scale: this.hris.final_pay_rating_scale,
                                    final_pay_final_score: this.hris.final_pay_final_score,
                                    number_of_escalation_target: this.hris.number_of_escalation_target,
                                    number_of_escalation_weight: this.hris.number_of_escalation_weight,
                                    number_of_escalation_rating_scale: this.hris.number_of_escalation_rating_scale,
                                    number_of_escalation_final_score: this.hris.number_of_escalation_final_score,
                                    total_score: this.hris.total_score,
                                    commendation: this.hris.commendation,
                                    action_plans: this.hris.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 4) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    initiative_target: this.cnb.initiative_target,
                                    initiative_weight: this.cnb.initiative_weight,
                                    initiative_rating_scale: this.cnb.initiative_rating_scale,
                                    initiative_final_score: this.cnb.initiative_final_score,
                                    work_ethics_target: this.cnb.work_ethics_target,
                                    work_ethics_weight: this.cnb.work_ethics_weight,
                                    work_ethics_rating_scale: this.cnb.work_ethics_rating_scale,
                                    work_ethics_final_score: this.cnb.work_ethics_final_score,
                                    responsiveness_target: this.cnb.responsiveness_target,
                                    responsiveness_weight: this.cnb.responsiveness_weight,
                                    responsiveness_rating_scale: this.cnb.responsiveness_rating_scale,
                                    responsiveness_final_score: this.cnb.responsiveness_final_score,
                                    attendance_reliability_target: this.cnb.attendance_reliability_target,
                                    attendance_reliability_weight: this.cnb.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.cnb.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.cnb.attendance_reliability_final_score,
                                    payroll_file_target: this.cnb.payroll_file_target,
                                    payroll_file_weight: this.cnb.payroll_file_weight,
                                    payroll_file_rating_scale: this.cnb.payroll_file_rating_scale,
                                    payroll_file_final_score: this.cnb.payroll_file_final_score,
                                    timekeeping_target: this.cnb.timekeeping_target,
                                    timekeeping_weight: this.cnb.timekeeping_weight,
                                    timekeeping_rating_scale: this.cnb.timekeeping_rating_scale,
                                    timekeeping_final_score: this.cnb.timekeeping_final_score,
                                    processing_target: this.cnb.processing_target,
                                    processing_weight: this.cnb.processing_weight,
                                    processing_rating_scale: this.cnb.processing_rating_scale,
                                    processing_final_score: this.cnb.processing_final_score,
                                    hmo_target: this.cnb.hmo_target,
                                    hmo_weight: this.cnb.hmo_weight,
                                    hmo_rating_scale: this.cnb.hmo_rating_scale,
                                    hmo_final_score: this.cnb.hmo_final_score,
                                    inssuance_target: this.cnb.inssuance_target,
                                    inssuance_weight: this.cnb.inssuance_weight,
                                    inssuance_rating_scale: this.cnb.inssuance_rating_scale,
                                    inssuance_final_score: this.cnb.inssuance_final_score,
                                    final_pay_target: this.cnb.final_pay_target,
                                    final_pay_weight: this.cnb.final_pay_weight,
                                    final_pay_rating_scale: this.cnb.final_pay_rating_scale,
                                    final_pay_final_score: this.cnb.final_pay_final_score,
                                    total_score: this.cnb.total_score,
                                    commendation: this.cnb.commendation,
                                    action_plans: this.cnb.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 5) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    initiative_target: this.final_pay.initiative_target,
                                    initiative_weight: this.final_pay.initiative_weight,
                                    initiative_rating_scale: this.final_pay.initiative_rating_scale,
                                    initiative_final_score: this.final_pay.initiative_final_score,
                                    work_ethics_target: this.final_pay.work_ethics_target,
                                    work_ethics_weight: this.final_pay.work_ethics_weight,
                                    work_ethics_rating_scale: this.final_pay.work_ethics_rating_scale,
                                    work_ethics_final_score: this.final_pay.work_ethics_final_score,
                                    responsiveness_target: this.final_pay.responsiveness_target,
                                    responsiveness_weight: this.final_pay.responsiveness_weight,
                                    responsiveness_rating_scale: this.final_pay.responsiveness_rating_scale,
                                    responsiveness_final_score: this.final_pay.responsiveness_final_score,
                                    attendance_reliability_target: this.final_pay.attendance_reliability_target,
                                    attendance_reliability_weight: this.final_pay.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.final_pay.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.final_pay.attendance_reliability_final_score,
                                    accuracy_computation_target: this.final_pay.accuracy_computation_target,
                                    accuracy_computation_weight: this.final_pay.accuracy_computation_weight,
                                    accuracy_computation_rating_scale: this.final_pay.accuracy_computation_rating_scale,
                                    accuracy_computation_final_score: this.final_pay.accuracy_computation_final_score,
                                    timely_target: this.final_pay.timely_target,
                                    timely_weight: this.final_pay.timely_weight,
                                    timely_rating_scale: this.final_pay.timely_rating_scale,
                                    timely_final_score: this.final_pay.timely_final_score,
                                    submission_of_final_rating_target: this.final_pay.submission_of_final_rating_target,
                                    submission_of_final_rating_weight: this.final_pay.submission_of_final_rating_weight,
                                    submission_of_final_rating_scale: this.final_pay.submission_of_final_rating_scale,
                                    submission_of_final_rating_final_score: this.final_pay.submission_of_final_rating_final_score,
                                    number_target: this.final_pay.number_target,
                                    number_weight: this.final_pay.number_weight,
                                    number_rating_scale: this.final_pay.number_rating_scale,
                                    number_final_score: this.final_pay.number_final_score,
                                    total_score: this.final_pay.total_score,
                                    commendation: this.final_pay.commendation,
                                    action_plans: this.final_pay.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 6) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    work_ethics_target: this.manager_rating_sl.work_ethics_target,
                                    work_ethics_weight: this.manager_rating_sl.work_ethics_weight,
                                    work_ethics_rating_scale: this.manager_rating_sl.work_ethics_rating_scale,
                                    work_ethics_final_score: this.manager_rating_sl.work_ethics_final_score,
                                    leadership_target: this.manager_rating_sl.leadership_target,
                                    leadership_weight: this.manager_rating_sl.leadership_weight,
                                    leadership_rating_scale: this.manager_rating_sl.leadership_rating_scale,
                                    leadership_final_score: this.manager_rating_sl.leadership_final_score,
                                    attendance_reliability_target: this.manager_rating_sl.attendance_reliability_target,
                                    attendance_reliability_weight: this.manager_rating_sl.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.manager_rating_sl.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.manager_rating_sl.attendance_reliability_final_score,
                                    monthly_data_target: this.manager_rating_sl.monthly_data_target,
                                    monthly_data_weight: this.manager_rating_sl.monthly_data_weight,
                                    monthly_data_rating_scale: this.manager_rating_sl.monthly_data_rating_scale,
                                    monthly_data_final_score: this.manager_rating_sl.monthly_data_final_score,
                                    monthly_site_target: this.manager_rating_sl.monthly_site_target,
                                    monthly_site_weight: this.manager_rating_sl.monthly_site_weight,
                                    monthly_site_rating_scale: this.manager_rating_sl.monthly_site_rating_scale,
                                    monthly_site_final_score: this.manager_rating_sl.monthly_site_final_score,
                                    productivity_target: this.manager_rating_sl.productivity_target,
                                    productivity_weight: this.manager_rating_sl.productivity_weight,
                                    productivity_rating_scale: this.manager_rating_sl.productivity_rating_scale,
                                    productivity_final_score: this.manager_rating_sl.productivity_final_score,
                                    total_score: this.manager_rating_sl.total_score,
                                    commendation: this.manager_rating_sl.commendation,
                                    action_plans: this.manager_rating_sl.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 7) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    initiative_target: this.onboard.initiative_target,
                                    initiative_weight: this.onboard.initiative_weight,
                                    initiative_rating_scale: this.onboard.initiative_rating_scale,
                                    initiative_final_score: this.onboard.initiative_final_score,
                                    work_ethics_target: this.onboard.work_ethics_target,
                                    work_ethics_weight: this.onboard.work_ethics_weight,
                                    work_ethics_rating_scale: this.onboard.work_ethics_rating_scale,
                                    work_ethics_final_score: this.onboard.work_ethics_final_score,
                                    responsiveness_target: this.onboard.responsiveness_target,
                                    responsiveness_weight: this.onboard.responsiveness_weight,
                                    responsiveness_rating_scale: this.onboard.responsiveness_rating_scale,
                                    responsiveness_final_score: this.onboard.responsiveness_final_score,
                                    attendance_reliability_target: this.onboard.attendance_reliability_target,
                                    attendance_reliability_weight: this.onboard.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.onboard.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.onboard.attendance_reliability_final_score,
                                    completion_target: this.onboard.completion_target,
                                    completion_weight: this.onboard.completion_weight,
                                    completion_rating_scale: this.onboard.completion_rating_scale,
                                    completion_final_score: this.onboard.completion_final_score,
                                    submission_target: this.onboard.submission_target,
                                    submission_weight: this.onboard.submission_weight,
                                    submission_rating_scale: this.onboard.submission_rating_scale,
                                    submission_final_score: this.onboard.submission_final_score,
                                    bank_target: this.onboard.bank_target,
                                    bank_weight: this.onboard.bank_weight,
                                    bank_rating_scale: this.onboard.bank_rating_scale,
                                    bank_final_score: this.onboard.bank_final_score,
                                    hmo_target: this.onboard.hmo_target,
                                    hmo_weight: this.onboard.hmo_weight,
                                    hmo_rating_scale: this.onboard.hmo_rating_scale,
                                    hmo_final_score: this.onboard.hmo_final_score,
                                    escalation_target: this.onboard.escalation_target,
                                    escalation_weight: this.onboard.escalation_weight,
                                    escalation_rating_scale: this.onboard.escalation_rating_scale,
                                    escalation_final_score: this.onboard.escalation_final_score,
                                    inssuance_target: this.onboard.inssuance_target,
                                    inssuance_weight: this.onboard.inssuance_weight,
                                    inssuance_rating_scale: this.onboard.inssuance_rating_scale,
                                    inssuance_final_score: this.onboard.inssuance_final_score,
                                    submission_by_weekly_target: this.onboard.submission_by_weekly_target,
                                    submission_by_weekly_weight: this.onboard.submission_by_weekly_weight,
                                    submission_by_weekly_rating_scale: this.onboard.submission_by_weekly_rating_scale,
                                    submission_by_weekly_final_score: this.onboard.submission_by_weekly_final_score,
                                    total_score: this.onboard.total_score,
                                    commendation: this.onboard.commendation,
                                    action_plans: this.onboard.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 8) {
                           this.$constants.Coaching_API.get('/added_form', {
                               params: {
                                   form_type: response.data.form_type,
                                   cl_number: response.data.cl_number,
                                   initiative_target: this.payroll.initiative_target,
                                   initiative_weight: this.payroll.initiative_weight,
                                   initiative_rating_scale: this.payroll.initiative_rating_scale,
                                   initiative_final_score: this.payroll.initiative_final_score,
                                   work_ethics_target: this.payroll.work_ethics_target,
                                   work_ethics_weight: this.payroll.work_ethics_weight,
                                   work_ethics_rating_scale: this.payroll.work_ethics_rating_scale,
                                   work_ethics_final_score: this.payroll.work_ethics_final_score,
                                   responsiveness_target: this.payroll.responsiveness_target,
                                   responsiveness_weight: this.payroll.responsiveness_weight,
                                   responsiveness_rating_scale: this.payroll.responsiveness_rating_scale,
                                   responsiveness_final_score: this.payroll.responsiveness_final_score,
                                   attendance_reliability_target: this.payroll.attendance_reliability_target,
                                   attendance_reliability_weight: this.payroll.attendance_reliability_weight,
                                   attendance_reliability_rating_scale: this.payroll.attendance_reliability_rating_scale,
                                   attendance_reliability_final_score: this.payroll.attendance_reliability_final_score,
                                   payroll_reports_target: this.payroll.payroll_reports_target,
                                   payroll_reports_weight: this.payroll.payroll_reports_weight,
                                   payroll_reports_rating_scale: this.payroll.payroll_reports_rating_scale,
                                   payroll_reports_final_score: this.payroll.payroll_reports_final_score,
                                   government_target: this.payroll.government_target,
                                   government_weight: this.payroll.government_weight,
                                   government_rating_scale: this.payroll.government_rating_scale,
                                   government_final_score: this.payroll.government_final_score,
                                   bank_target: this.payroll.bank_target,
                                   bank_weight: this.payroll.bank_weight,
                                   bank_rating_scale: this.payroll.bank_rating_scale,
                                   bank_final_score: this.payroll.bank_final_score,
                                   escalation_target: this.payroll.escalation_target,
                                   escalation_weight: this.payroll.escalation_weight,
                                   escalation_rating_scale: this.payroll.escalation_rating_scale,
                                   escalation_final_score: this.payroll.escalation_final_score,
                                   total_score: this.payroll.total_score,
                                   commendation: this.payroll.commendation,
                                   action_plans: this.payroll.action_plans,
                               },
                           })
                           .catch(error => {
                               this.globalErrorHandling(error);
                           });
                        } else if (this.coaching_log.selectedForm.value === 9) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    number_of_hires_target: this.recruitment.number_of_hires_target,
                                    number_of_hires_actual_rating: this.recruitment.number_of_hires_actual_rating,
                                    number_of_hires_weight: this.recruitment.number_of_hires_weight,
                                    number_of_hires_rating_scale: this.recruitment.number_of_hires_rating_scale,
                                    number_of_hires_comment: this.recruitment.number_of_hires_comment,
                                    time_to_fill_target: this.recruitment.time_to_fill_target,
                                    time_to_fill_actual_rating: this.recruitment.time_to_fill_actual_rating,
                                    time_to_fill_weight: this.recruitment.time_to_fill_weight,
                                    time_to_fill_rating_scale: this.recruitment.time_to_fill_rating_scale,
                                    time_to_fill_comment: this.recruitment.time_to_fill_comment,
                                    work_ethics_number_target: this.recruitment.work_ethics_number_target,
                                    work_ethics_number_actual_rating: this.recruitment.work_ethics_number_actual_rating,
                                    work_ethics_number_weight: this.recruitment.work_ethics_number_weight,
                                    work_ethics_number_rating_scale: this.recruitment.work_ethics_number_rating_scale,
                                    work_ethics_number_comment: this.recruitment.work_ethics_number_comment,
                                    work_ethics_nte_target: this.recruitment.work_ethics_nte_target,
                                    work_ethics_nte_actual_rating: this.recruitment.work_ethics_nte_actual_rating,
                                    work_ethics_nte_weight: this.recruitment.work_ethics_nte_weight,
                                    work_ethics_nte_rating_scale: this.recruitment.work_ethics_nte_rating_scale,
                                    work_ethics_nte_comment: this.recruitment.work_ethics_nte_comment,
                                    recruitment_target: this.recruitment.recruitment_target,
                                    recruitment_actual_rating: this.recruitment.recruitment_actual_rating,
                                    recruitment_weight: this.recruitment.recruitment_weight,
                                    recruitment_rating_scale: this.recruitment.recruitment_rating_scale,
                                    recruitment_comment: this.recruitment.recruitment_comment,
                                    total_score: this.recruitment.total_score,
                                    development_plan: this.recruitment.development_plan,
                                    strengths: this.recruitment.strengths,
                                    managers_feedback: this.recruitment.managers_feedback,
                                    employees_acknowledge: this.recruitment.employees_acknowledge,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 10) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    critical_thinking_target: this.self_rating_hrbp.critical_thinking_target,
                                    critical_thinking_weight: this.self_rating_hrbp.critical_thinking_weight,
                                    critical_thinking_rating_scale: this.self_rating_hrbp.critical_thinking_rating_scale,
                                    critical_thinking_final_score: this.self_rating_hrbp.critical_thinking_final_score,
                                    initiative_target: this.self_rating_hrbp.initiative_target,
                                    initiative_weight: this.self_rating_hrbp.initiative_weight,
                                    initiative_rating_scale: this.self_rating_hrbp.initiative_rating_scale,
                                    initiative_final_score: this.self_rating_hrbp.initiative_final_score,
                                    work_ethics_target: this.self_rating_hrbp.work_ethics_target,
                                    work_ethics_weight: this.self_rating_hrbp.work_ethics_weight,
                                    work_ethics_rating_scale: this.self_rating_hrbp.work_ethics_rating_scale,
                                    work_ethics_final_score: this.self_rating_hrbp.work_ethics_final_score,
                                    responsiveness_target: this.self_rating_hrbp.responsiveness_target,
                                    responsiveness_weight: this.self_rating_hrbp.responsiveness_weight,
                                    responsiveness_rating_scale: this.self_rating_hrbp.responsiveness_rating_scale,
                                    responsiveness_final_score: this.self_rating_hrbp.responsiveness_final_score,
                                    attendance_reliability_target: this.self_rating_hrbp.attendance_reliability_target,
                                    attendance_reliability_weight: this.self_rating_hrbp.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.self_rating_hrbp.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.self_rating_hrbp.attendance_reliability_final_score,
                                    data_target: this.self_rating_hrbp.data_target,
                                    data_weight: this.self_rating_hrbp.data_weight,
                                    data_rating_scale: this.self_rating_hrbp.data_rating_scale,
                                    data_final_score: this.self_rating_hrbp.data_final_score,
                                    intervention_target: this.self_rating_hrbp.intervention_target,
                                    intervention_weight: this.self_rating_hrbp.intervention_weight,
                                    intervention_rating_scale: this.self_rating_hrbp.intervention_rating_scale,
                                    intervention_final_score: this.self_rating_hrbp.intervention_final_score,
                                    cluster_target: this.self_rating_hrbp.cluster_target,
                                    cluster_weight: this.self_rating_hrbp.cluster_weight,
                                    cluster_rating_scale: this.self_rating_hrbp.cluster_rating_scale,
                                    cluster_final_score: this.self_rating_hrbp.cluster_final_score,
                                    productivity_target: this.self_rating_hrbp.productivity_target,
                                    productivity_weight: this.self_rating_hrbp.productivity_weight,
                                    productivity_rating_scale: this.self_rating_hrbp.productivity_rating_scale,
                                    productivity_final_score: this.self_rating_hrbp.productivity_final_score,
                                    total_score: this.self_rating_hrbp.total_score,
                                    commendation: this.self_rating_hrbp.commendation,
                                    action_plans: this.self_rating_hrbp.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 11) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    work_ethics_target: this.self_rating_hrbp_site_lead.work_ethics_target,
                                    work_ethics_weight: this.self_rating_hrbp_site_lead.work_ethics_weight,
                                    work_ethics_rating_scale: this.self_rating_hrbp_site_lead.work_ethics_rating_scale,
                                    work_ethics_final_score: this.self_rating_hrbp_site_lead.work_ethics_final_score,
                                    leadership_target: this.self_rating_hrbp_site_lead.leadership_target,
                                    leadership_weight: this.self_rating_hrbp_site_lead.leadership_weight,
                                    leadership_rating_scale: this.self_rating_hrbp_site_lead.leadership_rating_scale,
                                    leadership_final_score: this.self_rating_hrbp_site_lead.leadership_final_score,
                                    attendance_reliability_target: this.self_rating_hrbp_site_lead.attendance_reliability_target,
                                    attendance_reliability_weight: this.self_rating_hrbp_site_lead.attendance_reliability_weight,
                                    attendance_reliability_rating_scale: this.self_rating_hrbp_site_lead.attendance_reliability_rating_scale,
                                    attendance_reliability_final_score: this.self_rating_hrbp_site_lead.attendance_reliability_final_score,
                                    analysis_target: this.self_rating_hrbp_site_lead.analysis_target,
                                    analysis_weight: this.self_rating_hrbp_site_lead.analysis_weight,
                                    analysis_rating_scale: this.self_rating_hrbp_site_lead.analysis_rating_scale,
                                    analysis_final_score: this.self_rating_hrbp_site_lead.analysis_final_score,
                                    satisfaction_target: this.self_rating_hrbp_site_lead.satisfaction_target,
                                    satisfaction_weight: this.self_rating_hrbp_site_lead.satisfaction_weight,
                                    satisfaction_rating_scale: this.self_rating_hrbp_site_lead.satisfaction_rating_scale,
                                    satisfaction_final_score: this.self_rating_hrbp_site_lead.satisfaction_final_score,
                                    productivity_target: this.self_rating_hrbp_site_lead.productivity_target,
                                    productivity_weight: this.self_rating_hrbp_site_lead.productivity_weight,
                                    productivity_rating_scale: this.self_rating_hrbp_site_lead.productivity_rating_scale,
                                    productivity_final_score: this.self_rating_hrbp_site_lead.productivity_final_score,
                                    total_score: this.self_rating_hrbp_site_lead.total_score,
                                    commendation: this.self_rating_hrbp_site_lead.commendation,
                                    action_plans: this.self_rating_hrbp_site_lead.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 12) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    show_up_target: this.sourcing.show_up_target,
                                    show_up_actual_rating: this.sourcing.show_up_actual_rating,
                                    show_up_weight: this.sourcing.show_up_weight,
                                    show_up_rating_scale: this.sourcing.show_up_rating_scale,
                                    show_up_comment: this.sourcing.show_up_comment,
                                    time_to_fill_target: this.sourcing.time_to_fill_target,
                                    time_to_fill_actual_rating: this.sourcing.time_to_fill_actual_rating,
                                    time_to_fill_weight: this.sourcing.time_to_fill_weight,
                                    time_to_fill_rating_scale: this.sourcing.time_to_fill_rating_scale,
                                    time_to_fill_comment: this.sourcing.time_to_fill_comment,
                                    escalation_target: this.sourcing.escalation_target,
                                    escalation_actual_rating: this.sourcing.escalation_actual_rating,
                                    escalation_weight: this.sourcing.escalation_weight,
                                    escalation_rating_scale: this.sourcing.escalation_rating_scale,
                                    escalation_comment: this.sourcing.escalation_comment,
                                    no_nte_target: this.sourcing.no_nte_target,
                                    no_nte_actual_rating: this.sourcing.no_nte_actual_rating,
                                    no_nte_weight: this.sourcing.no_nte_weight,
                                    no_nte_rating_scale: this.sourcing.no_nte_rating_scale,
                                    no_nte_comment: this.sourcing.no_nte_comment,
                                    initiative_target: this.sourcing.initiative_target,
                                    initiative_actual_rating: this.sourcing.initiative_actual_rating,
                                    initiative_weight: this.sourcing.initiative_weight,
                                    initiative_rating_scale: this.sourcing.initiative_rating_scale,
                                    initiative_comment: this.sourcing.initiative_comment,
                                    total_score: this.sourcing.total_score,
                                    development_plan: this.sourcing.development_plan,
                                    weaknesses: this.sourcing.weaknesses,
                                    strengths: this.sourcing.strengths,
                                    managers_feedback: this.sourcing.managers_feedback,
                                    employees_acknowledge: this.sourcing.employees_acknowledge,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 13) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    support_hiring_target: this.supervisor_recruitment.support_hiring_target,
                                    support_hiring_actual_rating: this.supervisor_recruitment.support_hiring_actual_rating,
                                    support_hiring_weight: this.supervisor_recruitment.support_hiring_weight,
                                    support_hiring_rating_scale: this.supervisor_recruitment.support_hiring_rating_scale,
                                    support_hiring_comments: this.supervisor_recruitment.support_hiring_comments,
                                    agent_hiring_target: this.supervisor_recruitment.agent_hiring_target,
                                    agent_hiring_actual_rating: this.supervisor_recruitment.agent_hiring_actual_rating,
                                    agent_hiring_weight: this.supervisor_recruitment.agent_hiring_weight,
                                    agent_hiring_rating_scale: this.supervisor_recruitment.agent_hiring_rating_scale,
                                    agent_hiring_comments: this.supervisor_recruitment.agent_hiring_comments,
                                    development_target: this.supervisor_recruitment.development_target,
                                    development_actual_rating: this.supervisor_recruitment.development_actual_rating,
                                    development_weight: this.supervisor_recruitment.development_weight,
                                    development_rating_scale: this.supervisor_recruitment.development_rating_scale,
                                    development_comments: this.supervisor_recruitment.development_comments,
                                    escalation_target: this.supervisor_recruitment.escalation_target,
                                    escalation_actual_rating: this.supervisor_recruitment.escalation_actual_rating,
                                    escalation_weight: this.supervisor_recruitment.escalation_weight,
                                    escalation_rating_scale: this.supervisor_recruitment.escalation_rating_scale,
                                    escalation_comments: this.supervisor_recruitment.escalation_comments,
                                    infraction_target: this.supervisor_recruitment.infraction_target,
                                    infraction_actual_rating: this.supervisor_recruitment.infraction_actual_rating,
                                    infraction_weight: this.supervisor_recruitment.infraction_weight,
                                    infraction_rating_scale: this.supervisor_recruitment.infraction_rating_scale,
                                    infraction_comments: this.supervisor_recruitment.infraction_comments,
                                    recruitment_teams_target: this.supervisor_recruitment.recruitment_teams_target,
                                    recruitment_teams_actual_rating: this.supervisor_recruitment.recruitment_teams_actual_rating,
                                    recruitment_teams_weight: this.supervisor_recruitment.recruitment_teams_weight,
                                    recruitment_teams_rating_scale: this.supervisor_recruitment.recruitment_teams_rating_scale,
                                    recruitment_teams_comments: this.supervisor_recruitment.recruitment_teams_comments,
                                    total_score: this.supervisor_recruitment.total_score,
                                    development_plan: this.supervisor_recruitment.development_plan,
                                    weaknesses: this.supervisor_recruitment.weaknesses,
                                    strengths: this.supervisor_recruitment.strengths,
                                    managers_feedback: this.supervisor_recruitment.managers_feedback,
                                    employees_acknowledge: this.supervisor_recruitment.employees_acknowledge,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        } else if (this.coaching_log.selectedForm.value === 14) {
                            this.$constants.Coaching_API.get('/added_form', {
                                params: {
                                    form_type: response.data.form_type,
                                    cl_number: response.data.cl_number,
                                    self_thinking_target: this.supervisor_hrbp.self_thinking_target,
                                    self_thinking_weight: this.supervisor_hrbp.self_thinking_weight,
                                    self_thinking_rating_scale: this.supervisor_hrbp.self_thinking_rating_scale,
                                    self_thinking_final_score: this.supervisor_hrbp.self_thinking_final_score,
                                    initiative_target: this.supervisor_hrbp.initiative_target,
                                    initiative_weight: this.supervisor_hrbp.initiative_weight,
                                    initiative_rating_scale: this.supervisor_hrbp.initiative_rating_scale,
                                    initiative_final_score: this.supervisor_hrbp.initiative_final_score,
                                    work_ethics_target: this.supervisor_hrbp.work_ethics_target,
                                    work_ethics_weight: this.supervisor_hrbp.work_ethics_weight,
                                    work_ethics_rating_scale: this.supervisor_hrbp.work_ethics_rating_scale,
                                    work_ethics_final_score: this.supervisor_hrbp.work_ethics_final_score,
                                    responsiveness_target: this.supervisor_hrbp.responsiveness_target,
                                    responsiveness_weight: this.supervisor_hrbp.responsiveness_weight,
                                    responsiveness_rating_scale: this.supervisor_hrbp.responsiveness_rating_scale,
                                    responsiveness_final_score: this.supervisor_hrbp.responsiveness_final_score,
                                    attendance_target: this.supervisor_hrbp.attendance_target,
                                    attendance_weight: this.supervisor_hrbp.attendance_weight,
                                    attendance_rating_scale: this.supervisor_hrbp.attendance_rating_scale,
                                    attendance_final_score: this.supervisor_hrbp.attendance_final_score,
                                    data_target: this.supervisor_hrbp.data_target,
                                    data_weight: this.supervisor_hrbp.data_weight,
                                    data_rating_scale: this.supervisor_hrbp.data_rating_scale,
                                    data_final_score: this.supervisor_hrbp.data_final_score,
                                    intervention_target: this.supervisor_hrbp.intervention_target,
                                    intervention_weight: this.supervisor_hrbp.intervention_weight,
                                    intervention_rating_scale: this.supervisor_hrbp.intervention_rating_scale,
                                    intervention_final_score: this.supervisor_hrbp.intervention_final_score,
                                    cluster_target: this.supervisor_hrbp.cluster_target,
                                    cluster_weight: this.supervisor_hrbp.cluster_weight,
                                    cluster_rating_scale: this.supervisor_hrbp.cluster_rating_scale,
                                    cluster_final_score: this.supervisor_hrbp.cluster_final_score,
                                    total_score: this.supervisor_hrbp.total_score,
                                    commendation: this.supervisor_hrbp.commendation,
                                    action_plans: this.supervisor_hrbp.action_plans,
                                },
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        }
                    }
                    if (this.attachment_image.length > 0) {
                        for (let value of this.attachment_image) {
                            this.fileImageData.append('attachments_image[]', value);
                        }
                        if (response.data.cl_number) {
                            this.$constants.Coaching_API.post('/image_attach/'+response.data.cl_number, this.fileImageData)
                            .then(response => {
                                $('.ladda-button').attr("disabled", false);
                                this.$swal({
                                    html: 'Successfully added',
                                    type: "success",
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result) {
                                        this.$bus.$emit('init_modal', false);
                                        window.location.href = '/employee/coachinglog'
                                    }
                                })
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        }
                    } else {
                        $('.ladda-button').attr("disabled", false);
                        this.$swal({
                            html: 'Successfully added',
                            type: "success",
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result) {
                                this.$bus.$emit('init_modal', false);
                                window.location.href = '/employee/coachinglog'
                            }
                        })
                    }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            dateRangeValidation(start, end){
                if (end < start) {
                    this.$swal({
                        text: "Date of Incident is not valid range",
                        type: "error",
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        this.coaching_log.date_start = '';
                        this.coaching_log.date_end = '';
                    });
                }
            },
            addOffense: function () {
                this.showProvisionMultiple = true;
                this.provision_multiple.push({
                    category_multi: '',
                    offense_multi: '',
                    offense_description_multi: '',
                    gravity_multi: '',
                 });
                if (this.provision_multiple[0].category_multi.value != "") {
                    this.categoryDisabled = true;
                    this.offenseDisabled = true;
                }
                this.showAddOffense = false;
            },
            deleteOffense: function (index, offense) {
                this.offense_remove.splice(this.offense_remove.indexOf(offense.value), 1);
                this.provision_multiple.splice(index, 1);
                if (index == 0) {
                    this.categoryDisabled = false;
                    this.offenseDisabled = false;
                    this.showAddOffense = true
                }
                if(index < 0)
                this.addOffense()
            },
            deleteDropdownSelectedOffense: function (index) {
                this.selectedOffense.splice(index, 1);
                const valuesArr = this.selectedOffense;
                const removeValFromIndex = this.offense_index;
                const indexSet = new Set(removeValFromIndex);
                const arrayWithValuesRemoved = valuesArr.filter((value, i) => !indexSet.has(i));
                this.selectedOffense = arrayWithValuesRemoved;
            },
            eventOffenseMultiple(index){
                if(this.provision_multiple[index].category_multi.value != undefined){
                    if(this.provision_multiple[index].category_multi.value == 15){
                        this.provision_multiple[index].offense_multi = '';
                        this.provision_multiple[index].offense_description_multi = '';
                        this.provision_multiple[index].gravity_multi = '';
                        this.offenseMultipleDisabled = true;
                        this.showOffenseMultiple = false;
                        this.showGravityMultiple = false;
                        this.showDescriptionMultiple = false;
                        /* TODO: multiple attendance points error*/
                        // this.dropdownInfraction();
                    } else {
                        this.offenseMultipleDisabled = false;
                        this.provision_multiple[index].offense_multi = '';
                        this.provision_multiple[index].offense_description_multi = '';
                        this.provision_multiple[index].gravity_multi = '';
                        this.showOffenseMultiple = true;
                        this.showGravityMultiple = true;
                        this.showDescriptionMultiple = true;
                        this.dropdownOffenseCategory(
                            this.provision_multiple[index].category_multi.value,
                            this.offense_remove,
                            index
                        );
                    }
                }
            },
            eventCocMultiple(index){
                this.showAddOffense = true;
                this.showDeleteOffense = true
                if(this.provision_multiple[index].offense_multi.value != undefined){
                    this.offense_remove[index+1] = this.provision_multiple[index].offense_multi.value;
                    this.$constants.Coc_API.get("/offense/codeofconduct/multiple/"
                    +this.provision_multiple[index].offense_multi.value+"/"
                    +this.offense_remove)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.dropdownOffenseCategory(this.provision_multiple[index].category_multi.value, this.offense_remove);
                        this.provision_multiple[index].offense_description_multi = response.data.description
                        this.provision_multiple[index].gravity_multi = response.data.gravity.gravity
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            eventCoc(){
                if(this.coaching_log.offense.value != undefined){
                    this.showAddOffense = true
                    this.offense_remove[0] = this.coaching_log.offense.value;
                    this.$constants.Coc_API.get("/offense/codeofconduct/"+this.coaching_log.offense.value)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.coaching_log.offense_description = this.selectedCoc.offense_description;
                        this.coaching_log.gravity = this.selectedCoc.gravity.gravity;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
        },
    }
</script>
