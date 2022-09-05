<template>
    <table class="table table-borderless">
        <tbody>
        <tr>
            <td colspan="2">
                Creator : <strong>{{ announce_view.creator }}</strong>
            </td>
        </tr>
        <tr>
            <td class="col-lg-6">
                On : <strong>{{ announce_view.date_created | formatDateCompleteMonth }}</strong>
            </td>
            <td class="col-lg-6" v-if="announce_view.effective_date">
                Effective : <strong>{{ announce_view.effective_date | formatDateCompleteMonth }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td align="justify" colspan="2">
                <div v-html="announce_view.content" />
            </td>
        </tr>
        <tr v-if="announce_view.attachments">
            <td v-if="announce_view.attachments">
                <div v-for="attach1 in announce_view.attachments">
                    <strong v-if="attach1.attachments != 'null'">Download Attachment:</strong>
                    <ul v-for="attach2 in attach1.attachments.split(',')">
                        <li v-if="attach2.match(/announcement/g)">
                            <a :href="'/api/admin/incidentreport/download/attachment/'+ attach2 | urlReplace">
                                {{ attach2 | urlReplace }}
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input-checkbox v-model="announce_view.acknowledge" :checked="isChecked" @change="eventChecked()"
                label="I hereby acknowledge that I have read, understand and agree to the terms of this document"
                :disabled="isDisabled" />
            </td>
        </tr>
        </tbody>
    </table>
</template>
<script>
    export default {
        props: {
            announcementProps: Object,
        },
        data: function () {
            return {
                isChecked: false,
                isDisabled: false,
                announce_view: {
                    announce_id: '',
                    subject: '',
                    content: '',
                    date_created: '',
                    acknowledge: '',
                    user_id: '',
                    distro_category_id: '',
                    attachments: [],
                    effective_date: '',
                    creator: '',
                },
            };
        },
        created(){
            this.announce_view.announce_id = this.announcementProps.announce_id;
            this.announce_view.subject = this.announcementProps.subject;
            this.announce_view.creator = this.announcementProps.creator;
            this.announce_view.date_created = this.announcementProps.date_created;
            this.announce_view.content = this.announcementProps.content;
            this.announce_view.user_id = this.announcementProps.user_id;
            this.announce_view.distro_category_id = this.announcementProps.distro_category_id;
            this.announce_view.effective_date = this.announcementProps.effective_date;
            this.announce_view.creator = this.announcementProps.creator;

            if (this.announcementProps.attachments) {
                this.announce_view.attachments = this.announcementProps.attachments;
            }
            if (this.announcementProps.acknowledged_at) {
                this.isChecked = true;
                this.isDisabled = true;
                this.announce_view.acknowledge  = this.announcementProps.acknowledged_at
            }
        },
        methods: {
            eventChecked: function () {
                this.$constants.Default_API.get("/announcement/update/acknowledge/"
                + this.announce_view.announce_id, {
                    params: {
                        distro_category_id: this.announce_view.distro_category_id,
                        user_id: this.announce_view.user_id,
                    }
                }).then(response => {
                    this.$bus.$emit('init_modal', false);
                    this.announce_view = response.data;
                    this.isChecked = true;
                    this.$success('Successfully Acknowledged!');
                    this.$bus.$emit('updateList');
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        },
    }
</script>
