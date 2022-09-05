<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">

            <div v-if="rule.announcement.child_rules.compose_mail.is_enable"
                class="pull-right" style="margin-top: -55px;">
                <button type="button" @click.prevent="createMail()"
                class="btn btn-info">
                    <i class="fa fa-plus"/> Compose Mail
                </button>
            </div>

            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                    <button class='btn btn-warning btn-xs' @click="viewRow(props.row)">
                        <i class="fa fa-eye"/> {{ labels.viewBtn }}
                    </button>
                </template>
                <template slot="creator" slot-scope="props">
                    <span v-if="props.row.announce.sender" >
                        {{ props.row.announce.sender.first_name }}
                        {{ props.row.announce.sender.last_name }}
                    </span>
                </template>
                <template slot="date_created" slot-scope="props">
                    <span v-if="props.row.announce.created_at" >
                        {{ props.row.announce.created_at | formatDateCompleteMonth }}
                    </span>
                </template>
                <template slot="effective_date" slot-scope="props">
                    <span v-if="props.row.announce.effective_date" >
                        {{ props.row.announce.effective_date | formatDateCompleteMonth }}
                    </span>
                </template>
            </v-client-table>

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <compose-mail v-if="openAction == 'create'" :announcementProps="announce_data" slot="body" />
                <view-mail v-if="openAction == 'view'" :announcementProps="announce_data" slot="body" />
            </modal>
    </the-ibox>
    </div>
</template>
<script>
    import {mapGetters} from 'vuex'
    import composeMail from '@/modules/announcement/composeMail.vue'
    import viewMail from '@/modules/announcement/viewMail.vue'

    export default {
        components: {
            composeMail,
            viewMail
        },
        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Compose Mail',
                            view: '',
                            viewBtn: 'View',
                        }
                    }
                },

            },
        data: function () {
            return {
                rule: this.$ep.rule,
                isLoading: false,
                columns: [
                    'actions',
                    'announce.subject',
                    'creator',
                    'date_created',
                    'effective_date',
                    'acknowledged_at'
                ],
                options: {
                    headings: {
                        'announce.subject': 'Subject',
                        'announce.effective_date': 'Effective Date',
                    },
                    sortable: [],
                    filterable: []
                },
                headerName: '',
                openModal: false,
                announce_data: {
                    announce_id: '',
                    subject: '',
                    content: '',
                    attachments: '',
                    distro_category_id: '',
                    date_created: '',
                    acknowledged_at: '',
                    effective_date: '',
                    creator: '',
                },
            }
        },
        mounted() {
            let loadRequired = {
                constants: this.$constants.Default_API,
                setLoading: this.setLoading,
                globalError: this.globalErrorHandling
            }
            this.$store.dispatch('fetchAnnounces', loadRequired)
        },
        computed: {
            ...mapGetters([
                'rows'
            ])
        },
        created() {
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            eventModalClose: function () {
                this.announce_data = {};
            },
            createMail: function () {
                this.openAction = "create";
                this.openModal = true;
                this.headerName = this.labels.add;
                this.announce_data.subject = '';
                this.announce_data.content = '';
                this.announce_data.attachments = '';
                this.announce_data.distro_category_id = '';
                this.announce_data.effective_date = '';
            },
            viewRow: function (props_row) {
                this.openModal = true;
                this.openAction = "view";
                this.headerName = props_row.announce.subject;
                this.announce_data.announce_id = props_row.announcement_id;
                this.announce_data.acknowledged_at = props_row.acknowledged_at;
                this.announce_data.date_created = props_row.announce.created_at;
                this.announce_data.content = props_row.announce.content;
                this.announce_data.user_id = props_row.user_id;
                this.announce_data.distro_category_id = props_row.distro_category_id;
                this.announce_data.attachments = props_row.announce_attached;
                this.announce_data.user_id = props_row.user_id;
                this.announce_data.effective_date = props_row.announce.effective_date;
                this.announce_data.creator = props_row.announce.sender.first_name
                + " " + props_row.announce.sender.last_name;
            },
        }
    }
</script>
