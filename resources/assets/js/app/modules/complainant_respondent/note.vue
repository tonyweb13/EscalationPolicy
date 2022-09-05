<template>
    <div>
        <label>Message Box</label>
        <div class="chat-discussion m-b-lg">
            <div v-for="ret in retrieve">

                <!-- complainant -->
                <div class="chat-message left"
                    v-if="ret.complainant_user_id == current_user
                    && ret.noted_by == 'complainant'
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
                    v-if="ret.complainant_user_id == current_user
                    && ret.noted_by == 'hr_bp'
                    && ret.reply_to == 'complainant'"
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

                <!-- respondent -->
                <div class="chat-message left"
                    v-if="ret.respondent_user_id == current_user
                    && ret.noted_by == 'respondent'
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
                    v-if="ret.respondent_user_id == current_user
                    && ret.noted_by == 'hr_bp'
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

                <!-- invite -->
                <div class="chat-message left"
                    v-if="ret.invite_user_id == current_user
                    && ret.noted_by == 'invite'
                    && ret.reply_to == 'hr_bp'"
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
                    v-if="ret.invite_user_id == current_user
                    && ret.noted_by == 'hr_bp'
                    && ret.reply_to == 'invite'"
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
            @submit.prevent="eventNote()" >
            <div slot="form-body">
                <the-label label="Reply to HR:" >
                    <vue-editor v-model="note.notes" :editorToolbar="customToolbar"/>
                </the-label>

                <button-save :disabled='!isComplete'/>
            </div>
        </form>
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
                    complainant_id: this.noteProps.comp_complainant_id,
                    complainant_user_id: this.noteProps.comp_complainant_user_id,
                    respondent_user_id: this.noteProps.comp_respondent_user_id,
                    noted_by: this.noteProps.comp_noted_by,
                    notes: '',
                    ir_number: '',
                    invite_user_id: '',
                    hr_user_id: '',
                    // invite_user: [],
                },
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                retrieve: {
                    complainant_id: '',
                    complainant_user_id: '',
                    respondent_user_id: '',
                    invite_user_id: '',
                    hr_user_id: '',
                    noted_by: '',
                    notes: '',
                    created_at: '',
                },
                current_user: this.$ep.user.id,
                commit: {}
            };
        },
        created() {
            this.note.ir_number = this.noteProps.comp_ir_number;

            if (this.noteProps.comp_hr_user_id) {
                this.note.hr_user_id = this.noteProps.comp_hr_user_id;
            }

            if (this.noteProps.comp_invite_user_id) {
                this.note.invite_user_id = this.noteProps.comp_invite_user_id;
            }

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
            eventNote() {
                this.laddabtn.start();
                let note = this.note;

                this.$constants.Default_API.get("/notes/create", {
                    params: {
                        complainant_id: note.complainant_id,
                        complainant_user_id: note.complainant_user_id,
                        respondent_user_id: note.respondent_user_id,
                        invite_user_id: note.invite_user_id,
                        hr_user_id: note.hr_user_id,
                        notes: note.notes,
                        noted_by: note.noted_by,
                        ir_number: note.ir_number,
                    },
                }).then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.note = response.data;
                    this.$success('Successfully Saved!')
                    this.$bus.$emit('updateList');
                    this.$bus.$emit('updateNotes');
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            // eventNote() {
            //     this.laddabtn.start();
            //     let note = this.note;

            //     let get_required_params = {
            //         constants: this.$constants.Default_API,
            //         laddabtn: this.laddabtn,
            //         bus: this.$bus,
            //         success: this.$success,
            //         globalError: this.globalErrorHandling,
            //         passing: {
            //             params: {
            //                 complainant_id: note.complainant_id,
            //                 complainant_user_id: note.complainant_user_id,
            //                 respondent_user_id: note.respondent_user_id,
            //                 invite_user_id: note.invite_user_id,
            //                 hr_user_id: note.hr_user_id,
            //                 notes: note.notes,
            //                 noted_by: note.noted_by,
            //                 ir_number: note.ir_number,
            //             }
            //         }
            //     }

            //     this.$store.dispatch('createNote', get_required_params)
            // },
        },
    }
</script>
