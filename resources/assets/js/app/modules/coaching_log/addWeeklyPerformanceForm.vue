<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <table class="table-coaching-log">
                    <tr>
                         <th class="th-cl" colspan="2">Target KPI/s</th>
                             <td class="td-cl" colspan="10" style="text-align:left; padding-left:10px;">Loan Origination</td>
                    </tr>
                    <tr>
                         <th class="th-cl" colspan="2">Identified Behavior/s <span class="required_fields">*</span></th>
                             <td class="td-cl" colspan="10">
                                <validation-provider rules="required" v-slot="{ errors }"> 
                                    <textarea class="form-control" style="width:400%;" placeholder="Type here....." 
                                    v-model="weekly_performance.identified_behavior"/>
                                    <div v-if="errors[0]" class="style-red-cl" style="height: 200px;">{{ errors[0] }}</div>
                                </validation-provider>
                            </td>
                    </tr>
                    <tr>
                         <th class="th-cl" colspan="2">Root Cause/s <span class="required_fields">*</span></th>
                             <td class="td-cl" colspan="10">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <textarea class="form-control" style="width:400%" placeholder="Type here....." 
                                    v-model="weekly_performance.root_cause" />
                                    <div v-if="errors[0]" class="style-red-cl" style="height: 200px;">{{ errors[0] }}</div>
                                </validation-provider>
                            </td>
                    </tr>
                    <tr>
                         <th class="th-cl" colspan="2" style="height:40px;">Action Plan/s<span class="required_fields">*</span></th>
                             <td class="td-cl" colspan="10" style="height:40px;">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <textarea class="form-control" style="width:400%" placeholder="Type here....." 
                                    v-model="weekly_performance.action_plans" />
                                    <div v-if="errors[0]" class="style-red-cl" style="height: 200px;">{{ errors[0] }}</div>
                                </validation-provider>
                            </td>
                    </tr>
                    <tr>
                         <th class="th-cl" colspan="2">Monthly Goal<span class="required_fields">*</span></th>
                         <td class="td-cl" colspan="3">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='text' class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.base_performance" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <th class="th-cl" colspan="2">Goal<span class="required_fields">*</span></th>
                             <td class="td-cl" colspan="5">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control" style="width: 190%;"  placeholder="Type here....."  
                                    v-model="weekly_performance.goal" />
                                    <div class="style-red-cl" style="height: 15px; margin-bottom: 50px;" v-if="errors[0]" >{{ errors[0] }}</div>
                                </validation-provider>
                            </td>
                    </tr>
                    <tr>
                         <th class="th-cl" rowspan="12" style="width: 7%;">Weekly Goals</th>
                         <th class="th-cl" rowspan="2">KPI</th>
                         <th class="th-cl" colspan="2">Week 1 <span class="required_fields">*</span></th>
                         <th class="th-cl" colspan="2">Week 2</th>
                         <th class="th-cl" colspan="2">Week 3</th>
                         <th class="th-cl" colspan="2">Week 4</th>
                         <th class="th-cl" colspan="2">Week 5</th>
                    </tr>
                    <tr>
                        <th class="th-cl">Target <span class="required_fields">*</span></th>
                        <th class="th-cl" >Actual <span class="required_fields">*</span></th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>
                    </tr>
                    <tr>
                        <th class="th-cl" >Loan Origination</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week1.target.loan_origination" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                v-model="weekly_performance.week1.actual.loan_origination" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td  class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week2.target.loan_origination" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                            v-model="weekly_performance.week2.actual.loan_origination" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week3.target.loan_origination" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                            v-model="weekly_performance.week3.actual.loan_origination" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week4.target.loan_origination" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                            v-model="weekly_performance.week4.actual.loan_origination" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week5.target.loan_origination" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                            v-model="weekly_performance.week5.actual.loan_origination" />
                        </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >QA Scores</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week1.target.qa_scores" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                v-model="weekly_performance.week1.actual.qa_scores" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week2.target.qa_scores" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....."
                            v-model="weekly_performance.week2.actual.qa_scores" />
                        </td>

                        <td class="shaded">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week3.target.qa_scores" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....."
                            v-model="weekly_performance.week3.actual.qa_scores" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week4.target.qa_scores" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....."
                            v-model="weekly_performance.week4.actual.qa_scores" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week5.target.qa_scores" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....."
                            v-model="weekly_performance.week5.actual.qa_scores" />
                        </td>

                    </tr>

                    <tr>
                        <th class="th-cl" >Compliance</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week1.target.compliance" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control"  placeholder="Type here....." 
                                v-model="weekly_performance.week1.actual.compliance" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week2.target.compliance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....." 
                            v-model="weekly_performance.week2.actual.compliance" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week3.target.compliance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....." 
                            v-model="weekly_performance.week3.actual.compliance" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week4.target.compliance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....." 
                            v-model="weekly_performance.week4.actual.compliance" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week5.target.compliance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control"  placeholder="Type here....." 
                            v-model="weekly_performance.week5.actual.compliance" />
                        </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >Attendance</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week1.target.attendance" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week1.actual.attendance" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week2.target.attendance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                            v-model="weekly_performance.week2.actual.attendance" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week3.target.attendance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                            v-model="weekly_performance.week3.actual.attendance" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week4.target.attendance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                            v-model="weekly_performance.week4.actual.attendance" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                            v-model="weekly_performance.week5.target.attendance" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                            v-model="weekly_performance.week5.actual.attendance" />
                        </td>
                    </tr>

                    <tr>
                         <th class="th-cl" rowspan="2">Efficiency</th>
                         <th class="th-cl" colspan="2">Week 1 <span class="required_fields">*</span></th>
                         <th class="th-cl" colspan="2">Week 2</th>
                         <th class="th-cl" colspan="2">Week 3</th>
                         <th class="th-cl" colspan="2">Week 4</th>
                         <th class="th-cl" colspan="2">Week 5</th>
                    </tr>

                    <tr>
                        <th class="th-cl" >Target <span class="required_fields">*</span></th>
                        <th class="th-cl" >Actual <span class="required_fields">*</span></th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>

                        <th class="th-cl" >Target</th>
                        <th class="th-cl" >Actual</th>
                    </tr>

                    <tr>
                        <th class="th-cl" >In Call (%)</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week1.target.in_call" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control" placeholder="Type here....."
                                v-model="weekly_performance.week1.actual.in_call" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week2.target.in_call" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                                v-model="weekly_performance.week2.actual.in_call" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week3.target.in_call" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                                v-model="weekly_performance.week3.actual.in_call" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week4.target.in_call" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                                v-model="weekly_performance.week4.actual.in_call" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....."
                                v-model="weekly_performance.week5.target.in_call" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....."
                                v-model="weekly_performance.week5.actual.in_call" />
                        </td>

                    </tr>

                    <tr>
                        <th class="th-cl" >Ready (%)</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week1.target.ready" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week1.actual.ready" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week2.target.ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week2.actual.ready" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week3.target.ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week3.actual.ready" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week4.target.ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week4.actual.ready" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week5.target.ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week5.actual.ready" />
                        </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >Wrap up (%)</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week1.target.wrap_up" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week1.actual.wrap_up" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                       <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week2.target.wrap_up" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week2.actual.wrap_up" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week3.target.wrap_up" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week3.actual.wrap_up" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week4.target.wrap_up" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week4.actual.wrap_up" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week5.target.wrap_up" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week5.actual.wrap_up" />
                        </td>
                    </tr>

                    <tr>
                        <th class="th-cl" style="width: 8%;" >Not Ready (%)</th>

                        <td class="shaded td-cl">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week1.target.not_ready" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>
                         <td class="td-cl" >
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week1.actual.not_ready" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week2.target.not_ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week2.actual.not_ready" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week3.target.not_ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week3.actual.not_ready" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week4.target.not_ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week4.actual.not_ready" />
                        </td>

                        <td class="shaded td-cl">
                            <input type='number' step="any" class="form-control shaded" placeholder="Type here....." 
                                v-model="weekly_performance.week5.target.not_ready" />
                        </td>
                         <td class="td-cl" >
                            <input type='number' step="any" class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.week5.actual.not_ready" />
                        </td>
                    </tr>

                    <tr>
                         <th class="th-cl" colspan="2">Extend Action Plan ?</th>
                         <td class="td-cl" colspan="3">
                            <validation-provider rules="required" v-slot="{ errors }">
                                <input type='text' class="form-control" placeholder="Type here....." 
                                v-model="weekly_performance.extended_action_plan" />
                                <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                            </validation-provider>
                        </td>

                         <th class="th-cl" colspan="2">Justification</th>
                         <td class="td-cl" colspan="5">
                            <input type='text' class="form-control" style="width:200%;" placeholder="Type here....."
                            v-model="weekly_performance.justification" />
                        </td>

                    </tr>

                </table><br />
                <table class="table-coaching-log" >
                    <tr>
                         <th class="th-cl" style="width:20%;">Running Scorecard</th>
                         <th class="th-cl" style="width:5%;">Weight <span class="required_fields">*</span></th>
                         <th class="th-cl" style="width:5%;">Current Scores <span class="required_fields">*</span></th>
                         <th class="th-cl" colspan="2">Rating <span class="required_fields">*</span></th>
                         <th class="th-cl" style="width:50%;">Additional Feedback</th>
                    </tr>
                    <tr>
                        <th class="th-cl" >Loan Amount</th>
                            <td class="shaded td-cl">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control shaded"
                                    v-model="weekly_performance.weight.loan_amount" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"
                                    v-model="weekly_performance.current_score.loan_amount" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_right_side.loan_amount" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_left_side.loan_amount" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>

                        <td class="no-bottom-border td-cl" rowspan="6"> 
                            <textarea class="form-control" style="width:200%; height: 200px;" placeholder="Type here....." 
                            v-model="weekly_performance.additional_feedback"/>
                        </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >QA Score</th>
                            <td class="shaded td-cl">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control shaded"
                                    v-model="weekly_performance.weight.qa_score" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"
                                    v-model="weekly_performance.current_score.qa_score" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_right_side.qa_score" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_left_side.qa_score" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >Compliance</th>
                            <td class="shaded td-cl">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control shaded"
                                    v-model="weekly_performance.weight.compliance" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"
                                    v-model="weekly_performance.current_score.compliance" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_right_side.compliance" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_left_side.compliance" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >Correction</th>
                            <td class="shaded td-cl">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control shaded"
                                    v-model="weekly_performance.weight.correction" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"
                                    v-model="weekly_performance.current_score.correction" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_right_side.correction" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_left_side.correction" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                    </tr>

                    <tr>
                        <th class="th-cl" >Attendance Reliability</th>
                            <td class="shaded td-cl">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control shaded"
                                    v-model="weekly_performance.weight.attendance_reliability" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"
                                    v-model="weekly_performance.current_score.attendance_reliability" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_right_side.attendance_reliability" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_left_side.attendance_reliability" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                    </tr>
                    <tr>
                        <th class="th-cl" >Total</th>
                            <td class="shaded td-cl" colspan="2">
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control shaded"  placeholder="Type here....."
                                    v-model="weekly_performance.weight.total" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_right_side.total" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                             <td class="td-cl" >
                                <validation-provider rules="required" v-slot="{ errors }">
                                    <input type='number' step="any" class="form-control"  placeholder="Type here....."
                                    v-model="weekly_performance.rating_left_side.total" />
                                    <span class="style-red-cl" >{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                                </validation-provider>
                            </td>
                    </tr>
                </table>
                <br>
                <button-save class="ladda-button btn btn-primary"/>
            </div>
        </the-form>
    </ValidationObserver>
</template>
<style>
    .pull-left {
        float: left!important;
    }
    .pull-right {
        float: right!important;
    }
    .table-coaching-log {
        border-collapse: collapse;
        /* width: 755px; */
        width: 100%;
    }

    .table-coaching-log, .td-cl, .th-cl {
        border: 1px solid black;
    }

    .th-cl, .td-cl {
        text-align: center;
        font-size: 12px
    }

    .th-cl {
    	background-color: #1F4788;
    	column-width: 150px;
    	color: white;
    	font-weight: normal;
    }

    .td-cl {
    	column-width: 200px;
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
    input:placeholder-shown, textarea:placeholder-shown {
        font-style: italic;
        font-size: smaller;
    }
    .style-red-cl {
        color: red;
        font-size: xx-small;
    }
</style>
<script>
    export default {
        props: {
            coachingLogProps: Object,
        },

        data: function () {
            return {
    			weekly_performance: {
                    week1: {
                        target: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                        actual: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                    },
                    week2: {
                        target: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                        actual: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                    },
                    week3: {
                        target: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                        actual: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                    },
                    week4: {
                        target: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                        actual: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                    },
                    week5: {
                        target: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                        actual: {
                            loan_origination: '',
                            qa_scores: '',
                            compliance: '',
                            attendance: '',
                            in_call: '',
                            ready: '',
                            wrap_up: '',
                            not_ready: '',
                        },
                    },
                    identified_behavior:'',
                    root_cause:'',
                    action_plans:'',
                    base_performance:'',
                    goal:'',
                    extended_action_plan:'',
                    justification:'',
                    additional_feedback:'',
                    employee_no: '',
                    weight: {
                        loan_amount: '',
                        qa_score: '',
                        compliance: '',
                        correction: '',
                        attendance_reliability: '',
                        total: ''
                    },
                    current_score: {
                        loan_amount: '',
                        qa_score: '',
                        compliance: '',
                        correction: '',
                        attendance_reliability: ''
                    },
                    rating_left_side: {
                        loan_amount: '',
                        qa_score: '',
                        compliance: '',
                        correction: '',
                        attendance_reliability: '',
                        total: '',
                    },
                    rating_right_side: {
                        loan_amount: '',
                        qa_score: '',
                        compliance: '',
                        correction: '',
                        attendance_reliability: '',
                        total: '',
                    },
                },
                form: new FormData,
            };
        },
        created() {
            this.coaching_log_id = this.coachingLogProps.id;
            this.weekly_performance.performance_id = this.coachingLogProps.performance.id;
            this.weekly_performance.employee_no = this.coachingLogProps.employee_no;
            console.log("this.weekly_performance.employee_no ==", this.weekly_performance.employee_no );
            this.weekly_performance.week1_id = this.coachingLogProps.performance.week1_id;
            this.weekly_performance.week2_id = this.coachingLogProps.performance.week2_id;
            this.weekly_performance.week3_id = this.coachingLogProps.performance.week3_id;
            this.weekly_performance.week4_id = this.coachingLogProps.performance.week4_id;
            this.weekly_performance.week5_id = this.coachingLogProps.performance.week5_id;

            if (this.coachingLogProps.performance.week_one) {
                this.weekly_performance.week1.target.loan_origination = this.coachingLogProps.performance.week_one.target_loan_origination;
                this.weekly_performance.week1.target.qa_scores = this.coachingLogProps.performance.week_one.target_qa_scores;
                this.weekly_performance.week1.target.compliance = this.coachingLogProps.performance.week_one.target_compliance;
                this.weekly_performance.week1.target.attendance = this.coachingLogProps.performance.week_one.target_attendance;
                this.weekly_performance.week1.target.in_call = this.coachingLogProps.performance.week_one.target_in_call;
                this.weekly_performance.week1.target.ready = this.coachingLogProps.performance.week_one.target_ready;
                this.weekly_performance.week1.target.wrap_up = this.coachingLogProps.performance.week_one.target_wrap_up;
                this.weekly_performance.week1.target.not_ready = this.coachingLogProps.performance.week_one.target_not_ready;
                this.weekly_performance.week1.actual.loan_origination = this.coachingLogProps.performance.week_one.actual_loan_origination;
                this.weekly_performance.week1.actual.qa_scores = this.coachingLogProps.performance.week_one.actual_qa_scores;
                this.weekly_performance.week1.actual.compliance = this.coachingLogProps.performance.week_one.actual_compliance;
                this.weekly_performance.week1.actual.attendance = this.coachingLogProps.performance.week_one.actual_attendance;
                this.weekly_performance.week1.actual.in_call = this.coachingLogProps.performance.week_one.actual_in_call;
                this.weekly_performance.week1.actual.ready = this.coachingLogProps.performance.week_one.actual_ready;
                this.weekly_performance.week1.actual.wrap_up = this.coachingLogProps.performance.week_one.actual_wrap_up;
                this.weekly_performance.week1.actual.not_ready = this.coachingLogProps.performance.week_one.actual_not_ready;
            }

            if (this.coachingLogProps.performance.week_two) {
                this.weekly_performance.week2.target.loan_origination = this.coachingLogProps.performance.week_two.target_loan_origination;
                this.weekly_performance.week2.target.qa_scores = this.coachingLogProps.performance.week_two.target_qa_scores;
                this.weekly_performance.week2.target.compliance = this.coachingLogProps.performance.week_two.target_compliance;
                this.weekly_performance.week2.target.attendance = this.coachingLogProps.performance.week_two.target_attendance;
                this.weekly_performance.week2.target.in_call = this.coachingLogProps.performance.week_two.target_in_call;
                this.weekly_performance.week2.target.ready = this.coachingLogProps.performance.week_two.target_ready;
                this.weekly_performance.week2.target.wrap_up = this.coachingLogProps.performance.week_two.target_wrap_up;
                this.weekly_performance.week2.target.not_ready = this.coachingLogProps.performance.week_two.target_not_ready;
                this.weekly_performance.week2.actual.loan_origination = this.coachingLogProps.performance.week_two.actual_loan_origination;
                this.weekly_performance.week2.actual.qa_scores = this.coachingLogProps.performance.week_two.actual_qa_scores;
                this.weekly_performance.week2.actual.compliance = this.coachingLogProps.performance.week_two.actual_compliance;
                this.weekly_performance.week2.actual.attendance = this.coachingLogProps.performance.week_two.actual_attendance;
                this.weekly_performance.week2.actual.in_call = this.coachingLogProps.performance.week_two.actual_in_call;
                this.weekly_performance.week2.actual.ready = this.coachingLogProps.performance.week_two.actual_ready;
                this.weekly_performance.week2.actual.wrap_up = this.coachingLogProps.performance.week_two.actual_wrap_up;
                this.weekly_performance.week2.actual.not_ready = this.coachingLogProps.performance.week_two.actual_not_ready;
            }

            if (this.coachingLogProps.performance.week_three) {
                this.weekly_performance.week3.target.loan_origination = this.coachingLogProps.performance.week_three.target_loan_origination;
                this.weekly_performance.week3.target.qa_scores = this.coachingLogProps.performance.week_three.target_qa_scores;
                this.weekly_performance.week3.target.compliance = this.coachingLogProps.performance.week_three.target_compliance;
                this.weekly_performance.week3.target.attendance = this.coachingLogProps.performance.week_three.target_attendance;
                this.weekly_performance.week3.target.in_call = this.coachingLogProps.performance.week_three.target_in_call;
                this.weekly_performance.week3.target.ready = this.coachingLogProps.performance.week_three.target_ready;
                this.weekly_performance.week3.target.wrap_up = this.coachingLogProps.performance.week_three.target_wrap_up;
                this.weekly_performance.week3.target.not_ready = this.coachingLogProps.performance.week_three.target_not_ready;
                this.weekly_performance.week3.actual.loan_origination = this.coachingLogProps.performance.week_three.actual_loan_origination;
                this.weekly_performance.week3.actual.qa_scores = this.coachingLogProps.performance.week_three.actual_qa_scores;
                this.weekly_performance.week3.actual.compliance = this.coachingLogProps.performance.week_three.actual_compliance;
                this.weekly_performance.week3.actual.attendance = this.coachingLogProps.performance.week_three.actual_attendance;
                this.weekly_performance.week3.actual.in_call = this.coachingLogProps.performance.week_three.actual_in_call;
                this.weekly_performance.week3.actual.ready = this.coachingLogProps.performance.week_three.actual_ready;
                this.weekly_performance.week3.actual.wrap_up = this.coachingLogProps.performance.week_three.actual_wrap_up;
                this.weekly_performance.week3.actual.not_ready = this.coachingLogProps.performance.week_three.actual_not_ready;
            }

            if (this.coachingLogProps.performance.week_four) {
                this.weekly_performance.week4.target.loan_origination = this.coachingLogProps.performance.week_four.target_loan_origination;
                this.weekly_performance.week4.target.qa_scores = this.coachingLogProps.performance.week_four.target_qa_scores;
                this.weekly_performance.week4.target.compliance = this.coachingLogProps.performance.week_four.target_compliance;
                this.weekly_performance.week4.target.attendance = this.coachingLogProps.performance.week_four.target_attendance;
                this.weekly_performance.week4.target.in_call = this.coachingLogProps.performance.week_four.target_in_call;
                this.weekly_performance.week4.target.ready = this.coachingLogProps.performance.week_four.target_ready;
                this.weekly_performance.week4.target.wrap_up = this.coachingLogProps.performance.week_four.target_wrap_up;
                this.weekly_performance.week4.target.not_ready = this.coachingLogProps.performance.week_four.target_not_ready;
                this.weekly_performance.week4.actual.loan_origination = this.coachingLogProps.performance.week_four.actual_loan_origination;
                this.weekly_performance.week4.actual.qa_scores = this.coachingLogProps.performance.week_four.actual_qa_scores;
                this.weekly_performance.week4.actual.compliance = this.coachingLogProps.performance.week_four.actual_compliance;
                this.weekly_performance.week4.actual.attendance = this.coachingLogProps.performance.week_four.actual_attendance;
                this.weekly_performance.week4.actual.in_call = this.coachingLogProps.performance.week_four.actual_in_call;
                this.weekly_performance.week4.actual.ready = this.coachingLogProps.performance.week_four.actual_ready;
                this.weekly_performance.week4.actual.wrap_up = this.coachingLogProps.performance.week_four.actual_wrap_up;
                this.weekly_performance.week4.actual.not_ready = this.coachingLogProps.performance.week_four.actual_not_ready;
            }

            if (this.coachingLogProps.performance.week_five) {
                this.weekly_performance.week5.target.loan_origination = this.coachingLogProps.performance.week_five.target_loan_origination;
                this.weekly_performance.week5.target.qa_scores = this.coachingLogProps.performance.week_five.target_qa_scores;
                this.weekly_performance.week5.target.compliance = this.coachingLogProps.performance.week_five.target_compliance;
                this.weekly_performance.week5.target.attendance = this.coachingLogProps.performance.week_five.target_attendance;
                this.weekly_performance.week5.target.in_call = this.coachingLogProps.performance.week_five.target_in_call;
                this.weekly_performance.week5.target.ready = this.coachingLogProps.performance.week_five.target_ready;
                this.weekly_performance.week5.target.wrap_up = this.coachingLogProps.performance.week_five.target_wrap_up;
                this.weekly_performance.week5.target.not_ready = this.coachingLogProps.performance.week_five.target_not_ready;
                this.weekly_performance.week5.actual.loan_origination = this.coachingLogProps.performance.week_five.actual_loan_origination;
                this.weekly_performance.week5.actual.qa_scores = this.coachingLogProps.performance.week_five.actual_qa_scores;
                this.weekly_performance.week5.actual.compliance = this.coachingLogProps.performance.week_five.actual_compliance;
                this.weekly_performance.week5.actual.attendance = this.coachingLogProps.performance.week_five.actual_attendance;
                this.weekly_performance.week5.actual.in_call = this.coachingLogProps.performance.week_five.actual_in_call;
                this.weekly_performance.week5.actual.ready = this.coachingLogProps.performance.week_five.actual_ready;
                this.weekly_performance.week5.actual.wrap_up = this.coachingLogProps.performance.week_five.actual_wrap_up;
                this.weekly_performance.week5.actual.not_ready = this.coachingLogProps.performance.week_five.actual_not_ready;
            }

        },
        methods: {
            eventUpdate() {
                $('.ladda-button').attr("disabled", true);
                let week = [];

                if (this.weekly_performance.week5.target.loan_origination && this.weekly_performance.week5.actual.loan_origination) {
                    week.push({'week1':this.weekly_performance.week1});
                    week.push({'week2':this.weekly_performance.week2});
                    week.push({'week3':this.weekly_performance.week3});
                    week.push({'week4':this.weekly_performance.week4});
                    week.push({'week5':this.weekly_performance.week5});

                } else if (this.weekly_performance.week4.target.loan_origination && this.weekly_performance.week4.actual.loan_origination) {
                    week.push({'week1':this.weekly_performance.week1});
                    week.push({'week2':this.weekly_performance.week2});
                    week.push({'week3':this.weekly_performance.week3});
                    week.push({'week4':this.weekly_performance.week4});

                } else if (this.weekly_performance.week3.target.loan_origination && this.weekly_performance.week3.actual.loan_origination) {
                    week.push({'week1':this.weekly_performance.week1});
                    week.push({'week2':this.weekly_performance.week2});
                    week.push({'week3':this.weekly_performance.week3});

                } else if (this.weekly_performance.week2.target.loan_origination && this.weekly_performance.week2.actual.loan_origination) {
                    week.push({'week1':this.weekly_performance.week1});
                    week.push({'week2':this.weekly_performance.week2});
                } else {
                    week.push({'week1':this.weekly_performance.week1});
                }
                
                let getAxios = '';
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                
                this.form.append('employee_no', 
                this.weekly_performance.employee_no);
                
                this.form.append('identified_behavior', 
                this.weekly_performance.identified_behavior);
                
                this.form.append('root_cause', 
                this.weekly_performance.root_cause);
                
                this.form.append('action_plans', 
                this.weekly_performance.action_plans);
                
                this.form.append('base_performance', 
                this.weekly_performance.base_performance);
                
                this.form.append('goal', 
                this.weekly_performance.goal);
                
                this.form.append('extended_action_plan', 
                this.weekly_performance.extended_action_plan);
                
                this.form.append('justification', 
                this.weekly_performance.justification);
                
                this.form.append('additional_feedback', 
                this.weekly_performance.additional_feedback);
                
                this.form.append('week', 
                JSON.stringify(week));
                
                this.form.append('weight_loan_amount', 
                this.weekly_performance.weight.loan_amount);
                
                this.form.append('weight_qa_score', 
                this.weekly_performance.weight.qa_score);
                
                this.form.append('weight_compliance', 
                this.weekly_performance.weight.compliance);
                
                this.form.append('weight_correction', 
                this.weekly_performance.weight.correction);
                
                this.form.append('weight_attendance_reliability', 
                this.weekly_performance.weight.attendance_reliability);
                
                this.form.append('weight_total', 
                this.weekly_performance.weight.total);
                
                this.form.append('current_score_loan_amount', 
                this.weekly_performance.current_score.loan_amount);
                
                this.form.append('current_score_qa_score', 
                this.weekly_performance.current_score.qa_score);
                
                this.form.append('current_score_compliance', 
                this.weekly_performance.current_score.compliance);
                
                this.form.append('current_score_correction', 
                this.weekly_performance.current_score.correction);
                
                this.form.append('current_score_attendance_reliability', 
                this.weekly_performance.current_score.attendance_reliability);
                
                this.form.append('rating_left_side_loan_amount', 
                this.weekly_performance.rating_left_side.loan_amount);
                
                this.form.append('rating_left_side_qa_score', 
                this.weekly_performance.rating_left_side.qa_score);
                
                this.form.append('rating_left_side_compliance', 
                this.weekly_performance.rating_left_side.compliance);
                
                this.form.append('rating_left_side_correction', 
                this.weekly_performance.rating_left_side.correction);
                
                this.form.append('rating_left_side_attendance_reliability', 
                this.weekly_performance.rating_left_side.attendance_reliability);
                
                this.form.append('rating_left_side_total', 
                this.weekly_performance.rating_left_side.total);
                
                this.form.append('rating_right_side_loan_amount', 
                this.weekly_performance.rating_right_side.loan_amount);
                
                this.form.append('rating_right_side_qa_score', 
                this.weekly_performance.rating_right_side.qa_score);
                
                this.form.append('rating_right_side_compliance', 
                this.weekly_performance.rating_right_side.compliance);
                
                this.form.append('rating_right_side_correction', 
                this.weekly_performance.rating_right_side.correction);
                
                this.form.append('rating_right_side_attendance_reliability', 
                this.weekly_performance.rating_right_side.attendance_reliability);
                
                this.form.append('rating_right_side_total', 
                this.weekly_performance.rating_right_side.total);
                
                getAxios = this.$constants.Coaching_API.post('/weekly_performance_form', this.form, config)

                getAxios.then(response => {
                    $('.ladda-button').attr("disabled", false);
                    this.$swal({
                        html: 'Successfully added',
                        type: "success",
                        confirmButtonText: 'OK',
                    });
                    this.$bus.$emit('init_modal', false);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
