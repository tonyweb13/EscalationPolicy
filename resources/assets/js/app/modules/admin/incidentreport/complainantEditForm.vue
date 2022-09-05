<template>
    <the-form :asterisk="true" @submitForm="eventUpdate" >
        <div slot="form-body">
            <the-label label="Respondent" :asterisk="false">
                <vSelect
                    v-model="complainant.respondent"
                    :options="selectedRespondent"
                    :disabled="true"
                    label="text"
                />
            </the-label>
            <!-- start single offense -->
            <div v-if="complainant.offense_status == 'single'">
                <the-label label="Provision" :asterisk="true">
                    <vSelect
                        v-model="complainant.category"
                        :options="selectedCategory"
                        label="text"
                        @input="eventOffense"
                    />
                </the-label>
                <the-label label="Offense" :asterisk="true"
                v-show="this.complainant.comp_offense_check == 'offense'">
                    <vSelect
                        v-model="complainant.offense"
                        :options="selectedOffense"
                        label="text"
                        :disabled="offenseDisabled"
                        @input="eventCoc"
                    />
                </the-label>

                <the-label v-if="complainant.offense_description" label="Description:"
                v-show="this.complainant.comp_offense_check">
                    <div style="background: #eee; padding: 10px;"
                    v-html="complainant.offense_description"/>
                </the-label>

                <the-label v-if="complainant.gravity" label="Gravity:"
                v-show="this.complainant.comp_offense_check">
                    <div style="background: #eee; padding: 10px;" v-html="complainant.gravity"/>
                </the-label>
            </div>
            <!-- end single offense -->
            <!-- start multiple offense -->
            <div v-else-if="complainant.offense_status == 'multiple'">
                <div v-for="(multi_cat, index) in complainant.multiple_offense" :key="index">
                    <the-label label="Provision" :asterisk="true">
                        <vSelect
                            v-model="complainant.multi_category[index]"
                            :options="selectedCategory"
                            label="text"
                            @input="eventOffenseMulti"
                        />
                    </the-label>
                    <the-label label="Offense" :asterisk="true">
                        <vSelect
                            v-model="complainant.multi_offense[index]"
                            :options="selectedOffense"
                            label="text"
                            :disabled="offenseDisabled"
                            @input="eventCocMulti(complainant.multi_offense[index], index)"
                        />
                    </the-label>
                    <the-label label="Description:" v-if="complainant.multi_offense_desc[index]">
                        <div style="background: #eee; padding: 10px;"
                        v-html="complainant.multi_offense_desc[index]"/>
                    </the-label>
                    <the-label label="Gravity:">
                        <div style="background: #eee; padding: 10px;"
                        v-html="complainant.multi_gravity[index]"/>
                    </the-label>
                </div>
            </div>
            <!-- end multiple offense -->
            <the-label label="Date of Incident" :asterisk="true" class="m-t-lg">
                <br>
                <div class="col-lg-6">
                    <small>Start</small>
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="true"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        placeholder="YYYY-MM-DD"
                        v-model="complainant.date_incident_start"
                    />
                </div>
                <div class="col-lg-6">
                    <small>End</small>
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="true"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        placeholder="YYYY-MM-DD"
                        v-model="complainant.date_incident_end"
                        @input="dateRangeValidation(complainant.date_incident_start,
                        complainant.date_incident_end)"
                    />
                </div>
            </the-label>
            <br>
            <br>
            <br>
            <the-label label="Description" :asterisk="true">
                <vue-editor v-model="complainant.description" :editorToolbar="customToolbar"/>
            </the-label>
            <button-submit :disabled='!isComplete'/>
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            complainantProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                complainant: {
                    respondent: '',
                    complainant_request_id: '',
                    category: '',
                    offense: { 'value' : 0, 'text': '' },
                    gravity: '',
                    date_incident_start: '',
                    date_incident_end: '',
                    offense_description: '',
                    respondent_multiple: [],
                    incident_report: [],
                    hrbp_cluster_id: '',
                    infraction: '',
                    attendance_points: '',
					definition: '',
					offense_status: '',
                    complainant_id: '',
					respondent_id: '',
                    description: '',
                    multiple_offense: [],
                    multi_category: [],
                    multi_offense: [],
                    multi_offense_desc: [],
                    multi_gravity: [],
                    multiple_offense_new: [],
                    new_multi_off: '',
                },
                selectedRespondent: [],
                selectedCategory: [],
                selectedOffense: [],
                offenseDisabled: true,
                selectedCoc: [],
                selectedInfraction: [],
                selectedAttendance: [],
                form: new FormData,
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                showOffense: false,
                showGravity: false,
                showDescription: false,
                showTypeInfraction: false,
                showAttendancePoints: false,
                showDefinition: false,
                infractionDisabled: false,
            };
        },
        created(){
            this.eventCategory();
            this.eventOffense();
            this.eventFilingData();
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.complainant.multiple_offense) {
                    return this.complainant.respondent && this.complainant.multi_category
                    && this.complainant.multi_offense && this.complainant.date_incident_start
                    && this.complainant.description;
                } else {
                    return this.complainant.respondent && this.complainant.category
                    && this.complainant.offense && this.complainant.date_incident_start
                    && this.complainant.description;
                }
            }
        },
        methods: {
            eventFilingData() {
                console.log("eventFilingData this.complainant==", this.complainant)
                this.complainant.respondent = this.complainantProps.comp_respondent;
                this.complainant.id = this.complainantProps.comp_id;
                this.complainant.respondent_id = this.complainantProps.res_id;
                this.complainant.date_incident_start = this.complainantProps.comp_date_incident_start;
                this.complainant.date_incident_end = this.complainantProps.comp_date_incident_end;
                this.complainant.description = this.complainantProps.comp_description;
                this.complainant.comp_offense_check = this.complainantProps.comp_offense_check;

                if (this.complainantProps.comp_offense) {
                    this.complainant.offense.value = this.complainantProps.comp_offense_id;
                    this.complainant.offense.text = this.complainantProps.comp_offense;
                    this.complainant.category = this.complainantProps.comp_category;
                    this.complainant.offense_description = this.complainantProps.comp_offense_description;
                    this.complainant.gravity = this.complainantProps.comp_gravity;
                    this.complainant.offense_status = "single";

                } else if (this.complainantProps.comp_multiple_offense.length > 0) {
                    this.complainant.offense_status = "multiple";
                    this.complainant.multiple_offense = JSON.parse(this.complainantProps.comp_multiple_offense);
                    for (let i = 0; i < this.complainant.multiple_offense.length; i++) {
                        if(this.complainant.multiple_offense[i] != undefined){
                            this.$constants.Coc_API.get("/offense/codeofconduct/"
                            + this.complainant.multiple_offense[i])
                            .then(response => {
                                this.selectedCoc = response.data;
                                let cat = {
                                    'value': this.selectedCoc.category.id,
                                    'text': this.selectedCoc.category.name,
                                    'selected': 'selected',
                                }
                                let off = {
                                    'value': this.selectedCoc.id,
                                    'text': this.selectedCoc.offense,
                                    'selected': 'selected',
                                }
                                this.complainant.multi_category.push(cat);
                                this.complainant.multi_offense.push(off);
                                this.complainant.multi_offense_desc.push(this.selectedCoc.description);
                                this.complainant.multi_gravity.push(this.selectedCoc.gravity.gravity);
                            })
                            .catch(error => {
                                this.globalErrorHandling(error);
                            });
                        }
                    }
                }
            },
            eventUpdate() {
                this.laddabtn.start();
                for(let l=0; l<this.complainant.multiple_offense.length; l++){
                    if (this.complainant.multi_offense[l] != undefined) {
                        this.complainant.multiple_offense_new.push(this.complainant.multi_offense[l].value);
                    }
                }
                let complainant = this.complainant;
                let date_start = new Date(complainant.date_incident_start).toISOString().substring(0, 10);
                let date_end = new Date(complainant.date_incident_end).toISOString().substring(0, 10);

                let getAxios = '';
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                this.form.append('complainant_id', complainant.id);
                this.form.append('offense_id', complainant.offense.value);
                this.form.append('multiple_offense_new', complainant.multiple_offense_new);
                this.form.append('date_incident_start', date_start);
                this.form.append('date_incident_end', date_end);
                this.form.append('description', complainant.description);
                getAxios = this.$constants.Default_API.post('/complainant/complainant_edit/edit', 
                this.form, config)
                getAxios.then(response => {
                    this.$bus.$emit('init_modal', false);
                    this.complainant = response.data;
                    this.laddabtn.stop();
                    this.$bus.$emit('updateList');
                    this.$bus.$emit('updateDashboard');
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
                if(this.complainant.category.value != undefined){
                    if(this.complainant.category.value == 15){
                        this.infractionDisabled = false;
                        this.offenseDisabled = true;
                        this.disabledWitness = true;
                        this.complainant.witness = '';
                        this.complainant.offense = '';
                        this.complainant.offense_description = '';
                        this.complainant.gravity = '';
                        this.showOffense = false;
                        this.showGravity = false;
                        this.showDescription = false;
                        this.showTypeInfraction = true;
                        this.showAttendancePoints = true;
                        this.showDefinition = true;
                        this.$constants.Coc_API.get("/dropdown/attendance/infraction")
                        .then(response => {
                            this.selectedInfraction = response.data;
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    } else {
                        this.infractionDisabled = true;
                        this.offenseDisabled = false;
                        this.complainant.offense = '';
                        this.complainant.offense_description = '';
                        this.complainant.gravity = '';
                        this.disabledWitness = false;
                        this.showOffense = true;
                        this.showGravity = true;
                        this.showDescription = true;
                        this.showTypeInfraction = false;
                        this.showAttendancePoints = false;
                        this.showDefinition = false;
                        this.$constants.Coc_API.get("/dropdown/offense/category/"
                        + this.complainant.category.value)
                        .then(response => {
                            this.selectedOffense = response.data;
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    }
                }
            },
            eventCoc(){
                if(this.complainant.offense.value != undefined){
                    this.$constants.Coc_API.get("/offense/codeofconduct/"
                    + this.complainant.offense.value)
                    .then(response => {
                        this.selectedCoc = response.data;
                        this.complainant.offense_description = this.selectedCoc.offense_description;
                        this.complainant.gravity = this.selectedCoc.gravity.gravity;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            eventOffenseMulti(e){
                if (!e.selected) {
                    this.offenseDisabled = false;
                    this.showOffense = true;
                    this.showGravity = true;
                    this.showDescription = true;
                    this.showTypeInfraction = false;
                    this.showAttendancePoints = false;
                    this.showDefinition = false;
                    this.$constants.Coc_API.get("/dropdown/offense/category/" + e.value)
                    .then(response => {
                        this.selectedOffense = response.data;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            eventCocMulti(multi_offense, multi_index){
                if (!multi_offense.selected) {
                    this.$constants.Coc_API.get("/offense/codeofconduct/" + multi_offense.value)
                    .then(response => {
                        this.selectedCoc = response.data;
                        let cat = {
                            'value': this.selectedCoc.category.id,
                            'text': this.selectedCoc.category.name,
                            'selected': 'selected',
                        }
                        let off = {
                            'value': this.selectedCoc.id,
                            'text': this.selectedCoc.offense,
                            'selected': 'selected',
                        }
                        this.complainant.multi_category.push(cat);
                        this.complainant.multi_offense.push(off);
                        this.complainant.multi_offense_desc.push(this.selectedCoc.description);
                        this.complainant.multi_gravity.push(this.selectedCoc.gravity.gravity);
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
            dateRangeValidation(start, end){
                if (end < start) {
                    this.$swal({
                        text: "Date of Incident is not valid range",
                        type: "error",
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        this.complainant.date_incident_start = '';
                        this.complainant.date_incident_end = '';
                    });
                }
            },
        },
    }
</script>
