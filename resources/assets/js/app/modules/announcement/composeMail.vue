<template>
    <the-form :asterisk="true" @submitForm="eventUpdate">
        <div slot="form-body">
            <input-text v-model="announcement.from" label="From" :disabled="true" />

            <the-label label="To" :asterisk="true">
                <vSelect
                    multiple
                    v-model="announcement.to"
                    :options="selectedTo"
                    label="text"
                />
            </the-label>

            <the-label label="Distro" :asterisk="true">
                <vSelect
                    multiple
                    v-model="announcement.distro"
                    :options="selectedDistro"
                    label="text"
                />
            </the-label>

            <input-text v-model="announcement.subject" label="Subject" :asterisk="true" />

            <the-label label="Content" :asterisk="true">
                <vue-editor :editorToolbar="customToolbar" v-model="announcement.content" />
            </the-label>

            <input-file-uploader label="Attachments" @change="attachFile" />

            <the-label label="Effective Date" :asterisk="true">
            <datepicker
                :input-class="'custom-datepicker'"
                :format="'yyyy-MM-dd'"
                :calendar-button="true"
                :calendar-button-icon="'fa fa-calendar'"
                :use-utc="true"
                placeholder="YYYY-MM-DD"
                :disabledDates="disabledDates"
                v-model="announcement.effective_date"
            />
            </the-label>

            <button-custom :disabled='!isComplete' :name='"Send"' :icon='"fa fa-send"' />
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            announcementProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                announcement: {
                    sender_id: this.$ep.user.id,
                    from: this.$ep.user.first_name + " " + this.$ep.user.last_name,
                    to: '',
                    to_multiple: [],
                    subject: '',
                    content: '',
                    attachments: [],
                    distro: '',
                    distro_multiple: [],
                    effective_date: '',
                },
                selectedTo: [],
                selectedDistro: [],
                form: new FormData,
                customToolbar: [
                    [{ 'font': [] }],
                    [{ 'header': [false, 1, 2, 3, 4, 5, 6, ] }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{'align': ''}, {'align': 'center'}, {'align': 'right'}, {'align': 'justify'}],
                    [{ 'header': 1 }, { 'header': 2 }],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'video', 'formula'],
                    [{ 'direction': 'rtl' }],
                    ['clean'],
                ],
                disabledDates: {
                    to: new Date(Date.now() - 8640000)
                },
            };
        },
        created(){
            this.eventEmailTo();
            this.eventDistro();
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.announcement.to) {
                    return this.announcement.to && this.announcement.subject
                    && this.announcement.content && this.announcement.effective_date;
                } else if (this.announcement.distro) {
                    return this.announcement.distro && this.announcement.subject
                    && this.announcement.content && this.announcement.effective_date;
                }
            },
        },
        methods: {
            eventUpdate() {
                for(let r=0; r<this.announcement.to.length; r++){
                    this.announcement.to_multiple.push(this.announcement.to[r].value);
                }

                for(let r=0; r<this.announcement.distro.length; r++){
                    this.announcement.distro_multiple.push(this.announcement.distro[r].value);
                }

                for(let i=0; i<this.announcement.attachments.length;i++){
                    this.form.append('pics[]', this.announcement.attachments[i]);
                }

                const config = { headers: { 'Content-Type': 'multipart/form-data' } }

                this.laddabtn.start();
                let announcement = this.announcement;

                this.$constants.Default_API.post('/announcement/attach/file', this.form, config)
                .then(response => {
                    announcement.attachments = response.data.uploaded

                    let announce_store = {
                        constants: this.$constants.Default_API,
                        laddabtn: this.laddabtn,
                        bus: this.$bus,
                        success: this.$success,
                        globalError: this.globalErrorHandling,
                        passing: {
                            params: {
                                sender_id: this.$ep.user.id,
                                to: announcement.to_multiple,
                                distro: announcement.distro_multiple,
                                subject: announcement.subject,
                                content: announcement.content,
                                attachments: announcement.attachments,
                                effective_date: announcement.effective_date,
                            }
                        }
                    }

                    this.$store.dispatch('createAnnounce', announce_store)
                })
                .catch(error => {
                   this.globalErrorHandling(error);
                });
            },
            attachFile(e){
                let selectedFiles = e.target.files;

                if(!selectedFiles.length){
                    return false;
                }

                for(let i=0; i<selectedFiles.length;i++){
                    this.announcement.attachments.push(selectedFiles[i]);
                }
            },
            eventEmailTo() {
                this.$constants.Default_API.get("/dropdown/users/email")
                    .then(response => {
                    this.selectedTo = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventDistro() {
                this.$constants.Default_API.get("/announcement/dropdown/distro")
                    .then(response => {
                    this.selectedDistro = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        },
    }
</script>
