<template>
    <div class="tabs-container">
        <div class="tab-content">
            <the-form :asterisk="asterisk" @submitForm="eventUpdate" class="m-t-xl" >
                <div slot="form-body">
                    <table class="table borderless">
                        <tbody>
                        <tr>
                            <td>
                                <strong>IR Numbers : </strong> {{ request_data.ir_number }}
                            </td>
                            <td>
                                <strong>Date Reported : </strong> {{ request_data.date_reported | formatDate }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Complainant : </strong>{{ request_data.complainant_user }}
                            </td>
                            <td>
                                <strong>Respondent : </strong> {{ request_data.respondent_user }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Provision : </strong> {{ request_data.category }}
                            </td>
                            <td>
                                <strong>Gravity : </strong> {{ request_data.gravity }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Offense : </strong> {{ request_data.offense }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Requestor : </strong>
                                {{ request_data.request_requestor_name }}
                            </td>
                            <td>
                                <strong>Approver : </strong>
                                {{ request_data.request_approver_name }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" v-if="request_data.request_reason != ''">
                                <strong>Reason:</strong> <br>
                                <span v-html="request_data.request_reason"></span>
                            </td>
                            <td colspan="2" v-if="request_data.request_reason == ''">
                                <the-label label="Reason:" :asterisk="true">
                                    <vue-editor
                                        v-model="request_data.reason"
                                        :editorToolbar="customToolbar"
                                    />
                                </the-label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button-submit :disabled='!isComplete'
                                v-if="request_data.request_reason == ''"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </the-form>
            <button v-if="request_data.request_reason != ''"
                class='btn btn-success btn-xs'
                @click="eventApprove(request_data.ir_number, request_data.respondent_id,
                    request_data.ir_id, request_data.approver_id, request_data.id)">
                <i class="fa fa-thumbs-up"></i> Approve
            </button>
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
            request_data: {
                id: '',
                ir_number: '',
                complainant_user: '',
                respondent_user: '',
                date_reported: '',
                category: '',
                gravity: '',
                offense: '',
                request_edit: '',
                request_reason: '',
                request_requestor_name: '',
                request_approver_name: '',
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
            request: {
                ir_id: '',
                respondent_id: '',
                ir_number: '',
                requestor_id: '',
                approver_id: '',
                status_id: '',
                reason: '',
                request_status: 'Pending',
            }
        };
    },
    mounted() {
        if (this.request_data.request_edit != "Pending") {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        } else {
            this.laddabtn ='';
        }
    },
    created(){
        console.log(this.complainantProps);
        if(this.complainantProps.res_id){
            this.request_data.complainant_user = this.complainantProps.comp_complainant;
            this.request_data.respondent_user = this.complainantProps.comp_respondent;
            this.request_data.date_reported = this.complainantProps.comp_reported;
            this.request_data.category = this.complainantProps.comp_category;
            this.request_data.gravity = this.complainantProps.comp_gravity;
            this.request_data.offense = this.complainantProps.comp_offense;

            this.request_data.request_requestor_name = this.complainantProps.comp_requestor_name;
            this.request_data.request_approver_name = this.complainantProps.comp_approver_name;

            if (this.complainantProps.comp_request_reason) {
                this.request_data.request_edit = this.complainantProps.comp_request_edit;
                this.request_data.request_reason = this.complainantProps.comp_request_reason;
                this.asterisk = false;

                console.log("if this.request_data==");
                console.log(this.request_data);
            }

            this.request_data.ir_id = this.complainantProps.comp_incident_report.id;
            this.request_data.respondent_id = this.complainantProps.res_id;
            this.request_data.ir_number = this.complainantProps.comp_ir_number;
            this.request_data.requestor_id = this.complainantProps.comp_requestor_id;
            this.request_data.id = this.complainantProps.comp_request_id;
            this.request_data.approver_id = this.complainantProps.comp_approver_id;
        }
    },
    computed: {
        isComplete () {
            return this.request_data.reason;
        }
    },
   methods: {
        eventUpdate() {
            this.laddabtn.start();
            let request_data = this.request_data;
            let getAxios = '';

            getAxios = this.$constants.Incident_API.get("/onhold_request", {
                params: {
                    ir_id: request_data.ir_id,
                    respondent_id: request_data.respondent_id,
                    ir_number: request_data.ir_number,
                    requestor_id: request_data.requestor_id,
                    approver_id: request_data.approver_id,
                    reason: request_data.reason,
                    request_status: request_data.request_status,
                }
            });

            getAxios.then(response => {
                this.laddabtn.stop();
                this.$bus.$emit('init_modal', false);
                this.request_data = response.data;
                this.$success('Successfully Saved!')
                // this.$bus.$emit('updateList');
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        eventApprove: function (ir_number, respondent_id, ir_id, approver_id, id) {
            // console.log();
            this.laddabtn ='';
            this.$swal({
                title: 'Are you sure you want to approve to Edit IR No.' + ir_number + ' ?',
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
                        this.$constants.Incident_API.get("/onhold_request/approval/" + respondent_id, {
                            params: {
                                id: id,
                                ir_id: ir_id,
                                approver_id: approver_id
                            }
                        }).then(response => {
                            response.data
                            this.$bus.$emit('init_modal', false);
                            this.$bus.$emit('updateList');
                            this.$success(ir_number + ' has been approved!')
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
