
<template>
    <table class="table borderless">
        <tbody>
        <tr v-if="coaching_log.cl_number || (coaching_log.date_start && coaching_log.date_end)">
            <td colspan="2">
                <table class="table borderless">
                    <tr>
                        <td v-if="coaching_log.cl_number">
                            <strong>Coaching Number : </strong> {{ coaching_log.cl_number }}
                        </td>
                        <td v-if="coaching_log.date_start && coaching_log.date_end">
                            <strong>Date Reported : </strong> {{ coaching_log.date_start | formatDate }} -
                            {{ coaching_log.date_end | formatDate }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td v-if="coaching_log.offense_multiple.length > 0" colspan="2">
                <strong>Offense : </strong>

                <div v-for="(off_multi, index) in coaching_log.offense_multiple" :key="index">
                    <fieldset class="fieldsetCustom">
                        <legend class="legendCustom">Offense ( {{ index+1 }} )</legend>
                        <table class="table borderless">
                            <tr>
                                <td colspan="2">
                                    <strong>Provision : </strong> {{ off_multi.category.name }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>Gravity : </strong> {{ off_multi.gravity.gravity }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>Prescriptive Period : </strong> {{ off_multi.gravity.prescriptive_period }}
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
            </td>
            <td v-else-if="coaching_log.offense_single.length > 0" colspan="2">
                <fieldset class="fieldsetCustom">
                    <legend class="legendCustom">Offense :</legend>
                    <table class="table borderless">
                        <tr>
                            <td colspan="2">
                                <strong>Provision : </strong> {{ coaching_log.offense_single[0].category.name }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Gravity : </strong> {{ coaching_log.offense_single[0].gravity.gravity }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Prescriptive Period : </strong> {{ coaching_log.offense_single[0].gravity.prescriptive_period }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Offense : </strong>
                                {{ coaching_log.offense_single[0].offense }}
                                <strong style="color:red;" v-if="coaching_log.offense_single[0].offense_archived">
                                    {{ coaching_log.offense_single[0].offense_archived }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            <td v-else-if="coaching_log.others" colspan="2">
                <fieldset class="fieldsetCustom">
                    <legend class="legendCustom">Others</legend>
                    <table class="table borderless">
                        <tr>
                            <td colspan="2">
                                <strong>Provision : </strong> Others
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                {{ coaching_log.others }}
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            <td v-else colspan="2">
                <strong>
                    No Offense Selected / Old Record
                </strong>
            </td>
        </tr>
        <tr v-if="coaching_log.number_occurrence && coaching_log.status">
            <td v-if="coaching_log.status == 1">
                <strong>Status : </strong>
                <span style="color:red">
                    Invalid
                </span>
            </td>
            <td v-else></td>
            <td>
                <strong>Occurrences : </strong>
                <span >
                    {{ coaching_log.number_occurrence }}
                </span>
            </td>
        </tr>
        <tr >
            <td>
                <div>
                    <strong>Findings / Coaching Opportunities/Areas of Improvement : </strong><br />
                    {{ coaching_log.findings }}
                </div>
            </td>
        </tr>

        <tr >
            <td>
                <div>
                    <strong>Point of Discussion/ Coaching Details : </strong><br />
                    {{ coaching_log.point_of_disscussion }}
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div >
                    <strong>Action Plans : </strong> <br />
                    {{ coaching_log.action_plans }}
                </div>
            </td>
        </tr>

        <tr >
            <td>
                <div >
                    <strong>Agent’s Commitment : </strong> <br />
                    {{ coaching_log.agents_commitment }}
                </div>
            </td>
        </tr>

        <tr >
            <td>
                <div >
                    <strong>Supervisor’s Commitment : </strong> <br />
                    {{ coaching_log.supervisors_commitment }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button class="ladda-button btn btn-primary" data-style="slide-right" data-color="mint" type="submit"
                @click="invalidCoaching(coaching_log, 1)"
                v-if="coaching_log.status != 1">

                    <i class="fa fa-thumbs-up"/> Invalid Coaching
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</template>
<style>
    td {
        /* These are technically the same, but use both */
        overflow-wrap: break-word;
        word-wrap: break-word;

        -ms-word-break: break-all;
        /* This is the dangerous one in WebKit, as it breaks things wherever */
        word-break: break-all;
        /* Instead use this non-standard one: */
        word-break: break-word;

        /* Adds a hyphen where the word breaks, if supported (No Blink) */
        -ms-hyphens: auto;
        -moz-hyphens: auto;
        -webkit-hyphens: auto;
        hyphens: auto;
    }
</style>
<script>
    export default {
        props: {
            coachingLogProps: Object,
        },

        data: function () {
            return {
    			coaching_log: {
                    id: '',
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
                    offense_single: [],
                    offense_multiple: [],
                    others: '',
                },
                offense_remove: [],
                offense_index: [],
            };
        },

        created() {
            this.coaching_log.offense_id = this.coachingLogProps.offense_id;


            if (this.coaching_log.offense_id) {
                let offense_ids = this.coaching_log.offense_id.replace('[', '');
                offense_ids = offense_ids.replace(']', '');
                offense_ids = offense_ids.replaceAll('"', '');
                let offense_multiple = offense_ids.split(',');
                if (this.coaching_log.offense_id.charAt(0) == '[') {
                    for(let c=0; c < offense_multiple.length; c++){
                       this.findMultipleOffense(offense_multiple[c]);
                    }
                } else {
                    this.findSingleOffense(this.coaching_log.offense_id);
                }
            }

            this.coaching_log.id = this.coachingLogProps.id;
            this.coaching_log.findings = this.coachingLogProps.findings;
            this.coaching_log.point_of_disscussion = this.coachingLogProps.point_of_disscussion;
            this.coaching_log.action_plans = this.coachingLogProps.action_plans;
            this.coaching_log.agents_commitment = this.coachingLogProps.agents_commitment;
            this.coaching_log.supervisors_commitment = this.coachingLogProps.supervisors_commitment;
            this.coaching_log.cl_number = this.coachingLogProps.cl_number;
            this.coaching_log.date_start = this.coachingLogProps.date_start;
            this.coaching_log.date_end = this.coachingLogProps.date_end;
            this.coaching_log.number_occurrence = this.coachingLogProps.number_occurrence;
            this.coaching_log.others = this.coachingLogProps.others;
            this.coaching_log.status = this.coachingLogProps.status;
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        methods: {
            invalidCoaching: function (getCoaching, approverDisapprove) {
                this.laddabtn.start();
                this.$swal({
                    title: 'Are you sure you want to Invalid Coaching Number: '+ getCoaching.cl_number +'?',
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            this.$constants.Coaching_API.get("/invalid/"+getCoaching.id)
                                .then(response => {
                                    this.laddabtn.stop();
                                    this.$bus.$emit('init_modal', false);
                                    return response.data;
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.laddabtn.stop();
                            this.$failure('Coaching Number:' + getCoaching.cl_number + ' Invalid has been cancelled!')
                        }
                    })
            },
            findMultipleOffense: function (offense_id) {
                this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                .then(response => {
                    this.coaching_log.offense_multiple.push(response.data);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            findSingleOffense: function (offense_id) {
                this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                .then(response => {
                    this.coaching_log.offense_single.push(response.data);
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
