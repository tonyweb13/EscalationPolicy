<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <the-label v-if="coaching_log.form_type != 15"
                label="Date of Incident" :asterisk="true" class="m-t-lg" style="margin-bottom: 80px;">
                    <br>
                    <validation-provider rules="required" v-slot="{ errors }">
                        <div class="col-lg-6">
                            <small>Start</small>
                            <datepicker
                                :input-class="'custom-datepicker'"
                                :format="'yyyy-MM-dd'"
                                :calendar-button="true"
                                :calendar-button-icon="'fa fa-calendar'"
                                :use-utc="true"
                                placeholder="YYYY-MM-DD"
                                :disabled-dates="maxDate.disabledDates"
                                v-model="coaching_log.date_start"
                            />
                        </div>
                        <span class="style-red" id="date_start">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>

                    <validation-provider rules="required" v-slot="{ errors }">
                        <div class="col-lg-6">
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
                        </div>
                        <span class="style-red" id="date_end">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label v-if="coaching_log.form_type != 15"
                label="Number of Occurrences">
                    <input type='text' id="occurrences" class="form-control" 
                    v-model="coaching_log.number_occurrence" />
                </the-label>

                <div v-if="coaching_log.form_type != 15">
                    <strong>Offense : </strong>

                    <div v-if="coaching_log.others" colspan="2">
                        <fieldset class="fieldsetCustom">
                            <legend class="legendCustom">Others</legend>
                            <table class="table borderless">
                                <tr>
                                    <td colspan="2">
                                        <strong>Classification of Coaching : </strong> Others
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        {{ coaching_log.others }}
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                    <div v-else-if="coaching_log.offense_multiple" v-for="(off_multi, index) 
                    in coaching_log.offense_multiple" :key="index">
                        <fieldset class="fieldsetCustom">
                            <legend class="legendCustom">Offense ( {{ index+1 }} )</legend>
                            <table class="table borderless">
                                <tr>
                                    <td colspan="2">
                                        <strong>Classification of Coaching : </strong> 
                                        {{ off_multi.category.name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Gravity : </strong> {{ off_multi.gravity.gravity }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Prescriptive Period : </strong> 
                                        {{ off_multi.gravity.prescriptive_period }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Offense : </strong>
                                        {{ off_multi.offense }}
                                        <strong style="color:red;" v-if="off_multi.offense_archived">
                                            {{ off_multi.offense_archived }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                    <div v-else-if="coaching_log.offense_single" colspan="2">
                        <fieldset class="fieldsetCustom">
                            <legend class="legendCustom">Offense</legend>
                            <table class="table borderless">
                                <tr>
                                    <td colspan="2">
                                        <strong>Classification of Coaching : </strong> 
                                        {{ coaching_log.offense_single.category.name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Gravity : </strong> 
                                        {{ coaching_log.offense_single.gravity.gravity }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Prescriptive Period : </strong> 
                                        {{ coaching_log.offense_single.gravity.prescriptive_period }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Offense : </strong>
                                        {{ coaching_log.offense_single.offense }}
                                        <strong style="color:red;" 
                                        v-if="coaching_log.offense_single.offense_archived">
                                            {{ coaching_log.offense_single.offense_archived }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                    <div v-else colspan="2">
                        <fieldset class="fieldsetCustom">
                            <legend class="legendCustom">No Offense</legend>
                            <table class="table borderless">
                                <tr>
                                    <td colspan="2">
                                        <strong>No Offense Selected / Old Record</strong>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>

                <the-label label="Findings / Coaching Opportunities / Areas of Improvement" 
                :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="findings" rows="4" cols="104.5" style="max-width:100%;" 
                        v-model="coaching_log.findings" />
                        <span class="style-red" id="number_occurrence">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label v-if="coaching_log.form_type != 15"
                label="Point of Discussion/ Coaching Details" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="point_of_disscussion" rows="4" cols="104.5" 
                        style="max-width:100%;" v-model="coaching_log.point_of_disscussion" />
                        <span class="style-red" id="number_occurrence">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Action Plans" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="action_plans" rows="4" cols="104.5" style="max-width:100%;" 
                        v-model="coaching_log.action_plans" />
                        <span class="style-red" id="number_occurrence">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label v-if="coaching_log.form_type != 15"
                label="Agent’s Commitment" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="agent_commitment" rows="4" cols="104.5" style="max-width:100%;"
                         v-model="coaching_log.agents_commitment" />
                        <span class="style-red" id="number_occurrence">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label v-if="coaching_log.form_type != 15"
                label="Supervisor’s Commitment" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <textarea id="supervisor_commitment" rows="4" cols="104.5" 
                        style="max-width:100%;" v-model="coaching_log.supervisors_commitment" />
                        <span class="style-red" id="number_occurrence">{{ errors[0] }}</span>
                        <br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <button-save class="ladda-button btn btn-primary" />
            </div>
        </the-form>
    </ValidationObserver>
</template>
<script>
    export default {
        props: {
            coachingLogProps: Object,
        },

        data: function () {
            return {
    			coaching_log: {
                    employee_no: '',
                    findings: '',
                    point_of_disscussion: '',
                    action_plans: '',
                    agents_commitment: '',
                    supervisors_commitment: '',
                    category: '',
                    offense: '',
                    gravity: '',
                    offense_description: '',
                    offense_multiple: [],
                    infraction: '',
                    attendance_points: '',
                    definition: '',
                    form_type: ''
                },
                selectedCategory: [],
                selectedOffense: [],
                offenseDisabled: true,
                disabledWitness: false,
                disabledAttachmentType: false,
                showOffense: false,
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
                indexCnt: 2,
                offenseCnt: 0,
                offense_remove: [],
                maxDate: null,
                offense_index: [],
                others: '',
            };
        },

        created() {
            this.coaching_log.id = this.coachingLogProps.id;
            this.coaching_log.findings = this.coachingLogProps.findings;
            this.coaching_log.point_of_disscussion = this.coachingLogProps.point_of_disscussion;
            this.coaching_log.action_plans = this.coachingLogProps.action_plans;
            this.coaching_log.agents_commitment = this.coachingLogProps.agents_commitment;
            this.coaching_log.supervisors_commitment = this.coachingLogProps.supervisors_commitment;
            this.coaching_log.offense_id = this.coachingLogProps.supervisors_commitment;
            this.coaching_log.cl_number = this.coachingLogProps.cl_number;
            this.coaching_log.date_start = this.coachingLogProps.date_start;
            this.coaching_log.date_end = this.coachingLogProps.date_end;
            this.coaching_log.number_occurrence = this.coachingLogProps.number_occurrence;
            this.coaching_log.offense_multiple = this.coachingLogProps.offense_multiple;
            this.coaching_log.offense_single = this.coachingLogProps.offense_single;
            this.coaching_log.others = this.coachingLogProps.others;
            this.coaching_log.status = this.coachingLogProps.status;
            this.coaching_log.form_type = this.coachingLogProps.form_type;
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
        },
        computed: {
            isComplete () {
                if (this.coaching_log.form_type == 15) {
                    return this.coaching_log.findings && this.coaching_log.action_plans;

                } else {
                    return this.coaching_log.findings && this.coaching_log.point_of_disscussion
                    && this.coaching_log.action_plans && this.coaching_log.agents_commitment
                    && this.coaching_log.supervisors_commitment && this.coaching_log.number_occurrence
                    && this.coaching_log.date_end && this.coaching_log.date_start;
                }
            }
        },
        methods: {
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

                        this.showOffense = false;
                        this.showGravity = false;
                        this.showDescription = false;
                        this.showTypeInfraction = true;
                        this.showAttendancePoints = true;
                        this.showDefinition = true;

                        this.dropdownInfraction();

                    } else {
                        this.infractionDisabled = true;
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

                        this.$constants.Coc_API.get("/dropdown/offense/category/" + 
                        this.coaching_log.category.value)
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
                let getAxios = '';

                getAxios = this.$constants.Coaching_API.get('/update_coaching', {
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
                        id: this.coaching_log.id,
                    },
                })
                getAxios.then(response => {
                    $('.ladda-button').attr("disabled", false);
                    this.$swal({
                        html: 'Successfully added',
                        type: "success",
                        confirmButtonText: 'OK',
                    });
                    this.$bus.$emit('init_modal', false);
                    window.location.href = '/employee/coachinglog'
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
                    console.log(this.offense_remove);
                    this.$constants.Coc_API.get("/offense/codeofconduct/multiple/"
                    +this.provision_multiple[index].offense_multi.value+"/"
                    +this.offense_remove)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.dropdownOffenseCategory(this.provision_multiple[index].category_multi.value, 
                        this.offense_remove);
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
