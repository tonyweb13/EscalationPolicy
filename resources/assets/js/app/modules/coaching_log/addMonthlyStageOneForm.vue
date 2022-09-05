<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)"> 
            <div slot="form-body">
                <h3 class="header-tab-cl">Stage One Form</h3>
                <the-label label="Date of Incident" :asterisk="true" class="m-t-lg" style="margin-bottom: 80px;">
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

                            <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
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
                            <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </div>
                </the-label>
                <the-label label="Number of Occurrences">
                    <input type='text' id="occurrences" class="form-control" v-model="coaching_log.number_occurrence" />
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
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>
                <the-label label="Description" :asterisk="true" v-if="showOffense">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="coaching_log.offense"
                            :options="selectedOffense"
                            label="text"
                            :disabled="offenseDisabled"
                            @input="eventCoc"
                        />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>
                <the-label label="Description:" v-if="showOthers" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="others" rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.others" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>
                <the-label label="Description:" v-if="showPerformanceReview" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="others" rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.performance_review" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label v-if="coaching_log.offense_description" label="Description:" v-show="showDescription">
                    <div style="background: #eee; padding: 10px;" v-html="coaching_log.offense_description"/>
                </the-label>

                <the-label v-if="coaching_log.gravity" label="Gravity:" v-show="showGravity">
                    <div style="background: #eee; padding: 10px;" v-html="coaching_log.gravity"/>
                </the-label>

                <!-- start multiple -->
                <div v-for="(multiple_off, index) in provision_multiple">
                    <fieldset class="fieldsetCustom">
                    <legend class="legendCustom">
                    Offense ( {{ offenseCnt == index ? 2 : indexCnt+index }} )
                    </legend>
                    <!-- multiple_off.category_multi == {{ multiple_off.category_multi.value }} -->
                    <the-label label="Classification of Coaching" v-show="showProvisionMultiple" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <vSelect
                                v-model="multiple_off.category_multi"
                                :options="selectedCategory"
                                label="text"
                                @input="eventOffenseMultiple(index)"
                            />
                            <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
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
                            <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                        </validation-provider>
                    </the-label>

                    <the-label v-if="multiple_off.offense_description_multi" label="Description:" v-show="showDescriptionMultiple">
                        <div style="background: #eee; padding: 10px;" v-html="multiple_off.offense_description_multi"/>
                    </the-label>

                    <the-label v-if="multiple_off.gravity_multi" label="Gravity:" v-show="showGravityMultiple">
                        <div style="background: #eee; padding: 10px;" v-html="multiple_off.gravity_multi"/>
                    </the-label>

                    <div class="pull-left" v-if="showDeleteOffense">
                        <button type="button" @click="deleteOffense(index, multiple_off.offense_multi)" class="btn btn-danger">
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

                <the-label label="Findings / Coaching Opportunities/Areas of Improvement" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="findings" rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.findings" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Point of Discussion/ Coaching Details" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="point_of_disscussion" rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.point_of_disscussion" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Action Plans" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="action_plans" rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.action_plans" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Employee’s Commitment" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="agent_commitment" rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.agents_commitment" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Supervisor’s Commitment" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea rows="4" cols="104.5" style="width:100%;" v-model="coaching_log.supervisors_commitment" />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
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
                <table class="table-cl-monthly">
                    <tr>
                        <td class="td-cl-monthly" style="width: 30%;">Key Performance Indicators</td>
                        <td class="td-cl-monthly" style="width: 8%;">Target</td>
                        <td class="td-cl-monthly" style="width: 5%;">Weight</td>
                        <td class="td-cl-monthly" style="width: 25%;" >Rating</td>
                        <td class="td-cl-monthly">Computation (Rating x %)</td>
                        <td class="td-cl-monthly">Comments</td>
                    </tr>
                    <tr>
                        <th class="th-cl-monthly"  colspan="6">Loan Improvement</th>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  style="text-align: left !important;">
                            <span v-show="keyPerformance.showTextFirst" >
                            5 - <span> {{ keyPerformance.loanIndicator.fifthIndicator }} </span><br>
                            4 - <span> {{ keyPerformance.loanIndicator.fourthIndicator }} </span><br>
                            3 - <span> {{ keyPerformance.loanIndicator.thirdIndicator }} </span><br>
                            2 - <span> {{ keyPerformance.loanIndicator.secondIndicator }} </span><br>
                            1 - <span> {{ keyPerformance.loanIndicator.firstIndicator }} </span><br>
                            <button class="btn btn-sm btn-danger" @click="editKeyPerformance(1)">
                                <i class="fa fa-pencil-square"/> Edit
                            </button>
                            </span>
                            <span v-show="keyPerformance.showInputFirst">
                            5 - <input type="text" v-model="keyPerformance.loanIndicator.fifthIndicator"
                            style="width: 90%;"><br>
                            4 - <input type="text" v-model="keyPerformance.loanIndicator.fourthIndicator"
                            style="width: 90%;"><br>
                            3 - <input type="text" v-model="keyPerformance.loanIndicator.thirdIndicator"
                            style="width: 90%;"><br>
                            2 - <input type="text" v-model="keyPerformance.loanIndicator.secondIndicator"
                            style="width: 90%;"><br>
                            1 - <input type="text" v-model="keyPerformance.loanIndicator.firstIndicator"
                            style="width: 90%;"><br>
                            <button class="btn btn-sm btn-warning" @click="updateKeyPerformance(1)">
                                <i class="fa fa-save"/> Save Changes
                            </button>
                            </span>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.loan_amount_target"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">20%</td>
                        <td class="td-cl-monthly">
                            <table class="table table-bordered"> 
                                <tr>
                                    <th scope="col" style="text-align: center;">EOM Rating</th>
                                    <th scope="col" style="text-align: center;">W2</th>
                                    <th scope="col" style="text-align: center;">W3</th>
                                    <th scope="col" style="text-align: center;">W4</th>
                                </tr>
                                <tr>
                                    <td>
                                        <validation-provider rules="required" v-slot="{ errors }">
                                            <input
                                                type='number'
                                                class="form-control"
                                                v-model="stage_one.loan_amount_rating_eom"
                                            />
                                            <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                        </validation-provider>
                                    </td>
                                    <td>
                                        <validation-provider rules="required" v-slot="{ errors }">
                                            <input
                                                type='number'
                                                class="form-control"
                                                v-model="stage_one.loan_amount_rating_w2"
                                            />
                                            <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                        </validation-provider>
                                    </td>
                                    <td>
                                        <validation-provider rules="required" v-slot="{ errors }">
                                            <input
                                                type='number'
                                                class="form-control"
                                                v-model="stage_one.loan_amount_rating_w3"
                                            />
                                            <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                        </validation-provider>
                                    </td>
                                    <td>
                                        <validation-provider rules="required" v-slot="{ errors }">
                                            <input
                                                type='number'
                                                class="form-control"
                                                v-model="stage_one.loan_amount_rating_w4"
                                            />
                                            <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                        </validation-provider>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.loan_amount_computation" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                           <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.loan_amount_comment" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                    <tr>
                        <th class="th-cl-monthly"  colspan="6">Knowledge and Communication Assessment</th>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  style="text-align: left !important;">
                            <span v-show="keyPerformance.showTextSecond" >
                            5 - <span> {{ keyPerformance.knowledgeIndicator.fifthIndicator }} </span><br>
                            4 - <span> {{ keyPerformance.knowledgeIndicator.fourthIndicator }} </span><br>
                            3 - <span> {{ keyPerformance.knowledgeIndicator.thirdIndicator }} </span><br>
                            2 - <span> {{ keyPerformance.knowledgeIndicator.secondIndicator }} </span><br>
                            1 - <span> {{ keyPerformance.knowledgeIndicator.firstIndicator }} </span><br>
                            <button class="btn btn-sm btn-danger" @click="editKeyPerformance(2)">
                                <i class="fa fa-pencil-square"/> Edit
                            </button>
                            </span>
                            <span v-show="keyPerformance.showInputSecond">
                            5 - <input type="text" v-model="keyPerformance.knowledgeIndicator.fifthIndicator"
                            style="width: 90%;"><br>
                            4 - <input type="text" v-model="keyPerformance.knowledgeIndicator.fourthIndicator"
                            style="width: 90%;"><br>
                            3 - <input type="text" v-model="keyPerformance.knowledgeIndicator.thirdIndicator"
                            style="width: 90%;"><br>
                            2 - <input type="text" v-model="keyPerformance.knowledgeIndicator.secondIndicator"
                            style="width: 90%;"><br>
                            1 - <input type="text" v-model="keyPerformance.knowledgeIndicator.firstIndicator"
                            style="width: 90%;"><br>
                            <button class="btn btn-sm btn-warning" @click="updateKeyPerformance(2)">
                                <i class="fa fa-save"/> Save Changes
                            </button>
                            </span>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.knowledge_target"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">30%</td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.knowledge_rating"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.knowledge_computation" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.knowledge_comment" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                    <tr>
                        <th class="th-cl-monthly"  colspan="6">QA Score</th>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  style="text-align: left !important;">
                            <span v-show="keyPerformance.showTextThird" >
                            5 - <span> {{ keyPerformance.qaIndicator.fifthIndicator }} </span><br>
                            4 - <span> {{ keyPerformance.qaIndicator.fourthIndicator }} </span><br>
                            3 - <span> {{ keyPerformance.qaIndicator.thirdIndicator }} </span><br>
                            2 - <span> {{ keyPerformance.qaIndicator.secondIndicator }} </span><br>
                            1 - <span> {{ keyPerformance.qaIndicator.firstIndicator }} </span><br>
                            <button class="btn btn-sm btn-danger" @click="editKeyPerformance(3)">
                                <i class="fa fa-pencil-square"/> Edit
                            </button>
                            </span>
                            <span v-show="keyPerformance.showInputThird">
                            5 - <input type="text" v-model="keyPerformance.qaIndicator.fifthIndicator"
                            style="width: 90%;"><br>
                            4 - <input type="text" v-model="keyPerformance.qaIndicator.fourthIndicator"
                            style="width: 90%;"><br>
                            3 - <input type="text" v-model="keyPerformance.qaIndicator.thirdIndicator"
                            style="width: 90%;"><br>
                            2 - <input type="text" v-model="keyPerformance.qaIndicator.secondIndicator"
                            style="width: 90%;"><br>
                            1 - <input type="text" v-model="keyPerformance.qaIndicator.firstIndicator"
                            style="width: 90%;"><br>
                            <button class="btn btn-sm btn-warning" @click="updateKeyPerformance(3)">
                                <i class="fa fa-save"/> Save Changes
                            </button>
                            </span>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.qa_score_target"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">10%</td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.qa_score_rating"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.qa_score_computation" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.qa_score_comment" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                    <tr>
                        <th class="th-cl-monthly"  colspan="6">Coaching Logs</th>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  style="text-align: left !important;">
                            <span v-show="keyPerformance.showTextFourth" >
                            5 - <span> {{ keyPerformance.coachingIndicator.fifthIndicator }} </span><br>
                            4 - <span> {{ keyPerformance.coachingIndicator.fourthIndicator }} </span><br>
                            3 - <span> {{ keyPerformance.coachingIndicator.thirdIndicator }} </span><br>
                            2 - <span> {{ keyPerformance.coachingIndicator.secondIndicator }} </span><br>
                            1 - <span> {{ keyPerformance.coachingIndicator.firstIndicator }} </span><br>
                            <button class="btn btn-sm btn-danger" @click="editKeyPerformance(4)">
                                <i class="fa fa-pencil-square"/> Edit
                            </button>
                            </span>
                            <span v-show="keyPerformance.showInputFourth">
                            5 - <input type="text" v-model="keyPerformance.coachingIndicator.fifthIndicator"
                            style="width: 90%;"><br>
                            4 - <input type="text" v-model="keyPerformance.coachingIndicator.fourthIndicator"
                            style="width: 90%;"><br>
                            3 - <input type="text" v-model="keyPerformance.coachingIndicator.thirdIndicator"
                            style="width: 90%;"><br>
                            2 - <input type="text" v-model="keyPerformance.coachingIndicator.secondIndicator"
                            style="width: 90%;"><br>
                            1 - <input type="text" v-model="keyPerformance.coachingIndicator.firstIndicator"
                            style="width: 90%;"><br>
                            <button class="btn btn-sm btn-warning" @click="updateKeyPerformance(4)">
                                <i class="fa fa-save"/> Save Changes
                            </button>
                            </span>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.coaching_log_target"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">20%</td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.coaching_log_rating"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.coaching_log_computation" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.coaching_log_comment" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                    <tr>
                        <th class="th-cl-monthly"  colspan="6">Attendance Reliability</th>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  style="text-align: left !important;">
                            <span v-show="keyPerformance.showTextFifth" >
                            5 - <span> {{ keyPerformance.attendanceIndicator.fifthIndicator }} </span><br>
                            4 - <span> {{ keyPerformance.attendanceIndicator.fourthIndicator }} </span><br>
                            3 - <span> {{ keyPerformance.attendanceIndicator.thirdIndicator }} </span><br>
                            2 - <span> {{ keyPerformance.attendanceIndicator.secondIndicator }} </span><br>
                            1 - <span> {{ keyPerformance.attendanceIndicator.firstIndicator }} </span><br>
                            <button class="btn btn-sm btn-danger" @click="editKeyPerformance(5)">
                                <i class="fa fa-pencil-square"/> Edit
                            </button>
                            </span>
                            <span v-show="keyPerformance.showInputFifth">
                            5 - <input type="text" v-model="keyPerformance.attendanceIndicator.fifthIndicator"
                            style="width: 90%;"><br>
                            4 - <input type="text" v-model="keyPerformance.attendanceIndicator.fourthIndicator"
                            style="width: 90%;"><br>
                            3 - <input type="text" v-model="keyPerformance.attendanceIndicator.thirdIndicator"
                            style="width: 90%;"><br>
                            2 - <input type="text" v-model="keyPerformance.attendanceIndicator.secondIndicator"
                            style="width: 90%;"><br>
                            1 - <input type="text" v-model="keyPerformance.attendanceIndicator.firstIndicator"
                            style="width: 90%;"><br>
                            <button class="btn btn-sm btn-warning" @click="updateKeyPerformance(5)">
                                <i class="fa fa-save"/> Save Changes
                            </button>
                            </span>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.attendance_reliability_target"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">20%</td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input
                                    type='number'
                                    class="form-control"
                                    v-model="stage_one.attendance_reliability_rating"
                                    placeholder="Type here....."
                                />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.attendance_reliability_computation" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.attendance_reliability_comment" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  colspan="6" style="text-align: left !important;">II. DEVELOPMENT PLAN</td>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  colspan="3">STRENGTHS</td>
                        <td class="td-cl-monthly"  colspan="3">WEAKNESSES</td>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  colspan="3">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.strengths" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                        <td class="td-cl-monthly"  colspan="3">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.weakness" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly" colspan="6">ACTION PLAN</td>
                    </tr>
                    <tr>
                        <td class="td-cl-monthly"  colspan="6">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <textarea rows="4" cols="104.5" style="width:100%;" v-model="stage_one.action_plans" placeholder="Type here....." />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                    </tr>
                </table>
                <br>
                <br>
                <button-save class="ladda-button btn btn-primary"/>
            </div>
        </the-form>
    </ValidationObserver>
</template>
<style>
    .pull-left {
        float: left !important;
    }
    .pull-right {
        float: right !important;
    }
    .center-signature {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: justify;
        text-align: center;
        page-break-after:avoid;
    }
    .title {
        width: 100%;
        text-align:center;
    }
    .content {
        width: 100%;
        z-index: 100;
    }
    .plain {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: justify;
        page-break-after:avoid;
    }
    hr.divider {
        border: 1px solid black;
        margin-bottom: 20px;
    }
    .table-border {
        page-break-after:avoid;
        border: 1px solid black;
        border-collapse: collapse;
        width:100%
    }
    .table-cl-monthly {
        border-collapse: collapse;
        width: 100%;
        font-weight: bold;
        text-align: center;
    }

    .table-cl-monthly, .td-cl-monthly, .th-cl-monthly {
        border: 1px solid black;
    }

    .th-cl-monthly, .td-cl-monthly {
        text-align: center;
        font-size: 12px
    }

    .th-cl-monthly {
    	background-color: #1F4788;
    	color: white;
    	font-weight: normal;
    }

    .shaded {
    	background-color: #BDC3C7;
    }

    .no-bottom-border {
        border-bottom: none;
    }

    .no-top-border {
        border-top: none;
    }

    .no-left-border {
        border-left: none;
    }

    .no-right-border {
        border-right: none;
    }
    .left, .right {
        position: absolute;
        width: 30px;
        height: 20px;
        top: 0;
        bottom: 0;
        margin: auto;
    }
    .left {
        left: 0;
        margin-left: -15px;
    }
    .right {
        right: 0;
        margin-right: -15px;
    }
    .text-signed {
        font-size: 15px;
    }
    .offenseTable {
        padding: 10px;
    }
    .table-row {
        display: table-row;
    }
    .table-cell {
        display: table-cell;
        height: 30px;
        border: 1px solid black;
        border-top: 0px !important;
    }
    .table-cell-first {
        display: table-cell;
        border-bottom: 1px solid black;
        border-right: 1px solid black;
    }
    .table-cell-last {
        display: table-cell;
        border-bottom: 1px solid black;
        border-left: 1px solid black;
    }
    .table-cell-max {
        display: table-cell;
        border-right: 1px solid black;
        border-left: 1px solid black;
        border-bottom: 0px solid black;
    }
    .table-cell-max-last {
        display: table-cell;
        border-bottom: 0px solid black;
        border-left: 1px solid black;
    }
    .table-cell-max-first {
        display: table-cell;
        border-right: 1px solid black;
        border-bottom: 0px solid black;
    }

    .imgdiv {
        position: absolute;
    }
    .clipzone2 {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .clipzone2 img {
        width: 100%;
        margin: -650px 0 0 0;
    }
    .clipzone
    {
        position:relative;
        height:650px;
        overflow:hidden;
    }
    .style-red-cl {
        color: red;
        font-size: xx-small;
    }
    .input-number-cl {
        width: 35%;
    }
    input:placeholder-shown, textarea:placeholder-shown {
        font-style: italic;
        font-size: smaller;
    }
    .header-tab-cl {
        text-align: center; 
        background-color: #1F4788;
        color: white;
        padding-top: 5px;
        padding-bottom: 5px;
    }
</style>
<script>
    export default {
        props: {
            employee_no: String,
            coachingLogProps: Object,
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
                selectedCategory: [],
                selectedOffense: [],
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
                keyPerformance: {
                    showInputFirst: false,
                    showTextFirst: true,
                    loanIndicator: {
                        fifthIndicator: "",
                        fourthIndicator: "",
                        thirdIndicator: "",
                        secondIndicator: "", 
                        firstIndicator: "",
                    },
                    showInputSecond: false,
                    showTextSecond: true,
                    knowledgeIndicator: {
                        fifthIndicator: "",
                        fourthIndicator: "",
                        thirdIndicator: "",
                        secondIndicator: "", 
                        firstIndicator: "",
                    },
                    showInputThird: false,
                    showTextThird: true,
                    qaIndicator: {
                        fifthIndicator: "",
                        fourthIndicator: "",
                        thirdIndicator: "",
                        secondIndicator: "", 
                        firstIndicator: "",
                    },
                    showInputFourth: false,
                    showTextFourth: true,
                    coachingIndicator: {
                        fifthIndicator: "",
                        fourthIndicator: "",
                        thirdIndicator: "",
                        secondIndicator: "", 
                        firstIndicator: "",
                    },
                    showInputFifth: false,
                    showTextFifth: true,
                    attendanceIndicator: {
                        fifthIndicator: "",
                        fourthIndicator: "",
                        thirdIndicator: "",
                        secondIndicator: "", 
                        firstIndicator: "",
                    },
                }
            };
        },

        created() {   
            this.keyPerformanceIndicator();         
            this.eventCategory();
            this.eventOffense();
            let today = new Date()
            let tomorrow = new Date(today)
            tomorrow.setDate(tomorrow.getDate() + 1)
            this.maxDate = {
              disabledDates: {
                from: tomorrow,
              }
            }

            // if (this.coachingLogProps) {
            //     console.log("stage1 edit coachingLogProps==", this.coachingLogProps);
            // } else {
            //     console.log("stage1 employee_no==", this.employee_no);
                this.coaching_log.respondent = '';
                this.coaching_log.category = null;
                this.coaching_log.offense = null;
                this.coaching_log.gravity = '';
                this.coaching_log.offense_description = '';
                this.coaching_log.attachment_type = { "value": 0, "text": "Others" };
                this.coaching_log.attachment_type = { "value": 100, "text": "Performance Review" };
            // }
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
                /* default monthly value for stage one form */
                this.coaching_log.selectedForm = {
                    "text": "Stage One Form",
                    "value": 0
                };

                if (this.coaching_log.offense) {
                    this.coaching_log.offense_multiple.push(this.coaching_log.offense.value);
                }

                if (this.provision_multiple.length> 0) {
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
                } else if (this.coaching_log.offense_multiple.length> 1) {
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
                    }
                    if (this.attachment_image.length> 0) {
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
                                });
                                this.$bus.$emit('init_modal', false);
                                this.$bus.$emit('updateList');
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
                        });
                        this.$bus.$emit('init_modal', false);
                        window.location.href = '/employee/coachinglog'
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
            keyPerformanceIndicator() {
                this.$constants.Coaching_API.get("/keyperformance/indicator/1")
                .then(response => {
                    this.keyPerformance.loanIndicator.fifthIndicator = response.data.fifth_indicator;
                    this.keyPerformance.loanIndicator.fourthIndicator = response.data.fourth_indicator;
                    this.keyPerformance.loanIndicator.thirdIndicator = response.data.third_indicator;
                    this.keyPerformance.loanIndicator.secondIndicator = response.data.second_indicator;
                    this.keyPerformance.loanIndicator.firstIndicator = response.data.first_indicator;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

                this.$constants.Coaching_API.get("/keyperformance/indicator/2")
                .then(response => {
                    this.keyPerformance.knowledgeIndicator.fifthIndicator = response.data.fifth_indicator;
                    this.keyPerformance.knowledgeIndicator.fourthIndicator = response.data.fourth_indicator;
                    this.keyPerformance.knowledgeIndicator.thirdIndicator = response.data.third_indicator;
                    this.keyPerformance.knowledgeIndicator.secondIndicator = response.data.second_indicator;
                    this.keyPerformance.knowledgeIndicator.firstIndicator = response.data.first_indicator;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

                this.$constants.Coaching_API.get("/keyperformance/indicator/3")
                .then(response => {
                    this.keyPerformance.qaIndicator.fifthIndicator = response.data.fifth_indicator;
                    this.keyPerformance.qaIndicator.fourthIndicator = response.data.fourth_indicator;
                    this.keyPerformance.qaIndicator.thirdIndicator = response.data.third_indicator;
                    this.keyPerformance.qaIndicator.secondIndicator = response.data.second_indicator;
                    this.keyPerformance.qaIndicator.firstIndicator = response.data.first_indicator;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

                this.$constants.Coaching_API.get("/keyperformance/indicator/4")
                .then(response => {
                    this.keyPerformance.coachingIndicator.fifthIndicator = response.data.fifth_indicator;
                    this.keyPerformance.coachingIndicator.fourthIndicator = response.data.fourth_indicator;
                    this.keyPerformance.coachingIndicator.thirdIndicator = response.data.third_indicator;
                    this.keyPerformance.coachingIndicator.secondIndicator = response.data.second_indicator;
                    this.keyPerformance.coachingIndicator.firstIndicator = response.data.first_indicator;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

                this.$constants.Coaching_API.get("/keyperformance/indicator/5")
                .then(response => {
                    this.keyPerformance.attendanceIndicator.fifthIndicator = response.data.fifth_indicator;
                    this.keyPerformance.attendanceIndicator.fourthIndicator = response.data.fourth_indicator;
                    this.keyPerformance.attendanceIndicator.thirdIndicator = response.data.third_indicator;
                    this.keyPerformance.attendanceIndicator.secondIndicator = response.data.second_indicator;
                    this.keyPerformance.attendanceIndicator.firstIndicator = response.data.first_indicator;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            editKeyPerformance(keyPer){
                if (keyPer == 1) {
                    this.keyPerformance.showTextFirst = false
                    this.keyPerformance.showInputFirst = true

                } else if (keyPer == 2) {
                    this.keyPerformance.showTextSecond = false
                    this.keyPerformance.showInputSecond = true

                } else if (keyPer == 3) {
                    this.keyPerformance.showTextThird = false
                    this.keyPerformance.showInputThird = true

                } else if (keyPer == 4) {
                    this.keyPerformance.showTextFourth = false
                    this.keyPerformance.showInputFourth = true

                } else if (keyPer == 5) {
                    this.keyPerformance.showTextFifth = false
                    this.keyPerformance.showInputFifth = true
                }
            },
            updateKeyPerformance(keyPer){
                let updateIndicator = ''
                if (keyPer == 1) {
                    updateIndicator = this.keyPerformance.loanIndicator;

                } else if (keyPer == 2) {
                    updateIndicator = this.keyPerformance.knowledgeIndicator;

                } else if (keyPer == 3) {
                    updateIndicator = this.keyPerformance.qaIndicator;

                } else if (keyPer == 4) {
                    updateIndicator = this.keyPerformance.coachingIndicator;

                } else if (keyPer == 5) {
                    updateIndicator = this.keyPerformance.attendanceIndicator;

                }

                this.$constants.Coaching_API.get("/keyperformance/indicator/update/" + keyPer, {
                    params: {
                        fifth_indicator: updateIndicator.fifthIndicator,
                        fourth_indicator: updateIndicator.fourthIndicator,
                        third_indicator: updateIndicator.thirdIndicator,
                        second_indicator: updateIndicator.secondIndicator,
                        first_indicator: updateIndicator.firstIndicator,
                    }
                }).then(response => {
                    this.$bus.$emit('updateKeyPerformance1List');
                    this.$bus.$on('updateKeyPerformance1List', this.keyPerformanceIndicator);
                    this.keyPerformance.showInputFirst = false
                    this.keyPerformance.showTextFirst = true

                    this.keyPerformance.showInputSecond = false
                    this.keyPerformance.showTextSecond = true

                    this.keyPerformance.showInputThird = false
                    this.keyPerformance.showTextThird = true

                    this.keyPerformance.showInputFourth = false
                    this.keyPerformance.showTextFourth = true

                    this.keyPerformance.showInputFifth = false
                    this.keyPerformance.showTextFifth = true

                    this.$success('Key Performance Indicator Successfully Saved!')
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
