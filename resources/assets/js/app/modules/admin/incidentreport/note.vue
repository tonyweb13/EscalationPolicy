<template>
    <div>
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active" v-if="note.complainant_user_id">
                    <a data-toggle="tab" href="#tab-complainant" aria-expanded="true">Complainant</a>
                </li>
                <li><a data-toggle="tab" href="#tab-respondent" aria-expanded="false">Respondent</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-complainant" class="tab-pane active">
                    <div class="chat-discussion m-b-lg">
                        <div v-for="ret in retrieve" v-if="note.complainant_user_id">
                            <div class="chat-message left"
                                    v-if="ret.noted_by == 'complainant'
                                    && ret.reply_to == 'hr_bp'"
                                    >
                                <div class="message" style="margin-left:-10px;">
                                    <label class="message-author">
                                        {{ ret.complainant_user.first_name }}
                                        {{ ret.complainant_user.last_name }}
                                    </label>
                                    <span class="message-date">
                                        {{ ret.created_at }}
                                    </span>
                                    <span class="message-content" v-html="ret.notes" />
                                </div>
                            </div>
                            <div class="chat-message right"
                                v-if="ret.noted_by == 'hr_bp'
                                && ret.reply_to == 'complainant'"
                                >
                                <div class="message"  style="margin-left:-10px; margin-right:0px;">
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
                        @submit.prevent="eventNote('complainant')" v-if="note.complainant_user_id">
                        <div slot="form-body">

                        <the-label label="Reply to Complainant:">
                            <vue-editor v-model="note.notes" :editorToolbar="customToolbar"/>
                        </the-label>

                        <button-save :disabled='!isComplete'/>
                        </div>
                        </form>
                </div>
                <div class="clearfix"></div>

                <div id="tab-respondent" class="tab-pane">
                    <div class="chat-discussion m-b-lg">
                        <div v-for="ret in retrieve">
                            <div class="chat-message left"
                                v-if="ret.noted_by == 'respondent'
                                && ret.reply_to == 'hr_bp'"
                                >
                                <div class="message" style="margin-left:-10px;">
                                <label class="message-author">
                                    {{ ret.respondent_user.first_name }}
                                    {{ ret.respondent_user.last_name }}
                                </label>
                                <span class="message-date">
                                    {{ ret.created_at }}
                                </span>
                                <span class="message-content" v-html="ret.notes" />
                                </div>
                            </div>
                            <div class="chat-message right"
                                v-if="ret.noted_by == 'hr_bp'
                                && ret.reply_to == 'respondent'"
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
                    @submit.prevent="eventNote('respondent')" >
                        <div slot="form-body">

                        <the-label label="Reply to Respondent:">
                            <vue-editor v-model="note.notes" :editorToolbar="customToolbar"/>
                        </the-label>

                        <button-save :disabled='!isComplete'/>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

    </div>
</template>
<script>
    export default {
        props: {
            noteProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                note: {
                    complainant_id: 0,
                    complainant_user_id: 0,
                    respondent_user_id: this.noteProps.comp_respondent_user_id,
                    reply_to: '',
                    notes: '',
                    ir_number: '',
                },
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                selectedReplyTo: [
                    'complainant',
                    'respondent'
                ],
                retrieve: {
                    complainant_id: '',
                    complainant_user_id: '',
                    respondent_user_id: '',
                    noted_by: '',
                    notes: '',
                    created_at: '',
                }
            };
        },
        created() {
            if (this.noteProps && this.noteProps.comp_complainant_id && this.noteProps.comp_complainant_user_id) {

            console.log("created this.noteProps==")
            console.log(this.noteProps)

            console.log("created this.noteProps.comp_complainant_user_id==")
            console.log(this.noteProps.comp_complainant_user_id)

                this.note.complainant_id =  this.noteProps.comp_complainant_id;
                this.note.complainant_user_id = this.noteProps.comp_complainant_user_id;

                this.note.ir_number = this.noteProps.comp_ir_number;

                this.$constants.Default_API.get("/notes/retrieve/"
                    + this.noteProps.comp_complainant_id
                    + "/" + this.noteProps.comp_complainant_user_id
                    + "/" + this.noteProps.comp_respondent_user_id)
                    .then(response => {
                        this.retrieve = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.note.notes;
            }
        },
        methods: {
            eventNote(reply_to) {
                this.laddabtn.start();
                let note = this.note;

                this.$constants.Default_API.get("/notes/create", {
                    params: {
                        complainant_id: note.complainant_id,
                        complainant_user_id: note.complainant_user_id,
                        respondent_user_id: note.respondent_user_id,
                        notes: note.notes,
                        reply_to: reply_to,
                        noted_by: 'hr_bp',
                        ir_number: note.ir_number,
                    },
                }).then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.note = response.data;
                    this.$success('Successfully Saved!')
                    this.$bus.$emit('updateList');
                    this.$bus.$emit('updateDashboard');
                    this.$bus.$emit('updateNotes');

                    // if (this.note.reply_to == "complainant") {
                    //     this.$constants.Default_API.get("/mail/note/complainant/"
                    //         + this.note.ir_number
                    //         + "/" + this.note.complainant_user_id
                    //     )
                    //     .then(response => {
                    //         return response.data
                    //     })
                    //     .catch(error => {
                    //         this.globalErrorHandling(error);
                    //     });

                    // } else if (this.note.reply_to == "respondent") {

                    //     this.$constants.Default_API.get("/mail/note/respondent/"
                    //         + this.note.ir_number
                    //         + "/" + this.note.respondent_user_id
                    //     )
                    //     .then(response => {
                    //         console.log("send note respondent!");
                    //         return response.data
                    //     })
                    //     .catch(error => {
                    //         this.globalErrorHandling(error);
                    //     });
                    // }
                })
            },
        },
    }
</script>
