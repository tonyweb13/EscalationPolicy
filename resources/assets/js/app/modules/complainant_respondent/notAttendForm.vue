<template>
    <div>
        <the-form :asterisk="true" @submitForm="eventHearing">
            <div slot="form-body">
                <the-label label="Please Specify Reason" :asterisk="true">
                    <vue-editor v-model="hearing.reason" :editorToolbar="customToolbar"/>
                </the-label>

                <!-- <div v-show="asteriskDate" class="asterisk_label">*</div>
                <the-label label="Suggested Date">
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'yyyy-MM-dd'"
                        :calendar-button="calendarButton"
                        :calendar-button-icon="'fa fa-calendar'"
                        :use-utc="true"
                        v-model="hearing.suggested_date"
                    />
                </the-label> -->

                <!-- <div v-show="asteriskTime" class="asterisk_label">*</div>
                <the-label label="Suggested Time">
                    <vSelect
                        v-model="hearing.suggested_time"
                        :options="selectedTime"
                        label="text"
                    />
                </the-label> -->

                <!-- <div v-show="asteriskPlace" class="asterisk_label">*</div>
                <the-label label="Suggested Meeting Place">
                    <vSelect
                        v-model="hearing.suggested_place"
                        :options="selectedMeetingPlace"
                        label="text"
                    />
                </the-label> -->

                <button-save :disabled='!isComplete'/>
            </div>
        </the-form>
    </div>
</template>
<script>
    export default {
        props: {
            hearingProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                hearing: {
                    respondent_id: '',
                    going: '',
                    ir_id: '',
                    requestor: '',
                    requestor_user_id: '',
                    reason: '',
                    // suggested_date: '',
                    // suggested_time: '',
                    // suggested_place: '',
                },
                calendarButton: true,
                // selectedTime: this.$constants.Time,
                // selectedMeetingPlace: this.$constants.Meeting_Place,
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                asteriskDate: false,
                asteriskTime: false,
                asteriskPlace: false,
            };
        },
        created() {
            this.hearing.ir_id = this.hearingProps.ir_id;
            this.hearing.requestor = this.hearingProps.requestor;
            this.hearing.requestor_user_id = this.hearingProps.requestor_user_id;
            this.hearing.respondent_id = this.hearingProps.respondent_id;
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                // if (this.hearing.suggested_date) {
                //     this.asteriskDate = true;
                //     this.asteriskTime = true;
                //     this.asteriskPlace = true;

                //     return this.hearing.reason && this.hearing.suggested_date
                //     && this.hearing.suggested_time && this.hearing.suggested_place;
                // } else {
                    return this.hearing.reason;
                // }
            }
        },
        methods: {
            eventHearing() {
                this.laddabtn.start();
                let hearing = this.hearing;

                let hrApproval = '';
                // if (hearing.suggested_date || hearing.suggested_time) {
                //     hrApproval = "Request Suggested Date";
                // } else {
                    hrApproval = "Invite Decline";
                // }

                if (this.hearing.respondent_id) {

                    this.$constants.Default_API.get("/hearing/hr/approval/"+ this.hearing.respondent_id, {
                        params: {
                            going: "No",
                            hr_approval: hrApproval,
                            ir_id: hearing.ir_id,
                            requestor_user_id: hearing.requestor_user_id,
                            reason: hearing.reason,
                        }
                    })
                    .then(response => {
                        this.laddabtn.stop();
                        this.$bus.$emit('init_modal', false);
                        this.$bus.$emit('updateList');
                        this.$bus.$emit('updateDashboard');
                        return response.data;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else {
                    this.$constants.Default_API.get("/hearing/create", {
                        params: {
                            going: "No",
                            ir_id: hearing.ir_id,
                            requestor: hearing.requestor,
                            requestor_user_id: hearing.requestor_user_id,
                            reason: hearing.reason,
                            // suggested_date: hearing.suggested_date,
                            // suggested_time: hearing.suggested_time.value,
                            // suggested_place: hearing.suggested_place.value,
                            hr_approval: hrApproval,
                        },
                    }).then(response => {
                        this.laddabtn.stop();
                        this.$bus.$emit('init_modal', false);
                        this.hearing = response.data;

                        this.$success('Successfully Saved!')
                        this.$bus.$emit('updateList');
                        this.$bus.$emit('updateDashboard');
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
        },
    }
</script>
