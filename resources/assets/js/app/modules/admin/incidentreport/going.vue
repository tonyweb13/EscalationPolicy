<template>
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#tab-approval-status" aria-expanded="true">Approval Status</a>
            </li>
            <li v-for="inv_hearing in going.invite_hearing">
                <a data-toggle="tab" :href="'#tab-' + inv_hearing.invite_ir_fullname.employee_no"
                aria-expanded="false">
                    Note to {{ inv_hearing.invite_ir_fullname.first_name }}
                </a>
            </li>
        </ul>
        <div class="tab-content">

            <div id="tab-approval-status" class="tab-pane active">
                <br>
                <small><i>To follow up HR Invite/Respondent, please leave a note using the <b>"Note"</b> option
                and use the <b>"Edit"</b> option to update the Date of Admin Hearing</i></small><br><br>

                <!-- respondent -->
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td colspan="2">
                            <label>Respondent: </label> {{ going.respondent_name }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <hr>

                <!-- invites-->
                <table v-for="inv_hearing in going.invite_hearing"
                        v-if="inv_hearing.requestor_user_id"
                        class="table table-striped">

                    <tbody>
                    <tr>
                        <td>
                            <label>HR Invite Approval Status:  </label>
                        </td>
                        <td  >
                            <div>
                                <b>{{ inv_hearing.hr_approval }}</b><br>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>HR Invite: </label>
                            {{ inv_hearing.invite_ir_fullname.first_name }}
                            {{ inv_hearing.invite_ir_fullname.last_name }}
                        </td>
                        <td v-if="inv_hearing.going">
                            <label>Going : </label>
                            <i>{{ inv_hearing.going }} </i>
                            <span v-if="inv_hearing.accepted_date"> - ({{ frontEndDateFormat(inv_hearing.accepted_date) }})</span>
                        </td>
                    </tr>

                    <tr>

                        <td colspan="2" v-if="inv_hearing.going == 'No'
                        || inv_hearing.going == 'Pending'">

                            <label>Reason : </label>
                            <div v-html="inv_hearing.reason"/>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- Invite Not Seen -->
                <table v-if="going.invite_hearing && going.invite_hearing.length == '0'"
                    v-for="inv_user in going.invite_user"
                    class="table table-striped">
                    <tbody>
                    <tr>
                        <td>
                            <label>HR Invite: </label>
                            {{ inv_user.invite_fullname.first_name }}
                            {{ inv_user.invite_fullname.last_name }}
                        </td>
                        <td>
                            <label>Going : </label>
                            <h4 style="display: inline;"><i>Not Confirm</i></h4>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <hr>

            </div>
            <div class="clearfix"></div>

            <div v-for="inv_hearing in going.invite_hearing"
                :id="'tab-' + inv_hearing.invite_ir_fullname.employee_no"
                class="tab-pane">

                <div class="chat-discussion m-b-lg">
                    <div v-for="ret in retrieve">
                        <div class="chat-message left"
                            v-if="ret.noted_by == 'invite'
                            && ret.reply_to == 'hr_bp'
                            && ret.invite_user_id == inv_hearing.invite_ir_fullname.id"
                            >
                            <div class="message" style="margin-left:-10px;">
                            <label class="message-author">
                                {{ ret.invite_user.first_name }}
                                {{ ret.invite_user.last_name }}
                            </label>
                            <span class="message-date">
                                {{ ret.created_at }}
                            </span>
                            <span class="message-content" v-html="ret.notes" />
                            </div>
                        </div>
                        <div class="chat-message right"
                            v-if="ret.noted_by == 'hr_bp'
                            && ret.reply_to == 'invite'
                            && ret.invite_user_id == inv_hearing.invite_ir_fullname.id"
                            >
                            <div class="message" style="margin-left:-10px; margin-right:0px;">
                            <label class="message-author">
                                {{ ret.hr_user.first_name }}
                                {{ ret.hr_user.last_name }}
                            </label>
                            <span class="message-date">
                                {{ ret.created_at }}
                            </span>
                            <span class="message-content" v-html="ret.notes" />
                            </div>
                        </div>
                    </div>
                </div>
                <form enctype="multipart/form-data"
                    @submit.prevent="eventNote('invite', inv_hearing.invite_ir_fullname.id)" >

                    <div slot="form-body">

                    <the-label :label="'Reply to HR Invite: ' + inv_hearing.invite_ir_fullname.first_name">
                        <vue-editor v-model="note.notes" :editorToolbar="customToolbar"/>
                    </the-label>

                    <button-save :disabled='!isComplete'/>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>

        </div>
    </div>
</template>
<script>
    export default {
        props: {
            goingProps: Object,
        },
        data: function () {
            return {
                // laddabtn : '',
                going: {
                    respondent_id: this.goingProps.respondent_id,
                    respondent_ir_id: this.goingProps.respondent_ir_id,
                    respondent_user_id: this.goingProps.respondent_user_id,
                    respondent_name: this.goingProps.respondent_name,
                    respondent_going: this.goingProps.respondent_going,
                    respondent_reason: this.goingProps.respondent_reason,
                    respondent_hr_approval: this.goingProps.respondent_hr_approval,
                    accepted_date: this.goingProps.accepted_date,

                    invite_user: [],
                    invite_hearing: [],
                    invite_going: '',
                    invite_user_id: [],
                    hearing_user_id: [],
                    not_confirm: '',

                    complainant_id: this.goingProps.complainant_id,
                    complainant_user_id: this.goingProps.complainant_user_id,
                },
                note: {
                    complainant_id: this.goingProps.complainant_id,
                    complainant_user_id: this.goingProps.complainant_user_id,
                    respondent_user_id: this.goingProps.respondent_user_id,
                    reply_to: '',
                    notes: '',
                    ir_number: this.goingProps.ir_number,
                },
                retrieve: {
                    complainant_id: '',
                    complainant_user_id: '',
                    respondent_user_id: '',
                    noted_by: '',
                    notes: '',
                    created_at: '',
                },
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
            };
        },
        created() {
            if (this.goingProps.list_invites_user) {
                for(let i=0; i<this.goingProps.list_invites_user.length; i++){
                    this.going.invite_user.push(this.goingProps.list_invites_user[i]);
                }
            }

            if (this.goingProps.list_invites_hearing) {
                for(let i=0; i<this.goingProps.list_invites_hearing.length; i++){
                    this.going.invite_hearing.push(this.goingProps.list_invites_hearing[i]);

                    if(this.goingProps.list_invites_hearing[i].going == 'Yes') {
                        this.going.invite_going = this.goingProps.list_invites_hearing[i].going;
                    }
                }
            }

            this.$constants.Default_API.get("/notes/retrieve/"
                + this.note.complainant_id
                + "/" + this.note.complainant_user_id
                + "/" + this.note.respondent_user_id)
                .then(response => {
                    this.retrieve = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        computed: {
            isComplete () {
                return this.note.notes;
            }
        },
        methods: {
            frontEndDateFormat: function(date) {
                return moment.utc(date, 'YYYY-MM-DD').format("dddd MMMM DD, Y");
        	},
            eventNote(reply_to, get_invite_user_id) {
                let note = this.note;

                this.$constants.Default_API.get("/notes/create", {
                    params: {
                        complainant_id: note.complainant_id,
                        complainant_user_id: note.complainant_user_id,
                        respondent_user_id: note.respondent_user_id,
                        invite_user_id: get_invite_user_id,
                        notes: note.notes,
                        reply_to: reply_to,
                        noted_by: 'hr_bp',
                        ir_number: note.ir_number,
                    },
                }).then(response => {
                    this.$bus.$emit('init_modal', false);
                    this.note = response.data;
                    this.$success('Successfully Saved!');
                    this.$bus.$emit('updateDashboard');
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
