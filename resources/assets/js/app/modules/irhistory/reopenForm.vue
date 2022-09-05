<template>
    <div class="tabs-container">
        <div class="tab-content">
            <the-form :asterisk="asterisk" @submitForm="eventUpdate" class="m-t-xl" >
                <div slot="form-body">
                    <table class="table borderless">
                        <tbody>
                        <tr>
                            <td>
                                <strong>IR Number : </strong> {{ reopen.ir_number }}
                            </td>
                            <td>
                                <strong>Date Reported : </strong> {{ reopen_data.date_reported | formatDate }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Complainant : </strong>{{ reopen_data.complainant_user }}
                            </td>
                            <td>
                                <strong>Respondent : </strong> {{ reopen_data.respondent_user }}
                            </td>
                        </tr>
                        <tr v-if="reopen_data.offense">
                            <td>
                                <strong>Provision : </strong> {{ reopen_data.category }}
                            </td>
                            <td>
                                <strong>Gravity : </strong> {{ reopen_data.gravity }}
                            </td>
                        </tr>
                        <tr v-if="reopen_data.offense">
                            <td colspan="2">
                                <strong>Offense : </strong> {{ reopen_data.offense }}
                            </td>
                        </tr>
                        <tr v-for="(off_multi, index) in reopen_data.offense_multiple" :key="index">
                            <td colspan="2">
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
                                        <tr>
                                            <td colspan="2">
                                                <strong v-if="reopen_data.multiple_irr_array[index]">
                                                    Initial Corrective Action: {{ reopen_data.multiple_irr_array[index] }}
                                                </strong><br />
                                                <strong v-if="reopen_data.multiple_instance[index]">
                                                    Initial Instance: {{ reopen_data.multiple_instance[index] }}
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Requestor : </strong>
                                {{ reopen_data.reopen_requestor_name }}
                            </td>
                            <td>
                                <strong>Approver : </strong>
                                {{ reopen_data.reopen_approver_name }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" v-if="reopen_data.reopen_status_id">
                                <strong>Change Status:</strong>
                                <span v-if="reopen_data.reopen_status_id == '2'">In Progress</span>
                                <span v-else-if="reopen_data.reopen_status_id == '3'">On Hold</span>
                            </td>
                            <td colspan="2" v-else>
                                <the-label label="Change Status:" :asterisk="true">
                                    <vSelect
                                        v-model="reopen.status_id"
                                        :options="selectedStatus"
                                        label="text"
                                    />
                                </the-label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" v-if="reopen_data.reopen_reason">
                                <strong>Reason:</strong> <br>
                                <div v-html="reopen_data.reopen_reason" />
                            </td>
                            <td colspan="2" v-else>
                                <the-label label="Reason:" :asterisk="true">
                                    <vue-editor
                                        v-model="reopen.reason"
                                        :editorToolbar="customToolbar"
                                    />
                                </the-label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button-submit :disabled='!isComplete'
                                v-if="reopen_data.reopen_status_id == '' && reopen_data.reopen_reason == ''"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </the-form>
            <button v-if="reopen_data.reopen_status_id != '' && reopen_data.reopen_reason != ''
            && (role == 'Super Admin Access' || role == 'HR Admin Access')"
            class='ladda-button btn btn-success btn-xs'
            @click="eventApprove(reopen.ir_number, reopen.status_id, reopen.respondent_id,
            reopen.ir_id, reopen.approver_id, reopen_data.respondent_employee_no, reopen_data.offense)">
            <i class="fa fa-thumbs-up"></i> Approve</button>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        complainantProps: Object,
    },
    data: function () {
        return {
            role: this.$ep.role,
            user: this.$ep.user,
            laddabtn: '',
            asterisk: true,
            reopen_data: {
                ir_number: '',
                complainant_user: '',
                respondent_user: '',
                date_reported: '',
                category: '',
                gravity: '',
                offense: '',
                reopen_status_id: '',
                reopen_request: '',
                reopen_reason: '',
                reopen_requestor_name: '',
                reopen_approver_name: '',
                respondent_employee_no: '',
                offense_multiple: [],
                multiple_irr_array: [],
                multiple_instance: [],
            },
            selectedStatus: [
                { "value": 2, "text": "In Progress" },
                { "value": 3, "text": "On Hold" },
            ],
            // selectedStatus: this.$constants.Status,
            customToolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
            ],
            reopen: {
                ir_id: '',
                respondent_id: '',
                ir_number: '',
                requestor_id: '',
                approver_id: '',
                status_id: '',
                reason: '',
                request_status: 'Pending',
                respondent_employee_no: '',
            }
        };
    },
    mounted() {
        this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
    },
    created(){
        if(this.complainantProps.res_id){

            this.reopen_data.complainant_user = this.complainantProps.comp_complainant;
            this.reopen_data.respondent_user = this.complainantProps.comp_respondent;
            this.reopen_data.date_reported = this.complainantProps.comp_reported;
            if (this.complainantProps.comp_offense_id) {
                this.reopen_data.category = this.complainantProps.comp_category;
                this.reopen_data.gravity = this.complainantProps.comp_gravity;
                this.reopen_data.offense = this.complainantProps.comp_offense;
            } else if(this.complainantProps.comp_multi_offense_id) {
                let jsonIRRChecker = this.complainantProps.initial_irr_id.replace(/'/g,'').includes('[');
                if (!jsonIRRChecker) {
                    this.$constants.Coc_API.get("/dropdown/irr")
                        .then(response => {
                        this.selectedIRR = response.data;
                        for (let s = 0; s < this.selectedIRR.length; s++) {
                            if (this.selectedIRR[s].value == this.complainantProps.initial_irr_id) {
                                this.reopen_data.initial_irr = response.data[s].text;
                            }
                        }
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else {
                    if (JSON.parse(this.complainantProps.initial_irr_id).length > 0) {
                        let multiple_irr_first = this.complainantProps.initial_irr_id.replace(/\\|"/gi,'');
                        let multiple_irr_second = multiple_irr_first.replace(']','');
                        let multiple_irr_array = multiple_irr_second.replace('[','');
                        this.reopen_data.multiple_irr = multiple_irr_array.split(',');
                    }
                    for(let c=0; c < this.reopen_data.multiple_irr.length; c++){
                        this.findMultipleIrr(this.reopen_data.multiple_irr[c]);
                    }
                }
                let jsonInstanceChecker = this.complainantProps.initial_instance.replace(/'/g,'').includes('[');
                if (!jsonInstanceChecker) {
                    this.reopen_data.initial_instance = this.complainantProps.initial_instance;
                } else {
                    if (JSON.parse(this.complainantProps.initial_instance).length > 0) {
                        this.reopen_data.multiple_instance = JSON.parse(this.complainantProps.initial_instance.replace(/'/g,''));
                    }

                    // for(let c=0; c < this.complainantProps.comp_multi_offense_id.length; c++){
                    //     this.findMultipleOffense(this.complainantProps.comp_multi_offense_id[c]);
                    // }

                    /* get complainant multiple offense */
                    if (this.complainantProps.comp_multi_offense_id) {
                            for (let c in this.complainantProps.comp_multi_offense_id){
                            this.findMultipleOffense(this.complainantProps.comp_multi_offense_id[c]);
                        }
                    }
                }
            }

            this.reopen_data.reopen_requestor_name = this.complainantProps.comp_requestor_name;
            this.reopen_data.reopen_approver_name = this.complainantProps.comp_approver_name;

            if (this.complainantProps.comp_status_id && this.complainantProps.comp_reopen_reason) {
                this.reopen_data.reopen_status_id = this.complainantProps.comp_status_id;
                this.reopen_data.reopen_request = this.complainantProps.comp_reopen_request;
                this.reopen_data.reopen_reason = this.complainantProps.comp_reopen_reason;
                this.reopen_data.respondent_employee_no = this.complainantProps.comp_respondent_employee_no;
                this.asterisk = false;

                console.log("if this.reopen_data==");
                console.log(this.reopen_data);
            }

            if (this.complainantProps.comp_status_id == 4) {
                this.reopen.status_id = '';
            } else {
                this.reopen.status_id = this.complainantProps.comp_status_id;
            }

            this.reopen.ir_id = this.complainantProps.comp_incident_report.id;
            this.reopen.respondent_id = this.complainantProps.res_id;
            this.reopen.ir_number = this.complainantProps.comp_ir_number;
            this.reopen.requestor_id = this.complainantProps.comp_requestor_id;

            if (this.complainantProps.comp_approver_id != this.user.id) {
                this.reopen.approver_id = this.user.id;
            } else if (this.complainantProps.comp_approver_id == null) {
                this.reopen.approver_id = this.user.id;
            } else {
                this.reopen.approver_id = this.complainantProps.comp_approver_id;
            }
        }
    },
    computed: {
        isComplete () {
            return this.reopen.status_id && this.reopen.reason;
        }
    },
   methods: {
        findMultipleOffense(offense_id) {
            // this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
            // .then(response => {
            //     this.reopen_data.offense_multiple.push(response.data);
            // })
            // .catch(error => {
            //     this.globalErrorHandling(error);
            // });

            this.$constants.Coc_API.get("/offense/progression/"+offense_id+"/"+this.complainantProps.comp_respondent_user_id)
            .then(response => {

                let instance_multiple = [];
                let case_closure_multiple = [];
                if (response.data == 'No occurrence') {

                    this.$constants.Coc_API.get("/offense/codeofconduct/" + offense_id)
                    .then(respo => {
                        this.reopen_data.offense_multiple.push(respo.data);
                        this.reopen_data.multiple_instance.push("1st Instance")
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else if (response.data.seventh_respondent_id) {
                    instance_multiple = "7th Instance";
                } else if (response.data.sixth_respondent_id) {
                    instance_multiple = "7th Instance";
                } else if (response.data.fifth_respondent_id) {
                    instance_multiple = "6th Instance";
                } else if (response.data.fourth_respondent_id) {
                    instance_multiple = "5th Instance";
                } else if (response.data.third_respondent_id) {
                    instance_multiple = "4th Instance";
                } else if (response.data.second_respondent_id) {
                    instance_multiple = "3rd Instance";
                } else if (response.data.first_respondent_id) {
                    instance_multiple = "2nd Instance";
                }

                if (response.data.offense.offense) {
                    case_closure_multiple = response.data.offense;
                }

                this.reopen_data.multiple_instance.push(instance_multiple)
                this.reopen_data.offense_multiple.push(case_closure_multiple);
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        findMultipleIrr: function (irr_id) {
            this.$constants.Coc_API.get("/dropdown/irr")
                .then(response => {
                this.selectedIRR = response.data;
                for (let s = 0; s < this.selectedIRR.length; s++) {
                    if (this.selectedIRR[s].value == irr_id) {
                        this.reopen_data.multiple_irr_array.push(response.data[s].text);
                    }
                }
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        eventUpdate() {
            this.laddabtn.start();
            let reopen = this.reopen;
            let getAxios = '';

            getAxios = this.$constants.Admin_API.get("/irhistory/reopen/create", {
                params: {
                    ir_id: reopen.ir_id,
                    respondent_id: reopen.respondent_id,
                    ir_number: reopen.ir_number,
                    requestor_id: reopen.requestor_id,
                    approver_id: reopen.approver_id,
                    status_id: reopen.status_id.value,
                    reason: reopen.reason,
                    request_status: reopen.request_status,
                }
            });

            getAxios.then(response => {
                this.laddabtn.stop();
                this.$bus.$emit('init_modal', false);
                this.reopen = response.data;
                this.$success('Successfully Saved!')
                location.reload();
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        eventApprove: function (ir_number, status_id, respondent_id, ir_id, approver_id, respondent_employee_no, offense) {
            console.log("respondent_employee_no==", respondent_employee_no);
            console.log("offense==", offense);
            this.laddabtn ='';
            this.$swal({
                title: 'Are you sure you want to approve reopen IR No: #' + ir_number + '?',
                text: "Once approved, you will not be able to revert this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'No, Cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
            })
                .then((result) => {
                    if (result.value) {
                        this.$constants.Admin_API.get("/irhistory/reopen/approval/" + respondent_id, {
                            params: {
                                status_id: status_id,
                                ir_id: ir_id,
                                approver_id: approver_id,
                                respondent_id: respondent_id,
                                respondent_employee_no: respondent_employee_no,
                                offense: offense,
                            }
                        }).then(response => {
                            response.data
                            this.$bus.$emit('init_modal', false);
                            // this.$bus.$emit('updateList');
                            this.$success('#' + ir_number + ' has been approved!')
                            location.reload();
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$bus.$emit('init_modal', false);
                        this.$bus.$emit('updateList');
                        this.$failure(ir_number + ' has been cancelled')
                    }
                })
        },
   }
}
</script>
